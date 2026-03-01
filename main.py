from with_lib.chacha20_pycryptodome import Chacha20
from without_lib import chacha20, utils
from with_lib import chacha20_pycryptodome
import os


def separator():
    print("-" * 70)


####################################
#############Paramètres#############
####################################
key = os.urandom(32)  # 32 bytes
nonce = os.urandom(12)  # 12 bytes
message = b"voici un message non crypter !"
counter = 1
####################################
#############Paramètres#############
####################################

print("\nPARAMETRES")
separator()
print("Key     : ", key)
print("Nonce   : ", nonce)
print("Counter : ", counter)
print("Message : ", message)
separator()

# --- Initialisation ---
chacha20 = Chacha20(key)
chacha20_pycrypt = Chacha20(key)

# --- Chiffrement ---
cc2_message_crypter = chacha20.encrypt(message, nonce, counter=counter)
cc2_p_message_crypter = chacha20_pycrypt.encrypt(message, nonce, counter=counter)

print("\nCHIFFREMENT")
separator()
print("Sans lib - ", cc2_message_crypter)
print("Avec lib - ", cc2_p_message_crypter)

print("\nTest égalité chiffrement :",
      cc2_message_crypter == cc2_p_message_crypter)

separator()

# --- Déchiffrement ---
cc2_message_decrypter = chacha20.decrypt(cc2_message_crypter, nonce, counter=counter)
cc2_p_message_decrypter = chacha20_pycrypt.decrypt(cc2_p_message_crypter, nonce, counter=counter)

print("\nDECHIFFREMENT")
separator()
print("Sans lib - ", cc2_message_decrypter)
print("Avec lib - ", cc2_p_message_decrypter)

print("\nTest égalité déchiffrement :",
      cc2_message_decrypter == cc2_p_message_decrypter)

print("Test message original retrouvé :",
      cc2_message_decrypter == message and
      cc2_p_message_decrypter == message)

separator()

