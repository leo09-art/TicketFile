# TicketFile

Application Laravel pour la gestion de tickets.

## Prérequis

- PHP 8.3+
- Composer
- Node.js et npm
- SQLite

## Démarrer le projet (du clonage à l'execution)

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

5. Preparer la base de donnees SQLite, lancer les migrations et les seeders :

```bash
php artisan migrate --seed
```

## Identifiants admin par defaut

Apres `php artisan migrate --seed`, un compte administrateur par defaut est disponible avec :

```text
Email : admin@ticketfile.test
Mot de passe : admin12345
```

6. Demarrer l'application :

```bash
php artisan
```

`php artisan` sans argument demarre automatiquement le serveur Laravel (`serve`).

7. Optionnel (si vous modifiez les styles/scripts), dans un deuxieme terminal lancer Vite :

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
git checkout master
git pull origin master
git add .
git commit -m "Ajout de mon travail"
git push origin master
