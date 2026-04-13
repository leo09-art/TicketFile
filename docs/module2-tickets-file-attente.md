# Module 2 : Tickets & File d'attente

Guide d'implémentation complet - Sans code

---

## 1. Prérequis et contexte

### 1.1 Objectif du module
Ce module constitue le cœur fonctionnel du projet TicketFile. Il gère :
- La création de tickets par les usagers
- La génération automatique de numéros incrémentaux
- La gestion des statuts de tickets
- La logique de file d'attente FIFO
- Le reset quotidien automatique

### 1.2 Tables concernées
- **tickets** : Stockage des tickets
- **services** : Services disponibles (consultation, paiement, renseignement)

### 1.3 Préconditions
- Module 1 (Authentification) fonctionnel
- Tables `services` et `tickets` créées en base
- Au moins un service actif créé

---

## 2. Structure de la base de données

### 2.1 Migration pour la table `services`

**Champs à ajouter :**
- `name` : Nom du service (string, requis)
- `description` : Description courte (text, nullable)
- `is_active` : Activation du service (boolean, défaut true)

**Commande Laravel :**
```
php artisan make:migration add_fields_to_services_table
```

### 2.2 Migration pour la table `tickets`

**Champs à ajouter :**
- `ticket_number` : Numéro du ticket (integer, unique)
- `service_id` : Lien vers le service (foreignId)
- `user_id` : Usager propriétaire (foreignId, nullable pour accès sans compte)
- `counter_id` : Guichet assigné (foreignId, nullable)
- `status` : Statut du ticket (enum : en_attente, appelé, traité, absent, annulé)
- `called_at` : Date/heure d'appel (timestamp, nullable)
- `treated_at` : Date/heure de traitement (timestamp, nullable)
- `created_at` : Date de création (timestamp)

**Commande Laravel :**
```
php artisan make:migration add_fields_to_tickets_table
```

### 2.3 Exécution des migrations
```
php artisan migrate
```

---

## 3. Modèles et relations Eloquent

### 3.1 Modèle Service

**Relations à définir :**
- `tickets()` : HasMany → Ticket
- `counters()` : HasMany → Counter

**Méthodes utiles :**
- `active()` : Scope pour retourner uniquement les services actifs
- `getActiveServices()` : Retourne tous les services actifs

### 3.2 Modèle Ticket

**Relations à définir :**
- `service()` : BelongsTo → Service
- `user()` : BelongsTo → User
- `counter()` : BelongsTo → Counter

**Attributs accessibles (fillable) :**
- ticket_number, service_id, user_id, status

**Méthodes utiles :**
- `scopeWaiting()` : Tickets en attente
- `scopeCalled()` : Tickets appelés
- `scopeTreated()` : Tickets traités

---

## 4. Contrôleur : TicketController

### 4.1 Création du contrôleur
```
php artisan make:controller TicketController
```

### 4.2 Méthodes à implémenter

#### `index()`
- **Route :** GET /tickets
- **Rôle :** Admin uniquement
- **Fonction :** Liste tous les tickets du jour

#### `create()`
- **Route :** GET /tickets/create
- **Rôle :** Usager (connecté ou non)
- **Fonction :** Affiche le formulaire de création
- **Données à passer :** Liste des services actifs

#### `store(Request $request)`
- **Route :** POST /tickets
- **Rôle :** Usager (connecté ou non)
- **Fonction :** Crée un nouveau ticket
- **Étapes :**
  1. Valider la requête (service_id requis)
  2. Générer le numéro de ticket
  3. Créer le ticket avec statut "en_attente"
  4. Stocker user_id si usager connecté (sinon null)
  5. Rediriger vers la page de suivi

#### `show(Ticket $ticket)`
- **Route :** GET /tickets/{ticket}
- **Rôle :** Propriétaire du ticket ou Admin
- **Fonction :** Affiche la page de suivi

#### `updateStatus(Request $request, Ticket $ticket)`
- **Route :** PATCH /tickets/{ticket}/status
- **Rôle :** Agent (via Policy)
- **Fonction :** Met à jour le statut du ticket

---

## 5. Génération du numéro de ticket

### 5.1 Logique de numérotation

**Principe :**
- Numéro incrémental par jour
- Réinitialisation à 1 chaque jour à minuit
- Format recommandé : Nombre entier (1, 2, 3...)

### 5.2 Algorithme de génération

```
1. Récupérer la date du jour
2. Chercher le dernier ticket créé aujourd'hui
3. Si existe : ticket_number = dernier_numéro + 1
4. Si n'existe pas : ticket_number = 1
5. Sauvegarder le ticket
```

