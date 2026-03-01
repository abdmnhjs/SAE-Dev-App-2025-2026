import unittest
from chacha20_pycryptodome import Chacha20
from Crypto.Cipher import ChaCha20
from Crypto.Random import get_random_bytes
import os


class TestChaCha20(unittest.TestCase):
    def setUp(self):
        key = get_random_bytes(32)
        cipher = Chacha20(key)

    def test_encrypt(self):
        '''
        Pour ce test, on utilise l'échantillon donné par RFC8439.
        En revanche, leurs données sont en hexadecimal,
        une conversion est donc nécessaire pour pouvoir faire ce test.
        '''

        # 00:01:02:03:04:05:06:07:08:09:0a:0b:0c:0d:0e:0f:10:11:12:13:14:15:16:17:18:19:1a:1b:1c:1d:1e:1f
        key = bytes(range(32))

        # 00:00:00:00:00:00:00:4a:00:00:00:00
        nonce = bytes([0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x4a, 0x00, 0x00, 0x00, 0x00])
        counter = 1
        plaintext_hex = (
            "4c616469657320616e642047656e746c"
            "656d656e206f662074686520636c6173"
            "73206f66202739393a20496620492063"
            "6f756c64206f6666657220796f75206f"
            "6e6c79206f6e652074697020666f7220"
            "746865206675747572652c2073756e73"
            "637265656e20776f756c642062652069"
            "742e"
        )
        plaintext = bytes.fromhex(plaintext_hex)

        cipher = Chacha20(key)
        ciphertext = cipher.encrypt(plaintext, nonce, counter=counter)
        print(ciphertext.hex())

        expected_hex = (
            "6e2e359a2568f98041ba0728dd0d6981"
            "e97e7aec1d4360c20a27afccfd9fae0b"
            "f91b65c5524733ab8f593dabcd62b357"
            "1639d624e65152ab8f530c359f0861d8"
            "07ca0dbf500d6a6156a38e088a22b65e"
            "52bc514d16ccf806818ce91ab7793736"
            "5af90bbf74a35be6b40b8eedf2785e42"
            "874d"
        )

        assert ciphertext.hex() == expected_hex

    def test_decrypt(self):
        '''
        Pour ce test, on utilise aussi l'échantillon donné par RFC8439.
        Leurs données sont en hexadecimal,
        une conversion est donc nécessaire pour pouvoir faire ce test.
        '''

        # 00:01:02:03:04:05:06:07:08:09:0a:0b:0c:0d:0e:0f:10:11:12:13:14:15:16:17:18:19:1a:1b:1c:1d:1e:1f
        key = bytes(range(32))

        # 00:00:00:00:00:00:00:4a:00:00:00:00
        nonce = bytes([0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x4a, 0x00, 0x00, 0x00, 0x00])
        counter = 1
        plaintext_hex = (
            "6e2e359a2568f98041ba0728dd0d6981"
            "e97e7aec1d4360c20a27afccfd9fae0b"
            "f91b65c5524733ab8f593dabcd62b357"
            "1639d624e65152ab8f530c359f0861d8"
            "07ca0dbf500d6a6156a38e088a22b65e"
            "52bc514d16ccf806818ce91ab7793736"
            "5af90bbf74a35be6b40b8eedf2785e42"
            "874d"
        )
        plaintext = bytes.fromhex(plaintext_hex)

        cipher = Chacha20(key)
        ciphertext = cipher.decrypt(plaintext, nonce, counter=counter)
        print(ciphertext.hex())

        expected_hex = (
            "4c616469657320616e642047656e746c"
            "656d656e206f662074686520636c6173"
            "73206f66202739393a20496620492063"
            "6f756c64206f6666657220796f75206f"
            "6e6c79206f6e652074697020666f7220"
            "746865206675747572652c2073756e73"
            "637265656e20776f756c642062652069"
            "742e"
        )

        assert ciphertext.hex() == expected_hex


if __name__ == "__main__":
    unittest.main()
