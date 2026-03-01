## CONSEIL D'UTILISATION

Pour utiliser chacha20_pycryptodome, il faut au pré-requis installer pycryptodome.

Les deux commandes ci-dessous permettent l'installation de pycryptodome,
ce sont des commandes à exécuter à la source du projet, 1 seule suffit. 

``pip install -r requirements.txt``

``pip install pycryptodome``

## main.py

À la racine, le fichier main.py utilise et compare les deux chacha20.

## Tests

Des fichiers de test sont dans les deux dossiers (without_lib / with_lib)

Ils testent ce qui peut être tester des deux classes en utilisant les échantillons de données de la [RFC 8439](https://datatracker.ietf.org/doc/html/rfc8439#section-1.1).