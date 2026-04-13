# Documentation du Projet TicketFile

## 1. Vue d'ensemble

**Type:** Application web Laravel 13 (PHP)  
**Objectif:** Système de gestion de tickets guichet (type Banque/Poste)  
**Base de données:** MySQL

---

## 2. Fonctionnalités implémentées

### 2.1 Authentication

| Fonctionnalité | Description | Statut |
|----------------|-------------|--------|
| Inscription | Création de compte utilisateur | ✅ |
| Connexion | Authentification par email/password | ✅ |
| Déconnexion | Logout avec invalidation session | ✅ |
| Rôles | Système de 3 rôles: admin, agent, usager | ✅ |
| Redirection | Redirect vers dashboard selon rôle | ✅ |

**Route:** `/login`, `/register`, `/logout`

### 2.2 Gestion des utilisateurs

- Modèle `User` avec champs: name, email, password, role
- Rôle par défaut: `usager`
- Table `login` (-users Laravel)

### 2.3 Dashboard par rôle

| Dashboard | Route | Utilisateur |
|-----------|-------|-------------|
| Admin | `/dashboard-admin` | admin |
| Agent | `/dashboard-agent` | agent |
| Usager | `/dashboard-usager` | usager |

---

## 3. Structure des données

### 3.1 Tables créées

```
login
├── id
├── name
├── email
├── password
├── role (default: 'usager')
├── email_verified_at
├── remember_token
└── timestamps
```

### 3.2 Tables à compléter

```
services
├── id
└── timestamps

counters
├── id
└── timestamps

tickets
├── id
└── timestamps
```

---

## 4. Architecture MVC

### 4.1 Modèles (app/Models/)

| Modèle | Fichier | État |
|--------|---------|------|
| User | User.php | ✅ Opérationnel |
| Ticket | Ticket.php | ⚠️ Vide |
| Service | Service.php | ⚠️ Vide |
| Counter | Counter.php | ⚠️ Vide |

### 4.2 Contrôleurs (app/Http/Controllers/)

| Controller | Méthodes | État |
|------------|----------|------|
| AuthController | login, authenticate, register, store, logout | ✅ Opérationnel |
| TicketController | index, create, store, show, edit, update, destroy | ⚠️ À implémenter |
| ServiceController | index, create, store, show, edit, update, destroy | ⚠️ À implémenter |
| CounterController | index, create, store, show, edit, update, destroy | ⚠️ À implémenter |
| UserController | index, create, store, show, edit, update, destroy | ⚠️ À implémenter |

### 4.3 Vues (resources/views/)

| Vue | Description | État |
|-----|-------------|------|
| login/login.blade.php | Page de connexion | ✅ |
| login/register.blade.php | Page d'inscription | ✅ |
| pages/admin/dashboard-admin.blade.php | Dashboard admin | ✅ |
| pages/agent/dashboard-agent.blade.php | Dashboard agent | ✅ |
| pages/users/dashboard-user.blade.php | Dashboard usager | ✅ |
| pages/error/denied-page.blade.php | Page erreur permission | ✅ |
| welcome.blade.php | Page d'accueil | ✅ |

---

## 5. Routes définies (routes/web.php)

```php
// Racine
GET  /                    → welcome

// Auth
GET  /login               → AuthController@login
POST /login               → AuthController@authenticate
GET  /register            → AuthController@register
POST /register            → AuthController@store
POST /logout              → AuthController@logout

// Dashboards
GET  /dashboard-agent     → vue dashboard-agent
GET  /dashboard-usager    → vue dashboard-user
GET  /dashboard-admin     → vue dashboard-admin
```

---

## 6. Configuration (.env)

```env
APP_NAME=TicketFile
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ticketfile
DB_USERNAME=root
DB_PASSWORD=****  # À configurer

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

---

## 7. Résumé des fichiers

```
TicketFile/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php    ✅
│   │   │   ├── TicketController.php ⚠️
│   │   │   ├── ServiceController.php ⚠️
│   │   │   ├── CounterController.php ⚠️
│   │   │   └── UserController.php    ⚠️
│   │   └── Middleware/
│   ├── Models/
│   │   ├── User.php                  ✅
│   │   ├── Ticket.php                ⚠️
│   │   ├── Service.php               ⚠️
│   │   └── Counter.php               ⚠️
│   └── Providers/
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2026_03_30_091130_create_services_table.php   ⚠️
│   │   ├── 2026_03_30_091220_create_counters_table.php    ⚠️
│   │   └── 2026_03_30_091238_create_tickets_table.php      ⚠️
│   └── seeders/
├── routes/
│   ├── web.php
│   └── console.php
├── resources/
│   └── views/
│       ├── login/
│       ├── pages/
│       └── welcome.blade.php
└── .env
```

---

## 8. À implémenter

1. **Compléter les migrations** - Ajouter les champs nécessaires aux tables services, counters, tickets
2. **Implémenter les relations** - User → Tickets, Service → Tickets, etc.
3. **Compléter les contrôleurs** - Logique CRUD pour Ticket, Service, Counter
4. **Ajouter les routes API** - Ressources REST pour les entités
5. **Configurer les relations** - One-to-Many, Many-to-Many selon besoins
