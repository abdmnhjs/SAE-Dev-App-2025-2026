<?php
/**
 * Implémentation de ChaCha20 selon RFC 8439.
 * Chiffrement par flot : clé 256 bits, nonce 96 bits, compteur 64 bits.
 * Usage : ChaCha20::encrypt($key, $nonce, $message[, $counter]) / ChaCha20::decrypt(...)
 */

class ChaCha20
{
    private const SIGMA = [0x61707865, 0x3320646e, 0x79622d32, 0x6b206574]; // "expand 32-byte k"

    /**
     * Masque 32 bits (pour PHP 64-bit)
     */
    private static function u32(int $v): int
    {
        return $v & 0xFFFFFFFF;
    }

    /**
     * Rotation à gauche sur 32 bits
     */
    private static function rotl32(int $v, int $n): int
    {
        $v = self::u32($v);
        $n = $n & 31;
        return self::u32(($v << $n) | ($v >> (32 - $n)));
    }

    /**
     * Quarter round (RFC 8439 section 2.1)
     */
    private static function quarterRound(array &$s, int $a, int $b, int $c, int $d): void
    {
        $s[$a] = self::u32($s[$a] + $s[$b]);
        $s[$d] = self::rotl32(self::u32($s[$d] ^ $s[$a]), 16);
        $s[$c] = self::u32($s[$c] + $s[$d]);
        $s[$b] = self::rotl32(self::u32($s[$b] ^ $s[$c]), 12);
        $s[$a] = self::u32($s[$a] + $s[$b]);
        $s[$d] = self::rotl32(self::u32($s[$d] ^ $s[$a]), 8);
        $s[$c] = self::u32($s[$c] + $s[$d]);
        $s[$b] = self::rotl32(self::u32($s[$b] ^ $s[$c]), 7);
    }

    /**
     * Construit l'état initial (16 mots) : constantes, clé 256 bits, compteur 64 bits, nonce 96 bits.
     */
    private static function initialState(string $key, string $nonce, int $counter): array
    {
        if (strlen($key) !== 32 || strlen($nonce) !== 12) {
            throw new InvalidArgumentException('ChaCha20: clé 32 octets, nonce 12 octets.');
        }
        $s = [];
        for ($i = 0; $i < 4; $i++) {
            $s[] = self::SIGMA[$i];
        }
        for ($i = 0; $i < 8; $i++) {
            $s[] = self::load32le($key, $i * 4);
        }
        $s[] = self::u32($counter & 0xFFFFFFFF);
        $s[] = self::u32(($counter >> 32) & 0xFFFFFFFF);
        for ($i = 0; $i < 3; $i++) {
            $s[] = self::load32le($nonce, $i * 4);
        }
        return $s;
    }

    private static function load32le(string $buf, int $off): int
    {
        $b = unpack('V', substr($buf, $off, 4));
        return $b[1] & 0xFFFFFFFF;
    }

    private static function store32le(int $v): string
    {
        return pack('V', self::u32($v));
    }

    /**
     * Génère un bloc de keystream de 64 octets (RFC 8439 section 2.3–2.4).
     */
    private static function block(string $key, string $nonce, int $counter): string
    {
        $s = self::initialState($key, $nonce, $counter);
        $w = $s;

        // 20 rounds : 10 double rounds (column + diagonal)
        for ($i = 0; $i < 10; $i++) {
            // Column rounds
            self::quarterRound($s, 0, 4, 8, 12);
            self::quarterRound($s, 1, 5, 9, 13);
            self::quarterRound($s, 2, 6, 10, 14);
            self::quarterRound($s, 3, 7, 11, 15);
            // Diagonal rounds
            self::quarterRound($s, 0, 5, 10, 15);
            self::quarterRound($s, 1, 6, 11, 12);
            self::quarterRound($s, 2, 7, 8, 13);
            self::quarterRound($s, 3, 4, 9, 14);
        }

        $out = '';
        for ($i = 0; $i < 16; $i++) {
            $out .= self::store32le(self::u32($s[$i] + $w[$i]));
        }
        return $out;
    }

    /**
     * Chiffre ou déchiffre (XOR avec le keystream). Compteur incrémenté par bloc de 64 octets.
     */
    public static function encrypt(string $key, string $nonce, string $message, int $counter = 0): string
    {
        $len = strlen($message);
        $out = '';
        $pos = 0;
        while ($pos < $len) {
            $keystream = self::block($key, $nonce, $counter);
            $chunk = substr($message, $pos, 64);
            $out .= $chunk ^ $keystream;
            $pos += strlen($chunk);
            $counter++;
        }
        return $out;
    }

    /**
     * Déchiffrement = même opération XOR (ChaCha20 symétrique).
     */
    public static function decrypt(string $key, string $nonce, string $ciphertext, int $counter = 0): string
    {
        return self::encrypt($key, $nonce, $ciphertext, $counter);
    }
}
