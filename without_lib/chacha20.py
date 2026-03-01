from .utils import *
import os
class Chacha20:
    def __init__(self, key: bytes) -> None:
        self.key = key

    def init_etat_interne(self, counter: int, nonce: bytes) -> list[int]:
        '''
        [
        c0, c1, c2, c3,
        k0, k1, k2, k3,
        k4, k5, k6, k7,
        counter, n0, n1, n2
        ]
        '''
        if len(nonce) != 12:
            raise ValueError("le nonce doit être de taille 12 bytes (96-bit)")

        # 4 constantes :"expand 32-byte k" (spécification RFC 8439)
        etat_interne = [
            0x61707865, 0x3320646e, 0x79622d32, 0x6b206574,
        ]

        # 8 key words (little-endian)
        for i in range(0, 32, 4):
            etat_interne.append(conversion_octet_to_int(self.key[i:i+4]))

        # 1 mot de 32 bits pour le compteur (forcé en modulo 2^32)
        etat_interne.append(counter & 0xFFFFFFFF)

        # 3 mots de 32 bits issus du nonce (conversion little-endian)
        for i in range(0, 12, 4):
            etat_interne.append(conversion_octet_to_int(nonce[i:i+4]))

        # L’état interne doit contenir exactement 16 mots de 32 bits
        return etat_interne

    def quarter_round(self, s, a, b, c, d):
        s[a] = addition_32bits(s[a], s[b])
        s[d] ^= s[a]
        s[d] = rotation_gauche(s[d], 16)
        s[c] = addition_32bits(s[c], s[d])
        s[b] ^= s[c]
        s[b] = rotation_gauche(s[b], 12)
        s[a] = addition_32bits(s[a], s[b])
        s[d] ^= s[a]
        s[d] = rotation_gauche(s[d], 8)
        s[c] = addition_32bits(s[c], s[d])
        s[b] ^= s[c]
        s[b] = rotation_gauche(s[b], 7)

    def double_round(self, s):
        self.quarter_round(s, 0, 4, 8, 12)
        self.quarter_round(s, 1, 5, 9, 13)
        self.quarter_round(s, 2, 6, 10, 14)
        self.quarter_round(s, 3, 7, 11, 15)
        self.quarter_round(s, 0, 5, 10, 15)
        self.quarter_round(s, 1, 6, 11, 12)
        self.quarter_round(s, 2, 7, 8, 13)
        self.quarter_round(s, 3, 4, 9, 14)

    def block(self, counter: int, nonce: bytes) -> bytes:
        """
        Génère un bloc de keystream de 512 bits (16 mots × 32 bits = 64 octets)
        """

        # Initialisation de l’état ChaCha20 (16 mots 32 bits)
        etat_initial = self.init_etat_interne(counter, nonce)

        # Copie de l’état pour appliquer les 20 rounds
        etat_travail = etat_initial.copy()

        # Application des 20 rounds (10 double_round)
        for _ in range(10):
            self.double_round(etat_travail)

        #etat_travail[i] = etat_travail[i] + etat_initial[i] (mod 2^32)
        for i in range(16):
            etat_travail[i] = addition_32bits(etat_travail[i], etat_initial[i])

        # Conversion des 16 mots 32 bits en 64 octets (little-endian)
        bloc_64_octets = b"".join(
            conversion_int_to_octet(mot_32bits)
            for mot_32bits in etat_travail
        )

        return bloc_64_octets

    def keystream(self, nombre_octets: int, nonce: bytes, counter: int = 0) -> bytes:
        '''
        permet d'utiliser la fonction block répétitivement en fonction de la taille du message à crypter.
        '''
        flot_cle = bytearray()  # Contiendra tous les blocs générés
        compteur_bloc = counter & 0xFFFFFFFF  # Compteur 32 bits

        while len(flot_cle) < nombre_octets:
            # Génère un bloc de 64 octets à partir du compteur courant
            bloc_64_octets = self.block(compteur_bloc, nonce)

            # Ajoute le bloc au flot total
            flot_cle += bloc_64_octets

            # Incrémente le compteur pour le prochain bloc
            compteur_bloc = (compteur_bloc + 1) & 0xFFFFFFFF

        # Tronque pour obtenir exactement le nombre demandé
        return bytes(flot_cle[:nombre_octets])

    def encrypt(self, plaintext: bytes, nonce: bytes, counter: int = 0) -> bytes:
        keystream = self.keystream(len(plaintext), nonce, counter)
        return xor_bytes(plaintext, keystream)

    def decrypt(self, ciphertext: bytes, nonce: bytes, counter: int = 0) -> bytes:
        keystream = self.keystream(len(ciphertext), nonce, counter)
        return xor_bytes(ciphertext, keystream)

'''key = os.urandom(32)      # 32 bytes
nonce = os.urandom(12)    # 12 bytes

cipher = Chacha20(key)

message = b"voici un message non crypter !"
message_crypter = cipher.encrypt(message, nonce, counter=0)
message_decrypter = cipher.decrypt(message_crypter, nonce, counter=0)

print(key)
print(nonce)
print(message)
print(message_crypter)
print(message_decrypter)'''

