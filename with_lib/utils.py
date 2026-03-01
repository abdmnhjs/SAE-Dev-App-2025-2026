def xor(a, b):
    char = ""
    if (int(a) + int(b)) == 1:
        char = "1"
    else:
        char = "0"
    return char

def xor_bytes(a: bytes, b: bytes) -> bytes:
    return bytes(x ^ y for x, y in zip(a, b))

def rotation_gauche(x: int, n: int) -> int:
    x &= 0xFFFFFFFF
    return ((x << n) | (x >> (32 - n))) & 0xFFFFFFFF

def addition_32bits(a: int, b: int=0) -> int:
    return (a + b) & 0xFFFFFFFF

def conversion_octet_to_int(b4: bytes) -> int:
    return int.from_bytes(b4, byteorder="little")

def conversion_int_to_octet(x: int) -> bytes:
    return x.to_bytes(4, byteorder="little")

