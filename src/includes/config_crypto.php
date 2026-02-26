<?php
/**
 * Clé ChaCha20 pour le chiffrement des mots de passe (256 bits = 32 octets).
 * À générer une fois et à garder secret (ne pas commiter en production).
 * Exemple de génération en PHP : base64_encode(random_bytes(32))
 */
if (!defined('CHACHA20_KEY_PATH')) {
    define('CHACHA20_KEY_PATH', __DIR__ . '/../config/chacha20_key.php');
}
if (file_exists(CHACHA20_KEY_PATH)) {
    require CHACHA20_KEY_PATH;
}
if (!defined('CHACHA20_KEY') || strlen(CHACHA20_KEY) !== 32) {
    // Clé de développement par défaut (32 octets) — à remplacer en production
    define('CHACHA20_KEY', hex2bin('000102030405060708090a0b0c0d0e0f101112131415161718191a1b1c1d1e1f'));
}
