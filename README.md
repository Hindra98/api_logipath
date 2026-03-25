# API Logipath

## Description
API Logipath est une application Symfony développée pour gérer les colis et les livraisons. Elle fournit une API RESTful pour créer, lire, mettre à jour et supprimer des informations sur les colis.

## Technologies utilisées
- **Symfony** : Framework PHP pour le développement web
- **Doctrine** : ORM pour la gestion de la base de données
- **SQLite** : Base de données utilisée en développement
- **PHPUnit** : Pour les tests unitaires
- **Asset Mapper** : Pour la gestion des assets front-end
- **Stimulus** : Framework JavaScript pour les interactions front-end

## Versions
- Symfony 7.4
- PHP 8.2+
- Doctrine ORM 3.6

## Prérequis
- PHP 8.2 ou supérieur
- Composer
- Symfony CLI (recommandé)

## Installation

1. Clonez le dépôt :
   ```bash
   git clone <url-du-dépôt>
   cd api_logipath
   ```

2. Installez les dépendances :
   ```bash
   composer install
   ```

3. Configurez l'environnement :
   - Copiez le fichier `.env` et ajustez les variables si nécessaire.
   - La base de données SQLite est configurée par défaut dans `var/data_logipath.db`.

4. Créez la base de données et exécutez les migrations :
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

5. (Optionnel) Chargez des données de test :
   ```bash
   php bin/console doctrine:fixtures:load
   ```

## Utilisation

### Démarrer le serveur de développement
```bash
symfony server:start
```
Ou avec PHP intégré :
```bash
php -S localhost:8000 -t public
```

L'API sera accessible sur `http://localhost:8000`.

### Endpoints principaux
- `GET /packages` : Liste des colis
- `POST /packages` : Créer un nouveau colis
- `GET /packages/{id}` : Détails d'un colis
- `PUT /packages/{id}` : Mettre à jour un colis
- `DELETE /packages/{id}` : Supprimer un colis

### Tests
Exécutez les tests avec PHPUnit :
```bash
./bin/phpunit
```

## Structure du projet
- `src/Controller/` : Contrôleurs de l'API
- `src/Entity/` : Entités Doctrine
- `src/Repository/` : Repositories pour l'accès aux données
- `templates/` : Templates Twig (si utilisés)
- `migrations/` : Migrations Doctrine
- `tests/` : Tests unitaires
- `config/` : Configuration Symfony
- `public/` : Point d'entrée web

## Contribution
1. Forkez le projet
2. Créez une branche pour votre fonctionnalité (`git checkout -b feature/nouvelle-fonctionnalite`)
3. Commitez vos changements (`git commit -am 'Ajout de nouvelle fonctionnalité'`)
4. Poussez vers la branche (`git push origin feature/nouvelle-fonctionnalite`)
5. Ouvrez une Pull Request

## Licence
Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.