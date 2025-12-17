# CAHIER DES CHARGES - SAE 3 & 4 (2025)

## Introduction
Nous sommes une équipe de 4 à 5 étudiants travaillant sur un projet informatique s'étalant sur les semestres 3 et 4. L’objectif de ce projet est de mettre en place une plateforme web de gestion de parc informatique (inventaire) développée en PHP et MySQL (ou équivalent).

Le site comportera :
- Une page d’accueil avec texte et vidéo explicative,
- Un tableau de bord avec les différents modules de gestion,
- Un système de journalisation (logs) pour toutes les actions réalisées.

## Enoncé
La plateforme est conçue pour accueillir quatre types d'utilisateurs avec des droits distincts :

1. **Visiteur** :
   - Accès non authentifié.
   - Consultation de la page d'accueil (vidéo et texte explicatifs).
   - Consultation d'une partie limitée de l'inventaire.

2. **Technicien** :
   - Accès via authentification (compte par défaut : `techi` / `*tech1*`).
   - Consultation et modification du parc informatique (Unités Centrales et Écrans).
   - Ajout de machines via formulaire ou importation de fichier CSV.
   - Suppression de machines (mise au rebut).
   - Consultation de la liste de rebut et réintégration de matériel.
   - Exportation de listes au format CSV.

3. **Administrateur Web** :
   - Identifiants imposés : `adminweb` / `adminweb`.
   - Création et suppression des comptes techniciens.
   - Gestion des informations partagées (noms des OS, constructeurs).
   - Consultation et blocage de la liste de rebut pour exportation.

4. **Administrateur Système** :
   - Identifiants imposés : `sysadmin` / `sysadmin`.
   - N'a pas accès aux fonctionnalités métier (gestion de parc).
   - Accède exclusivement aux journaux d’activités (logs) pour surveiller l'activité et la sécurité de la plateforme.

## Pré-requis Techniques
L'application sera installée sur un serveur Raspberry Pi 4 (RPi4) disponible via le réseau local.

- **Système** : Un compte utilisateur système est obligatoire (Login : `sae2025`, MDP : `!sae2025!`).
- **Services** : Installation d'un serveur Web (Apache) et d'un SGBD (MySQL).
- **Sécurité** : Mise en place d'applications pour sécuriser les accès SSH.
- **Versionning** : Le code et la documentation doivent être hébergés sur un dépôt Git (GitHub ou GitLab) partagé.

## Données et Formats
L'application devra gérer l'importation de données via des fichiers CSV respectant strictement les en-têtes suivants :

- **Unités Centrales (UC)** :
  `NAME, SERIAL, MANUFACTURER, MODEL, TYPE, CPU, RAM_MB, DISK_GB, OS, DOMAIN, LOCATION, BUILDING, ROOM, MACADDR, PURCHASE_DATE, WARRANTY_END`

- **Écrans** :
  `SERIAL, MANUFACTURER, MODEL, SIZE_INCH, RESOLUTION, CONNECTOR, ATTACHED TO`

## Priorités et Livrables
Les priorités de développement et les rendus attendus sont les suivants :

1. **Conception et Maquettage** :
   - Réécriture du cahier des charges et dossiers de conception (disponibles fin novembre).
   - Mise en ligne d'une version statique HTML (navigation et formulaires fictifs).
   - Justification du choix du logo.

2. **Développement** :
   - Développement des fonctionnalités CRUD pour l'inventaire.
   - Mise en place de l'authentification et des rôles.
   - Gestion des fichiers logs pour l'administrateur système.

3. **Maintenance et Documentation** :
   - Documentation complète du code et du projet disponible sur le dépôt Git.
   - Code mis à jour régulièrement.
   - Communication professionnelle (mails) avec les intervenants.
