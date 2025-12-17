# CAHIER DES CHARGES - PROJET WEB (SAE 3 & 4)

## Introduction
[cite_start]Nous sommes une équipe de 4 à 5 étudiants travaillant sur un projet informatique s'étalant sur les semestres 3 et 4[cite: 1, 15]. [cite_start]L’objectif est de mettre en place une plateforme web de gestion de parc informatique développée en PHP et MySQL (ou équivalent)[cite: 22, 24].

Le site comportera :
- [cite_start]Une page d’accueil avec texte et vidéo explicative[cite: 35],
- [cite_start]Un système de gestion d'inventaire (unités centrales et écrans)[cite: 43],
- [cite_start]Un fichier de log enregistrant toutes les actions réalisées[cite: 25].

## Enoncé
[cite_start]La plateforme est conçue pour quatre types d'utilisateurs distincts[cite: 28].

### 1. Le Visiteur
- **Accès :** Utilisateur non inscrit.
- **Fonctionnalités :**
  - [cite_start]Consulter la page d'accueil (vidéo et texte explicatifs)[cite: 35].
  - [cite_start]Consulter une partie de l'inventaire[cite: 37].

### 2. Le Technicien
- **Accès :** Compte créé par l'administrateur web. [cite_start]Un compte par défaut doit exister (login : `techi`, mdp : `*tech1*`)[cite: 65].
- **Fonctionnalités :**
  - [cite_start]Consulter et modifier les informations du parc informatique[cite: 39, 40].
  - [cite_start]Ajouter une machine via un formulaire[cite: 41].
  - [cite_start]Importer des machines via un fichier CSV[cite: 42].
  - [cite_start]Supprimer une machine (mise au rebut)[cite: 42].
  - [cite_start]Consulter la liste de rebut et réintégrer du matériel[cite: 45].
  - [cite_start]Exporter des listes au format CSV[cite: 44].

### 3. L'Administrateur Web
- [cite_start]**Accès :** Identifiants uniques imposés (login : `adminweb`, mdp : `adminweb`)[cite: 57].
- **Fonctionnalités :**
  - [cite_start]Créer et supprimer des comptes techniciens[cite: 47, 51].
  - [cite_start]Créer des informations partagées (noms des OS, constructeurs)[cite: 48].
  - [cite_start]Consulter la liste de rebut[cite: 52].
  - [cite_start]Bloquer la liste de rebut pour exportation[cite: 53].

### 4. L'Administrateur Système
- [cite_start]**Accès :** Identifiants uniques imposés (login : `sysadmin`, mdp : `sysadmin`)[cite: 60].
- **Fonctionnalités :**
  - [cite_start]Ne participe pas à la gestion du parc (pas d'accès aux fonctionnalités métier)[cite: 61].
  - [cite_start]Accède exclusivement aux journaux d'activités (logs) pour la surveillance système[cite: 55, 62].

## Pré-requis Techniques
[cite_start]L'application sera hébergée sur un Raspberry Pi 4 (RPi4) accessible via le réseau du département[cite: 75, 110].

**Configuration du serveur :**
- [cite_start]**Système :** Compte utilisateur système obligatoire (login : `sae2025`, mdp : `!sae2025!`)[cite: 83].
- [cite_start]**Services :** Serveur Web (ex: Apache), SGBD (ex: MySQL)[cite: 79, 80].
- [cite_start]**Sécurité :** Mise en place d'outils pour sécuriser les accès SSH[cite: 81].

**Données (Formats CSV) :**
- [cite_start]**Unités Centrales :** NAME, SERIAL, MANUFACTURER, MODEL, TYPE, CPU, RAM_MB, DISK_GB, OS, DOMAIN, LOCATION, BUILDING, ROOM, MACADDR, PURCHASE_DATE, WARRANTY_END[cite: 69].
- [cite_start]**Écrans :** SERIAL, MANUFACTURER, MODEL, SIZE_INCH, RESOLUTION, CONNECTOR, ATTACHED TO[cite: 73].

## Priorités et Livrables
Les priorités de développement et les rendus attendus sont les suivants :

1.  **Conception et Maquettage :**
    - [cite_start]Réécriture du cahier des charges et réalisation des dossiers de conception (disponibles fin novembre)[cite: 95, 101].
    - [cite_start]Réalisation d'une plateforme statique en HTML (navigation et formulaires fictifs)[cite: 96].
    - [cite_start]Justification du choix du logo[cite: 99].

2.  **Développement et Documentation :**
    - [cite_start]Mise à disposition du code sur un dépôt Git (Github/Gitlab) partagé[cite: 86].
    - [cite_start]Documentation complète du code et du projet présente sur le dépôt[cite: 88, 102].
    - [cite_start]Gestion des logs d'activités pour l'administrateur système[cite: 25].

3.  **Communication :**
    - [cite_start]Exposé du projet en anglais[cite: 100].
    - [cite_start]Communication professionnelle par e-mail avec les intervenants[cite: 91].
