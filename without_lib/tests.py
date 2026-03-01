import unittest
from chacha20 import Chacha20
import os

class TestChaCha20(unittest.TestCase):
    def setUp(self):
        #instanciation de la classe chacha20
        self.cipher = Chacha20(os.urandom(32))

    def test_quarterround_basique(self):
        a = 0x11111111
        b = 0x01020304
        c = 0x9b8d6f43
        d = 0x01234567

        s = [a, b, c, d]
        self.cipher.quarter_round(s, 0, 1, 2, 3)

        self.assertEqual(s[0], 0xea2a92f4)
        self.assertEqual(s[1], 0xcb1cf8ce)
        self.assertEqual(s[2], 0x4581472e)
        self.assertEqual(s[3], 0x5881c4bb)

    def test_quarterround_rfc_state(self):
        # échantillon de test venant de RFC 8439 - https://datatracker.ietf.org/doc/html/rfc8439#section-2.2.1
        s = [
            0x879531e0, 0xc5ecf37d, 0x516461b1, 0xc9a62f8a,
            0x44c20ef3, 0x3390af7f, 0xd9fc690b, 0x2a5f714c,
            0x53372767, 0xb00a5631, 0x974c541a, 0x359e9963,
            0x5c971061, 0x3d631689, 0x2098d9d6, 0x91dbd320,
        ]

        # cette copie permet de comparer les bytes changer/inchanger
        before = s.copy()

        # RFC: QUARTERROUND(2, 7, 8, 13)
        self.cipher.quarter_round(s, 2, 7, 8, 13)


        self.assertEqual(s[2],  0xbdb886dc)
        self.assertEqual(s[7],  0xcfacafd2)
        self.assertEqual(s[8],  0xe46bea80)
        self.assertEqual(s[13], 0xccc07c79)

        # test que seuls les mots 2,7,8 et 13 n'ont pas changer
        for i in range(16):
            if i not in (2, 7, 8, 13):
                self.assertEqual(s[i], before[i])

    def test_encrypt(self):
        '''
        Pour ce test, on utilise l'échantillon donner par RFC8439.
        En revanche, leurs données sont en hexadecimal,
        une conversion est donc nécessaire pour pouvoir faire ce test.
        '''

        #00:01:02:03:04:05:06:07:08:09:0a:0b:0c:0d:0e:0f:10:11:12:13:14:15:16:17:18:19:1a:1b:1c:1d:1e:1f
        key = bytes(range(32))

        #00:00:00:00:00:00:00:4a:00:00:00:00
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

if __name__ == "__main__":
    unittest.main()