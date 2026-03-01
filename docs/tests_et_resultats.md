# Tests – ChaCha20 (RFC 8439)

---

## Test 1 – Encryption
Source: RFC 8439 – Section 2.2.1
https://datatracker.ietf.org/doc/html/rfc8439#section-2.2.1


Key (hex):
00:01:02:03:04:05:06:07:08:09:0a:0b:0c:0d:0e:0f:10:11:12:13:14:15:16:17:18:19:1a:1b:1c:1d:1e:1f

Nonce (hex):
0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x4a, 0x00, 0x00, 0x00, 0x00

Counter:
1

Plaintext (hex):
```
4c616469657320616e642047656e746c
656d656e206f662074686520636c6173
73206f66202739393a20496620492063
6f756c64206f6666657220796f75206f
6e6c79206f6e652074697020666f7220
746865206675747572652c2073756e73
637265656e20776f756c642062652069
742e
```

Résultat attendu (hex):
```
6e2e359a2568f98041ba0728dd0d6981
e97e7aec1d4360c20a27afccfd9fae0b
f91b65c5524733ab8f593dabcd62b357
1639d624e65152ab8f530c359f0861d8
07ca0dbf500d6a6156a38e088a22b65e
52bc514d16ccf806818ce91ab7793736
5af90bbf74a35be6b40b8eedf2785e42
874d
```

Résultat obtenu (hex):
```
6e2e359a2568f98041ba0728dd0d6981
e97e7aec1d4360c20a27afccfd9fae0b
f91b65c5524733ab8f593dabcd62b357
1639d624e65152ab8f530c359f0861d8
07ca0dbf500d6a6156a38e088a22b65e
52bc514d16ccf806818ce91ab7793736
5af90bbf74a35be6b40b8eedf2785e42
874d
```

Résultat : Succès


## Test 2 – Quarter Round
Source: RFC 8439 – Section 2.2.1
https://datatracker.ietf.org/doc/html/rfc8439#section-2.1.1

Données entrées :
```
879531e0  c5ecf37d  516461b1  c9a62f8a
44c20ef3  3390af7f  d9fc690b  2a5f714c
53372767  b00a5631  974c541a  359e9963
5c971061  3d631689  2098d9d6  91dbd320
```

Indices modifiés: 2, 7, 8, 13 (les 4 blocs ci-dessous)
```
53372767 
3d631689
bdb886dc
cfacafd2
```

Résultat attendu :
```
bdb886dc
cfacafd2
e46bea80
ccc07c79
```

Résultat obtenu :
```
bdb886dc
cfacafd2
e46bea80
ccc07c79
```
Résultat : Succès