<?php
/**
 * Exemple de fichier de clé ChaCha20 (256 bits = 32 octets).
 * Copier vers chacha20_key.php.
 * Pour générer une clé : en PHP faire base64_encode(random_bytes(32)) puis :
 *   define('CHACHA20_KEY', base64_decode('VOTRE_CLE_BASE64_ICI'));
 * Ne pas commiter chacha20_key.php en production.
 */
define('CHACHA20_KEY', hex2bin('000102030405060708090a0b0c0d0e0f101112131415161718191a1b1c1d1e1f'));
