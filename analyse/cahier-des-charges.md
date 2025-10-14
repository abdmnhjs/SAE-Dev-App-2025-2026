# CAHIER DES CHARGES

## Introduction

Nous sommes une équipe de 4 déveleoppeurs travaillant sur une plateforme web qui permet la gestion d'un parc informatique, elle sera principalement développée en php, liée à une base de données sur un serveur MySQL et sera déployée sur un Raspberry Pi, l'application web tournera sur un serveur web Apache.

La plateforme web permettra:
  - La page d’accueil proposera un texte explicatif et une vidéo explicative des fonctionnalités de la plateforme
  - L’utilisateur non inscrit peut consulter une partie de l’inventaire
  - Cas technicien :
  - consulter le parc informatique
  - modifier une information dans la liste du parc informatique
  - mettre une machine dans l’inventaire à partir d’un formulaire
  - mettre une série de machines dans l’inventaire à partir d’un fichier de données (format csv)
  - supprimer une machine de l’inventaire pour la placer dans une liste dite du rebut
  - Les machines sont séparées en deux catégories, moniteurs et unités centrales.
  - exporter une liste au format csv
  - consulter la liste du rebut, changer la statut du matériel si il est remis en service.
  - Cas adminisateur web :
    - créer un technicien dans la base
    - créer une information qui peut être réutilisée par les techniciens tels que :
      - le nom des systèmes d’exploitations
      - le constructeur de la machine
    - supprimer un technicien
    - consulter la liste du rebut.
    - bloquer la liste du rebut pour une future exportation de cette liste.
  - Cas administrateur système :
    - consulter les différents journaux d’activités de la plateforme





