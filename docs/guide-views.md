# Guide de création des Views - TicketFile

Ce guide indique où et comment créer les vues nécessaires pour le Module 2.

---

## 1. Prérequis

### Problème Vite

Les vues originales utilisent `@vite('resources/css/app.css')` qui nécessite les assets compilés.

**2 solutions :**

**Solution A** - Compiler les assets :
```bash
npm install
npm run build
```

**Solution B** - Remplacer par un fichier CSS externe ou supprimer `@vite()` et utiliser des styles inline.

---

## 2. Structure à créer

```
resources/views/
├── tickets/
│   ├── create.blade.php
│   └── show.blade.php
├── services/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
└── counters/
    ├── index.blade.php
    ├── create.blade.php
    └── edit.blade.php
```

---

## 3. tickets/create.blade.php

**Emplacement :** `resources/views/tickets/create.blade.php`

**Controller :** `TicketController@create`

**Données à afficher :**
- Título : "Prendre un ticket"
- Liste des services actifs (variable `$services`)
- Liste des services via `@foreach($services as $service)`

**Éléments du formulaire :**
- Action : `route('tickets.store')`
- Méthode : POST
- Token CSRF : `@csrf`
- Input radio pour `service_id`
- Bouton submit "Valider"

**Éléments clés Blade :**
- `@foreach($services as $service)` - Parcourir les services
- `$service->id` - ID du service
- `$service->name` - Nom du service
- `$service->description` - Description (optionnel)

---

## 4. tickets/show.blade.php

**Emplacement :** `resources/views/tickets/show.blade.php`

**Controller :** `TicketController@show`

**Données à afficher :**
- Numéro du ticket : `$ticket->ticket_number`
- Nom du service : `$service->name`
- Statut actuel : `$ticket->status`
- Position dans la file : `$position`

**Auto-refresh :**
- Ajouter dans `<head>` : `<meta http-equiv="refresh" content="10">`

**Affichage conditionnel :**
- Si `status === 'en_attente'` : afficher position + temps estimé
- Si `status === 'appele'` : afficher message "C'est votre tour !"
- code couleur CSS selon le statut

**Éléments clés Blade :**
- `@if($ticket->status === 'en_attente')` - Afficher si en attente
- `@switch($ticket->status)` - Afficher selon statut

---

## 5. services/index.blade.php

**Emplacement :** `resources/views/services/index.blade.php`

**Controller :** `ServiceController@index`

**Données à afficher :**
- Liste des services (variable `$services`)
- Colonnes : Nom, Description, Statut (actif/inactif)
- Boutons : Modifier, Supprimer

**Tableau :**
- Header avec colonnes : Nom, Description, Statut, Actions
- Ligne par service avec `@foreach`
- Bouton lien vers modification
- Formulaire DELETE pour suppression

**Éléments clés Blade :**
- `@foreach($services as $service)` - Parcourir
- `$service->name` - Nom
- `$service->description` - Description
- `$service->is_active` - Actif (true/false)
- `route('services.edit', $service)` - Lien modification
- `route('services.destroy', $service)` - Action suppression
- `@method('DELETE')` - Method spoofing

---

## 6. services/create.blade.php

**Emplacement :** `resources/views/services/create.blade.php`

**Controller :** `ServiceController@create`

**Données à afficher :**
- Formulaire de création
- Champs : name, description, is_active

**Éléments du formulaire :**
- Action : `route('services.store')`
- Méthode : POST
- Token CSRF : `@csrf`
- Input text pour `name`
- Textarea pour `description`
- Checkbox pour `is_active`

---

## 7. services/edit.blade.php

**Emplacement :** `resources/views/services/edit.blade.php`

**Controller :** `ServiceController@edit`

**Données à afficher :**
- Formulaire de modification
- Pré-remplir avec les valeurs existantes `$service`

**Éléments du formulaire :**
- Action : `route('services.update', $service)`
- Méthode : POST + `@method('PUT')`
- Inputs pré-remplis avec `value="{{ $service->name }}"`

---

## 8. counters/index.blade.php

**Emplacement :** `resources/views/counters/index.blade.php`

**Controller :** `CounterController@index`

**Données à afficher :**
- Liste des guichets (variable `$counters`)
- Colonnes : Nom, Service, Agent, Actions
- Relations : `$counter->service->name`, `$counter->agent->name`

**Tableau :**
- Header avec colonnes : Nom, Service, Agent, Actions
- Ligne par guichet avec `@foreach`
- Attention aux valeurs nulles : `$counter->service->name ?? '-'`

**Éléments clés Blade :**
- `@foreach($counters as $counter)` - Parcourir
- `$counter->service->name` - Service (relation)
- `$counter->agent->name ?? 'Non assigné'` - Agent avec valeur par défaut

---

## 9. counters/create.blade.php

**Emplacement :** `resources/views/counters/create.blade.php`

**Controller :** `CounterController@create`

**Données à afficher :**
- Formulaire de création
- Liste des services (variable `$services`)
- Liste des agents (variable `$agents`)

**Éléments du formulaire :**
- Input text pour `name`
- Select pour `service_id` (avec `$services`)
- Select pour `agent_user_id` (avec `$agents`)

**Note :** Le controller doit passer `$services` et `$agents` à cette vue.

---

## 10. counters/edit.blade.php

**Emplacement :** `resources/views/counters/edit.blade.php`

**Controller :** `CounterController@edit`

**Données à afficher :**
- Formulaire de modification
- Pré-remplir avec les valeurs existantes `$counter`

**Éléments du formulaire :**
- Action : `route('counters.update', $counter)`
- Méthode : POST + `@method('PUT')`
- Select avec l'option sélectionnée : `{{ $counter->service_id == $service->id ? 'selected' : '' }}`

---

## 11. Résumé des variables par vue

| Vue | Variables obligatoires |
|-----|---------------------|
| tickets/create | `$services` |
| tickets/show | `$ticket`, `$position`, `$service` |
| services/index | `$services` |
| services/create | aucune |
| services/edit | `$service` |
| counters/index | `$counters` |
| counters/create | `$services`, `$agents` |
| counters/edit | `$counter`, `$services`, `$agents` |

---

## 12. Résumé controller -> vue

| Controller | Méthode | Vue |
|------------|---------|-----|
| TicketController | create | tickets/create |
| TicketController | show | tickets/show |
| ServiceController | index | services/index |
| ServiceController | create | services/create |
| ServiceController | edit | services/edit |
| CounterController | index | counters/index |
| CounterController | create | counters/create |
| CounterController | edit | counters/edit |

---

*Guide créé le 14 avril 2026*