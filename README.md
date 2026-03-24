# Parkslot
## Description
Parkslot est une application web de gestion de parkings et de files d’attente, développée en Laravel 10 avec MySQL.
Elle permet aux utilisateurs de réserver des places, et aux administrateurs de gérer les parkings, les files d’attente et l’historique des réservations.

##  Technologies
- **Backend** : PHP 8.x, MySQL
- **Frontend** : HTML5, CSS, JavaScript
- **Framwork**: Laravel 10, Tailwind CSS


## Project Structure
La structure du projet suit une architecture modulaire.
```
Parking/
├── docs
│   ├── Maquette parking
│   ├── MCD
│   └── Plan_URLs.md
├── .gitignore
│ 
└── README.md
```

## Maquette
- https://www.figma.com/design/A9Rt2tfsoxS0vlKECB24gH/AP-PARKING?node-id=0-1&t=m45QhLQ2OwaM6wH3-1
- [Maquette PNG](docs/Maquette%20parking)

## Documentation
- [MCD](docs/MCD/Capture%20d'écran%202025-10-06%20150532.png)
- [Plan URLS](docs/Plan_URLs.md)



## Fonctionnalités

- Gestion des utilisateurs (CRUD pour les admins)
- Gestion des parkings et des places
- Consultation et réservation de places pour les utilisateurs
- File d’attente avec échange de positions par glisser-déposer (drag & drop)
- Historique des réservations
- Interface administrateur et utilisateur distincte
- Sécurisation via authentification Laravel (`auth`)
- Tâches planifiées via le scheduler Laravel

## Installation et lancement

1. Cloner le dépôt et aller dans le dossier du projet:
```
bash
git clone <lien_du_repo>
cd parkslot
```

2. Installer les dépendances PHP et JS:

```
composer install
npm install
npm run dev
```

3. Configurer l’environnement:

```
cp .env.example .env
php artisan key:generate
```


Modifier `.env` avec les informations de base de données


4. Créer les tables en base de données:

```
php artisan migrate
```

5Lancer le serveur local Laravel:

```
php artisan serve
```


## Utilisation

- S’inscrire ou se connecter
- Pour les administrateurs : accéder à /fileattente, /places, /utilisateurs, /historique
- Glisser-déposer dans la file d’attente pour changer l’ordre des positions
- Les utilisateurs peuvent réserver et consulter leurs réservations dans /vosreservations

## Scheduler / Tâches planifiées

1. Tester manuellement le scheduler:

```
php artisan schedule:run
```

2. Voir toutes les tâches planifiées:

```
php artisan schedule:list
```

3. Automatiser avec Cron (Linux):

```
* * * * * cd /chemin/vers/ton/projet && php artisan schedule:run >> /dev/null 2>&1
```

Remplacer /chemin/vers/ton/projet par le chemin réel vers ton projet.
>> /dev/null 2>&1 permet de ne pas remplir les logs avec la sortie de la commande.



## Auteur

- ImOriane
- Roxas004
- Bakback
