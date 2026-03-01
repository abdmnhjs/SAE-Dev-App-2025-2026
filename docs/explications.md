# Chacha20

---
## Définition

ChaCha20 est un algorithme de chiffrement symétrique à flot conçu par
Daniel J. Bernstein en 2008.

ChaCha20 est une alternative logicielle performante à AES, et il est aujourd’hui utilisé dans des protocoles modernes comme TLS 1.3.

ChaCha20 est un chiffrement par flot (stream cipher).

---
## Concept

### Le keystream

un chiffrement par flot,chiffre le texte bit par bit (ou octet par octet) au fur et à mesure qu'il est produit ou transmis.
Il repose sur une clé secrète (Key) et Il génère un flot pseudo-aléatoire en appliquant 20 rounds d’une fonction de permutation ARX
qui produit une suite de bits appelée flot de clé (keystream).

Son principe fondamental est simple :
- message chiffré = message clair ⊕ flot de clé
- message clair = message chiffré ⊕ flot de clé

### Formats conforme à RFC 8439

|  Élément | RFC 8439  |
|---|---|
| Clé  | 32 bytes (256 bits) |
| Nonce  | 12 bytes (96 bits) |
| Compteur  |  32 bits (int) |
| Bloc keystream  |  64 bytes |
| Endianness  | little-endian |

### détails

#### Le keystream

Le keystream est le module qui génère un flot de bits pseudo aléatoires.

3 entrées : clé, nonce, compteur

__Clé (key) :__

- Rôle : mot de passe secret utiliser pour chaque message.
- Taille 256 bits (32 bytes).

__Nonce :__

- Rôle : valeur non secrète, unique par message.
- Taille : 96 bits (12 bytes).
- Objectif : empêcher de réutiliser le même keystream avec la même clé.

__Compteur :__

- Rôle : numéro de bloc pour générer des morceaux successifs du keystream.
- Objectif : position dans le message.

### Exemples

Voici l'ordre des elements dans un "round" 

| 0  | 1  | 2  | 3  |
|----|----|----|----|
| 4  | 5  | 6  | 7  |
| 8  | 9  | 10 | 11 |
| 12 | 13 | 14 | 15 |

C'est l'ordre utilisé dans les tests
([tests.py](../without_lib/tests.py)) et
([test_lib](../with_lib/tests_lib.py))