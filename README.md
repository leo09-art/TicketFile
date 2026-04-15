# TicketFile

Application Laravel pour la gestion de tickets.

## Prérequis

- PHP 8.3+
- Composer
- Node.js et npm
- SQLite

## Démarrer le projet

1. Cloner le dépôt :

```bash
git clone https://github.com/leo09-art/TicketFile.git
cd TicketFile
```

2. Installer les dépendances PHP :

```bash
composer install
```

3. Copier le fichier d’environnement et générer la clé :

```bash
copy .env.example .env
php artisan key:generate
```

4. Installer les dépendances front :

```bash
npm install
```

5. Préparer la base de données SQLite, lancer les migrations et les seeders :

```bash
php artisan migrate
php artisan db:seed
```

## Identifiants admin par défaut

Après `php artisan db:seed`, un compte administrateur par défaut est disponible avec :

```text
Email : admin@ticketfile.test
Mot de passe : admin12345
```

6. Démarrer l’application :

```bash
php artisan serve
```

7. Dans un deuxième terminal, lancer Vite :

```bash
npm run dev
```

## Commandes utiles

```bash
composer run setup
composer run dev
composer test
```

## Accès

Ouvre ensuite :

```text
http://127.0.0.1:8000
```
