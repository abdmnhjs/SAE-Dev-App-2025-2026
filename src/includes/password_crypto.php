<?php
/**
 * Chiffrement des mots de passe avec ChaCha20 (RFC 8439).
 * Format stocké : chacha20:base64(nonce):base64(ciphertext)
 */

require_once __DIR__ . '/config_crypto.php';
require_once __DIR__ . '/ChaCha20.php';

/**
 * Chiffre un mot de passe et retourne une chaîne à stocker en BDD.
 */
function password_encrypt(string $plaintext): string
{
    $nonce = random_bytes(12);
    $key = CHACHA20_KEY;
    $msg = $plaintext;
    $ciphertext = ChaCha20::encrypt($key, $nonce, $msg, 0);
    return 'chacha20:' . base64_encode($nonce) . ':' . base64_encode($ciphertext);
}

/**
 * Vérifie un mot de passe en clair contre une valeur stockée (chiffrée ou legacy en clair).
 */
function password_verify_plain(string $plaintext, string $stored): bool
{
    if (strpos($stored, 'chacha20:') !== 0) {
        return $plaintext === $stored;
    }
    $parts = explode(':', $stored, 3);
    if (count($parts) !== 3) {
        return false;
    }
    $nonce = base64_decode($parts[1], true);
    $ciphertext = base64_decode($parts[2], true);
    if ($nonce === false || strlen($nonce) !== 12 || $ciphertext === false) {
        return false;
    }
    $decrypted = ChaCha20::decrypt(CHACHA20_KEY, $nonce, $ciphertext, 0);
    return hash_equals($decrypted, $plaintext);
}
