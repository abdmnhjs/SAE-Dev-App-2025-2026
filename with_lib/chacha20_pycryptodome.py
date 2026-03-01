import os
from Crypto.Cipher import ChaCha20
from Crypto.Random import get_random_bytes

class Chacha20:
    def __init__(self, key: bytes) -> None:
        """
        Initialise l'objet avec une clé de 256 bits (32 octets).
        """
        if len(key) != 32:
            raise ValueError("La clé doit contenir exactement 32 octets (256 bits).")

        self.key = key

    def encrypt(self, message_clair: bytes, nonce: bytes, counter: int = 0) -> bytes:
        """
        Chiffre un message en utilisant ChaCha20 (PyCryptodome).

        - nonce : 96 bits (12 octets) conformément à la RFC 8439
        - compteur_initial : position de départ dans le flot (bloc de 64 octets)
        """

        if len(nonce) != 12:
            raise ValueError("Le nonce doit contenir exactement 12 octets (96 bits).")

        # Création de l'objet chiffreur ChaCha20
        # PyCryptodome gère automatiquement les rounds internes
        cc2 = ChaCha20.new(
            key=self.key,
            nonce=nonce
        )

        # Positionnement dans le flot de clé :
        # chaque bloc ChaCha20 = 64 octets
        # seek(position) permet de simuler un compteur personnalisé
        position_octet = (counter & 0xFFFFFFFF) * 64
        cc2.seek(position_octet)

        # XOR automatique entre le message et le keystream
        message_chiffre = cc2.encrypt(message_clair)

        return message_chiffre

    def decrypt(self, message_chiffre: bytes, nonce: bytes, counter: int = 0) -> bytes:
        """
        Déchiffre un message.
        ChaCha20 étant un chiffrement par flot,
        le déchiffrement est identique au chiffrement (XOR).
        """
        if len(nonce) != 12:
            raise ValueError("Le nonce doit contenir exactement 12 octets (96 bits).")

        cc2 = ChaCha20.new(
            key=self.key,
            nonce=nonce
        )

        position_octet = (counter & 0xFFFFFFFF) * 64
        cc2.seek(position_octet)

        message_dechiffre = cc2.decrypt(message_chiffre)

        return message_dechiffre


'''key = get_random_bytes(32)
nonce = get_random_bytes(12)

cipher = Chacha20(key)

message = b"voici un message non crypter !"
message_crypter = cipher.encrypt(message, nonce, counter=0)
message_decrypter = cipher.decrypt(message_crypter, nonce, counter=0)

print(key)
print(nonce)
print(message)
print(message_crypter)
print(message_decrypter)'''