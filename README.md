
# WR506 - Movie API - Maximilien LEMOINE - 2024

### Prérequis

- [Php 8.1](https://www.php.net/downloads)
- [Composer](https://getcomposer.org/download/)
- [Symfony CLI](https://symfony.com/download)
- OpenSSL (pour générer les clés JWT)
- Projet Frontend [WR505](https://github.com/maximilienlemoine/WR505-MovieApp) (optionnel)

### Installation

1. Cloner le projet
2. Installer les dépendances
    ```bash
    composer install
    ```
3. Créer le fichier .env.local et renseigner les variables d'environnement
    ```bash
    cp .env .env.local
    ```
4. Renseigner les variables suivantes :
    ```dotenv
    DATABASE_URL #(url de la base de données)
    APP_URL #(adresse du back)
    FRONT_URL #(adresse du front)
    MAILER_SENDER #(adresse mail d'envoie des mails)
    MAILER_DSN #(url du serveur mail)
    ```
5. Créer la base de données
    ```bash
    php bin/console d:d:c
    php bin/console d:m:m
    ```
6. Créer les fixtures
    ```bash
    php bin/console d:f:l
    ```
7. Générer les clés JWT
    ```bash
    php bin/console lexik:jwt:generate-keypair
    ```
8. Lancer le serveur
    ```bash
    symfony server:start
    ```

La documentation de l'API est disponible à l'adresse suivante : [http://localhost:8000/api/doc](http://localhost:8000/api/docs)
Les identifiants par défaut pour se connecter à l'API sont les suivants :
```
Admin:
    email: user1@example.com
    password: password
User:
    email: user2@example.com [user2-user6]
    password: password
```

### Fonctionnalités

- [x] Fixtures
- [x] Authentification
- [x] Assert
- [x] Recherche
- [x] Upload
- [x] Role de lecture (user) / ecriture (admin)
- [x] Reset password (mail)