### 5.3 Implémentation suggérée

**Dans le modèle Ticket (méthode statique) :**
- Créer une méthode `generateTicketNumber()` qui encapsule la logique
- Utiliser un lock de transaction pour éviter les doublons en cas de forte concurrence

### 5.4 Reset quotidien

**Option A - Pas de cron (recommandé pour ce projet) :**
- La logique "chercher le dernier ticket du jour" s'occupe automatiquement du reset
- Chaque nouveau jour, aucun ticket trouvé → reprend à 1

**Option B - Cron (optionnel) :**
- Task Scheduler Laravel
- Script quotidien à minuit pour réinitialiser un compteur en base

---

## 6. Gestion des statuts

### 6.1 Liste des statuts

| Statut | Description | Transition possible |
|--------|-------------|---------------------|
| en_attente | Ticket créé, en file | appelé, annulé |
| appelé | Ticket appelé par agent | traité, absent |
| traité | Ticket serviced | - (terminal) |
| absent | Usager absent | - (terminal) |
| annulé | Ticket annulé par usager | - (terminal) |

### 6.2 Mise à jour des statuts

**Appel d'un ticket (agent) :**
- Vérifier qu'un ticket "en_attente" existe pour le guichet
- Mettre à jour : status = "appelé", called_at = now()

**Traitement d'un ticket :**
- Mettre à jour : status = "traité", treated_at = now()

**Marquer absent :**
- Mettre à jour : status = "absent"

**Annulation (usager) :**
- Mettre à jour : status = "annulé"

---

## 7. Logique de file d'attente (FIFO)

### 7.1 Principe First In First Out

Les tickets sont servis dans l'ordre de création :
- Le ticket créé en premier est le premier servi
- Chaque guichet gère sa propre file pour un service donné

### 7.2 Requête pour obtenir le prochain ticket

```
1. Filtrer par service_id
2. Filtrer par counter_id (ou NULL si pas encore assigné)
3. Filtrer par status = "en_attente"
4. Trier par created_at ASC
5. Prendre le premier résultat
```

### 7.3 Calcul de la position dans la file

**Pour un usager consultant sa position :**

```
position = nombre de tickets "en_attente" créés AVANT ce ticket
         + 1 (pour inclure le ticket lui-même)
```

**Requête Eloquent :**
```
Ticket::where('service_id', $serviceId)
      ->where('status', 'en_attente')
      ->where('created_at', '<', $ticket->created_at)
      ->count() + 1
```

### 7.4 Temps estimé d'attente

**Calcul :**
```
temps_moyen_par_ticket = configuration (ex: 5 minutes)
position = position dans la file
temps_estime = position * temps_moyen_par_ticket
```

---

## 8. Routes à définir

### 8.1 Routes web.php

```php
// Tickets - Usager
Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
Route::get('/tickets/{ticket}/suivi', [TicketController::class, 'show'])->name('tickets.show');

// Tickets - Admin
Route::get('/admin/tickets', [TicketController::class, 'index'])->name('admin.tickets.index');
Route::patch('/admin/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('admin.tickets.update');

// Routes avec middleware auth
Route::middleware(['auth'])->group(function () {
    // Routes réservées aux utilisateurs connectés
});
```

### 8.2 Nom des routes

| Route | Nom | Description |
|-------|-----|-------------|
| GET /tickets/create | tickets.create | Formulaire usager |
| POST /tickets | tickets.store | Création ticket |
| GET /tickets/{id}/suivi | tickets.show | Page suivi usager |

---

## 9. Vues (Blade)

### 9.1 Page de création de ticket

**Fichier :** `resources/views/tickets/create.blade.php`

**Éléments à afficher :**
- Titre : "Prendre un ticket"
- Liste des services disponibles (boutons ou select)
- Bouton "Valider" pour soumettre

**UX importante :**
- Afficher seulement les services actifs
- Indiquer le temps d'attente estimé pour chaque service (optionnel)

### 9.2 Page de suivi usager

**Fichier :** `resources/views/tickets/show.blade.php`

**Éléments à afficher :**
- Numéro du ticket
- Service demandé
- Statut actuel (avec code couleur)
- Position dans la file
- Temps estimé d'attente

**Rafraîchissement automatique :**
- Option 1 : Meta refresh (simpler)
  ```html
  <meta http-equiv="refresh" content="10">
  ```
- Option 2 : JavaScript setInterval
  ```javascript
  setInterval(() => location.reload(), 10000);
  ```

**UX - Message "C'est votre tour" :**
- Afficher un message visuel distinct quand status = "appelé"
- Animation ou couleur verte brillante
- Message : "C'est votre tour ! Veuillez vous présenter au guichet."

