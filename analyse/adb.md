# Recueil des besoins

## CHAPITRE 1 : Objectif et portée

L’objectif principal de ce projet est de développer une application web en PHP et MySQL permettant la gestion complète d'un parc informatique. La plateforme offrira une gestion différenciée selon les rôles : consultation pour les visiteurs, gestion opérationnelle pour les techniciens, administration des comptes pour l'administrateur web, et surveillance technique pour l'administrateur système.

Les intervenants sont au nombre de 4 :

- **Visiteur** : Il accède uniquement à la page d’accueil, à une vidéo explicative et à une partie restreinte de l'inventaire.
- **Technicien** : Il gère l'inventaire (ajout, modification, suppression/mise au rebut, import/export CSV).
- **Administrateur web** : Responsable de la gestion des comptes techniciens et des référentiels (OS, constructeurs).
- **Administrateur système** : Surveille les activités via les journaux (logs) pour assurer la sécurité et ne prend pas part à l'activité métier.

La portée du système définit les fonctionnalités clés incluses dans le projet ainsi que les éléments exclus, afin de cadrer les objectifs et les limites de l’application.

**Ce qui entre dans la portée** : Développement d'une interface web responsive, gestion CRUD de l'inventaire (Unités Centrales et Écrans), import/export de données au format CSV, gestion des utilisateurs (techniciens), journalisation des actions (logs), et déploiement sur Raspberry Pi 4.

**Ce qui est en dehors de la portée** : Le piratage des matériels des autres groupes (strictement interdit), la modification des identifiants imposés pour les administrateurs et le système, et l'accès aux fonctionnalités métier pour l'administrateur système.

## CHAPITRE 2 : Terminologie employée / Glossaire

Ce glossaire se concentre sur les termes et concepts spécifiques relatifs à la gestion de parc informatique dans le cadre de la SAE.

1. **Unité Centrale (UC)**
   - Désigne les ordinateurs du parc. Chaque UC est définie par des attributs précis (Nom, N° Série, Constructeur, CPU, RAM, OS, Localisation, etc.).
   - **Objectifs** : Le site doit permettre d'ajouter, modifier et lister ces équipements avec toutes leurs caractéristiques techniques.

2. **Moniteur (Écran)**
   - Désigne les écrans du parc. Ils possèdent des attributs spécifiques (Taille en pouces, Résolution, Connectique) et peuvent être associés ("Attached to") à une UC.
   - **Objectifs** : Gestion de l'inventaire des écrans séparément ou en lien avec les UC.

3. **Mise au rebut**
   - Action de retirer une machine de l'inventaire actif sans la supprimer définitivement de la base de données.
   - **Objectifs** : Une machine "au rebut" est déplacée dans une liste spécifique. Elle peut être "remise en service" par un technicien ou consultée par l'administrateur web.

4. **Fichier CSV (Import/Export)**
   - Format de fichier texte (Comma-Separated Values) utilisé pour échanger des données.
   - **Objectifs** : La plateforme doit permettre l'importation massive de machines via un fichier CSV respectant des en-têtes stricts, ainsi que l'exportation de l'inventaire vers ce format.

5. **Journaux d'activités (Logs)**
   - Fichiers textes enregistrant chronologiquement les événements survenus sur le serveur et l'application.
   - **Objectifs** : Traçabilité totale des actions (connexions, suppressions, erreurs) consultable uniquement par l'Administrateur Système.

## CHAPITRE 3 : Les cas d’utilisation

### (a) Les acteurs principaux et leurs objectifs généraux

1. **Visiteur**
   - **Objectif général** :
     - Comprendre le but de la plateforme via la page d'accueil et la vidéo.
     - Consulter une partie limitée de l'inventaire public.
   - **Actions possibles** :
     - Visualiser la page d'accueil et la vidéo explicative.
     - Consulter la liste publique du matériel.

