# LISTE DES QUESTIONS À POSER (LEVÉE D'AMBIGUÏTÉS)

## 1. Fonctionnalités et Règles de Gestion

### Visibilité du Visiteur
* Le sujet indique que le visiteur peut "consulter une partie de l'inventaire".
    * **Question :** S'agit-il uniquement des Unités Centrales, des Écrans, ou des deux ?
    * **Question :** Quelles informations spécifiques doivent être masquées pour le public (ex: adresses MAC, dates d'achat, localisation précise, numéros de série) ?

### Gestion du Rebut
* L'administrateur web peut "bloquer la liste du rebut pour une future exportation".
    * **Question :** Que signifie concrètement "bloquer" dans ce contexte ? Est-ce rendre la liste en lecture seule ? Empêcher de nouveaux ajouts ? Ou empêcher la remise en service des machines présentes ?
* **Question :** Lorsqu'une machine est mise au rebut, doit-elle disparaître totalement de la liste principale du parc actif, ou simplement changer de statut visuel (code couleur/état) ?

### Importation CSV et Liens
* Le fichier CSV des écrans contient un champ `ATTACHED TO`.
    * **Question :** Ce champ fait-il référence au `SERIAL` de l'Unité Centrale ou à son `NAME` ?
    * **Question :** Comment le système doit-il réagir si l'UC référencée n'existe pas encore dans la base lors de l'importation de l'écran ?
* **Question :** Quel est le séparateur attendu pour les fichiers CSV (point-virgule ou virgule) ?

## 2. Rôles et Administration

### Interface de l'Administrateur Système
* Cet administrateur accède aux journaux via la plateforme web.
    * **Question :** Cette interface doit-elle permettre des fonctionnalités de tri et de recherche (par date, type d'erreur, utilisateur), ou un simple affichage chronologique du fichier brut est-il suffisant ?
    * **Question :** Doit-il avoir accès uniquement aux logs applicatifs (actions utilisateurs) ou également aux logs du serveur (Apache, erreurs système) ?

### Données de référence
* L'administrateur web crée des "informations réutilisables" (OS, Constructeurs).
    * **Question :** Faut-il prévoir cette gestion dynamique pour d'autres champs comme les localisations (`BUILDING`, `ROOM`) ou les types de machines (`TYPE`) ?

## 3. Aspects Techniques (Raspberry Pi & Serveur)

### Journalisation (Logs)
* Il est demandé de créer un "fichier de log inhérent à toutes les actions".
    * **Question :** Privilégiez-vous un stockage des logs en base de données (table SQL) ou dans des fichiers textes plats sur le serveur ?
    * **Question :** Quel niveau de détail est attendu ? Doit-on logger les consultations (lecture simple) ou uniquement les modifications (écritures/suppressions) ?

### Sécurité SSH
* Le sujet demande de "sécuriser les accès SSH".
    * **Question :** Quel est le niveau de sécurité attendu ? (Simple mot de passe fort, interdiction du root login, changement de port, ou authentification par clé SSH obligatoire ?)
    * **Question :** Peut-on installer des outils tiers comme *Fail2Ban* sur le RPi ?

### Hébergement Vidéo
* **Question :** La vidéo explicative de la page d'accueil doit-elle être hébergée physiquement sur la carte SD du Raspberry Pi (ce qui prend de l'espace) ou peut-on intégrer un lien externe (YouTube/PeerTube) ?