### 9.3 Tableau de bord agent

**Fichier :** `resources/views/agent/queue.blade.php` (ou intégré au dashboard)

**Éléments à afficher :**
- Liste des tickets en attente pour son guichet
- Ticket actuellement appelé
- Boutons d'action rapides

---

## 10. Tests et validation

### 10.1 Tests fonctionnels à vérifier

| Scénario | Résultat attendu |
|----------|------------------|
| Création ticket usager connecté | Ticket créé, numéro incrémental, redirection suivi |
| Création ticket usager non-connecté | Ticket créé, user_id = null |
| Reset jour nouveau | Premier ticket du jour = 1 |
| Position file = 3 | Affichage "3 personnes devant vous" |
| Statut appelé | Message visuel "C'est votre tour" |

### 10.2 Commandes de test

```
# Créer un ticket
php artisan tinker
>>> App\Models\Ticket::create(['service_id' => 1, 'ticket_number' => 1, 'status' => 'en_attente'])

# Vérifier les tickets du jour
php artisan tinker
>>> App\Models\Ticket::whereDate('created_at', today())->get()

# Vérifier la file d'attente
php artisan tinker
>>> App\Models\Ticket::where('status', 'en_attente')->orderBy('created_at')->get()
```

---

## 11. Points UX à justifier en soutenance

### 11.1 Comment rassurez-vous un usager à distance ?

**Réponse à préparer :**
- Page de suivi avec position claire : "Vous êtes le 3ème dans la file"
- Temps estimé affiché : "Temps d'attente estimé : 15 minutes"
- Rafraîchissement automatique (toutes les 10 secondes)
- Message visuel distinct quand c'est le tour

### 11.2 Que se passe-t-il visuellement quand c'est le tour ?

**Éléments visuels :**
- Changement de couleur (fond vert)
- Message explicite : "C'est votre tour !"
- Numéro de guichet affiché
- Animation possible (pulsation)

### 11.3 Comment l'agent gère un usager absent ?

**Interface agent :**
- Bouton "Absent" en un clic
- Passage automatique au suivant
- Option de rappeler (si traité par erreur)

---

## 12. Checklist d'implémentation

### Phase 1 : Base de données
- [ ] Ajouter champs à table services
- [ ] Créer migration tickets complète
- [ ] Exécuter migrations

### Phase 2 : Modèles
- [ ] Configurer relations Service → Ticket
- [ ] Configurer relations Ticket → User, Service, Counter
- [ ] Ajouter scopes Eloquent

### Phase 3 : Contrôleur
- [ ] Implémenter méthode create()
- [ ] Implémenter méthode store() avec génération numéro
- [ ] Implémenter méthode show() avec calcul position

### Phase 4 : Routes
- [ ] Ajouter routes dans web.php
- [ ] Nommer les routes

### Phase 5 : Vues
- [ ] Créer vue create.blade.php
- [ ] Créer vue show.blade.php avec auto-refresh
- [ ] Implémenter code couleur statuts

### Phase 6 : Tests
- [ ] Tester création ticket
- [ ] Tester génération numéro (plusieurs consécutifs)
- [ ] Tester reset quotidien (simuler date)
- [ ] Tester calcul position file

---

## 13. Fichiers à créer/modifier

| Fichier | Action | Description |
|---------|--------|-------------|
| database/migrations/xxxx_xx_xx_xxxxxx_add_fields_to_services_table.php | Créer | Migration services |
| database/migrations/xxxx_xx_xx_xxxxxx_add_fields_to_tickets_table.php | Créer | Migration tickets |
| app/Models/Service.php | Modifier | Relations et scopes |
| app/Models/Ticket.php | Modifier | Relations et méthodes |
| app/Http/Controllers/TicketController.php | Créer | Logique principale |
| routes/web.php | Modifier | Ajouter routes tickets |
| resources/views/tickets/create.blade.php | Créer | Formulaire usager |
| resources/views/tickets/show.blade.php | Créer | Page de suivi |

---

## Résumé des étapes clés

1. **Préparer la base** : Compléter les migrations services et tickets
2. **Configurer les modèles** : Définir les relations Eloquent
3. **Implémenter le contrôleur** : Logique de création et gestion des statuts
4. **Générer les numéros** : Algorithme incrémental avec reset quotidien
5. **Calculer la position** : Requête pour position dans la file
6. **Créer les vues** : Interface usager avec auto-refresh
7. **Tester** : Valider tous les scénarios fonctionnels

---

*Document créé pour le Module 2 - Tickets & File d'attente*