2. **Technicien**
   - **Objectif général** :
     - Maintenir le parc informatique à jour (Inventaire).
   - **Actions possibles** :
     - Se connecter (compte créé par l'admin web).
     - Ajouter/Modifier/Supprimer du matériel.
     - Importer/Exporter des données en CSV.
     - Gérer la liste de rebut.

3. **Administrateur web**
   - **Objectif général** :
     - Gérer les accès des techniciens et les données de référence.
   - **Actions possibles** :
     - Créer/Supprimer des comptes techniciens.
     - Gérer les listes déroulantes (OS, Constructeurs).
     - Bloquer la liste de rebut pour export.

4. **Administrateur système**
   - **Objectif général** :
     - Surveillance technique et sécuritaire.
   - **Actions possibles** :
     - Consulter les différents journaux d’activités (logs).
     - Vérifier la sécurité des accès (SSH, Web).

### b) Les cas d’utilisation métier (concepts opérationnels)

#### 1. Mettre une machine dans l'inventaire (Formulaire)

| **Nom** | Mettre une machine dans l'inventaire (Formulaire)              |
|---------------------------------|---------------------------------------------------------------|
| **Portée** | Application Web ◼️                                            |
| **Niveau** | 🪁                                                            |
| **Acteur principal** | Technicien                                                    |
| **Scénario nominal** | 1. Le technicien accède au formulaire d'ajout <br> 2. Il saisit les caractéristiques (Serial, RAM, OS...) <br> 3. Il valide l'ajout |
| **Scénario alternatif** | 1. Des données sont manquantes -> Message d'erreur            |
| **Scénario exceptionnel** |                                                               |

#### 2. Importer des machines (CSV)

| **Nom** | Importer des machines (via fichier CSV)                       |
|---------------------------------|---------------------------------------------------------------|
| **Portée** | Application Web ◼️                                            |
| **Niveau** | 🌊                                                            |
| **Acteur principal** | Technicien                                                    |
| **Scénario nominal** | 1. Le technicien sélectionne un fichier CSV local <br> 2. Le système parse le fichier <br> 3. Les machines sont ajoutées à la base de données |
| **Scénario alternatif** | 1. Le format des en-têtes est incorrect -> Refus de l'import  |
| **Scénario exceptionnel** |                                                               |

#### 3. Mettre au rebut

| **Nom** | Mettre une machine au rebut                                   |
|---------------------------------|---------------------------------------------------------------|
| **Portée** | Application Web ◼️                                            |
| **Niveau** | 🐟                                                            |
| **Acteur principal** | Technicien                                                    |
| **Scénario nominal** | 1. Le technicien sélectionne une machine dans l'inventaire <br> 2. Il clique sur "Supprimer/Rebut" <br> 3. La machine change de statut (ne s'affiche plus dans l'inventaire actif) |
| **Scénario alternatif** |                                                               |
| **Scénario exceptionnel** |                                                               |

#### 4. Gérer les comptes techniciens

| **Nom** | Créer / Supprimer un technicien                               |
|---------------------------------|---------------------------------------------------------------|
| **Portée** | Application Web ◼️                                            |
| **Niveau** | 🌊                                                            |
| **Acteur principal** | Administrateur Web                                            |
| **Scénario nominal** | 1. L'admin remplit le formulaire de création (Login/Mdp) <br> 2. Le compte est actif immédiatement |
| **Scénario alternatif** | 1. Suppression d'un technicien existant                       |
| **Scénario exceptionnel** |                                                               |

### c) Les cas d’utilisation système

#### 1. Se connecter (Authentification)

| **Nom** | Se connecter                                                  |
|---------------------------------|---------------------------------------------------------------|
| **Portée** | Site web ◼️                                                   |
| **Niveau** | Sous fonction 🐟                                              |
| **Acteur principal** | Tous (sauf Visiteur)                                          |
| **Scénario nominal** | L’utilisateur entre son login et son mot de passe             |
| **Scénario alternatif** | Identifiants incorrects -> Message d'erreur                   |
| **Scénario exceptionnel** | Compte verrouillé                                             |

#### 2. Consulter les Logs

| **Nom** | Consulter les journaux d'activités                            |
|---------------------------------|---------------------------------------------------------------|
| **Portée** | Site web ◼️                                                   |
| **Niveau** | 🐟                                                            |
| **Acteur principal** | Administrateur Système                                        |
| **Scénario nominal** | 1. Connexion en tant que sysadmin <br> 2. Accès à la page des logs <br> 3. Visualisation des actions (qui a fait quoi et quand) |
| **Scénario alternatif** |                                                               |
| **Scénario exceptionnel** |                                                               |

#### 3. Visualiser la vidéo explicative

| **Nom** | Visualiser la vidéo explicative                               |
|---------------------------------|---------------------------------------------------------------|
| **Portée** | Site web ◼️                                                   |
| **Niveau** | 🌊                                                            |
| **Acteur principal** | Visiteur                                                      |
| **Scénario nominal** | Le visiteur arrive sur la page d'accueil et lance la vidéo    |
| **Scénario alternatif** |                                                               |
| **Scénario exceptionnel** |                                                               |

## CHAPITRE 4 : La technologie employée

Voici les exigences technologiques spécifiques imposées par le sujet :

### Serveur et OS :
- **Raspberry Pi 4 (RPi4)** : Support matériel obligatoire.
- **Système d'exploitation** : Linux (ex: Raspberry Pi OS), installé sur carte SD.
- **Utilisateur Système** : Création obligatoire de l'utilisateur `sae2025` avec le mot de passe `!sae2025!`.

### Serveur Web et BDD :
- **Apache** : Serveur web pour héberger l'application.
- **PHP** : Langage de développement côté serveur.
- **MySQL** : Système de gestion de base de données (ou équivalent SQL) pour stocker l'inventaire et les utilisateurs.

### Connectivité et Sécurité :
- **SSH** : Le RPi4 doit être accessible en SSH depuis le réseau du département.
- **Sécurisation** : Mise en place d'applications pour sécuriser les accès SSH.
- **Réseau** : Configuration IP fournie par l'enseignant.

### Code Source et Versioning :
- **Git (GitLab/GitHub)** : Utilisation obligatoire d'un dépôt partagé avec les professeurs contenant le code, la documentation et les dossiers de conception.

### Interface utilisateur :
- **HTML/CSS** : Réalisation d'une première version statique (maquette HTML) avant le développement dynamique.

## CHAPITRE 5 : Autres exigences

### (a) Processus de développement

#### i) Qui sont les participants du projet ?
Le projet est mené par un groupe de 4 à 5 étudiants. La répartition des tâches couvre l'analyse, la conception, le développement, les tests et la documentation.

#### ii) Quelles valeurs devront être privilégiées ?
Rigueur, sécurité (pas de modification des identifiants imposés), respect des délais (livrables fin novembre) et interopérabilité (fichiers CSV).

#### iii) Quels retours ou quelle visibilité sur le projet les utilisateurs et commanditaires souhaitent-ils ?
- **Enseignants** : Accès au dépôt Git, accès SSH au RPi, et un exposé en anglais.
- **Clients fictifs** : Communication par e-mail recommandée.

#### iv) Que peut-on acheter ? Que doit-on construire ?
- **Matériel** : Fourni (RPi4).
- **Logiciel** : Tout doit être construit (l'application web) ou installé (serveur LAMP) par les étudiants.

#### v) Quelles sont les autres exigences du processus ?
- **Documentation** : Le code doit être documenté. Un document justifiant le choix du logo doit être rédigé.
- **Continuité** : Le projet doit être conçu pour évoluer au Semestre 4.
