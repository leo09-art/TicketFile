# Guide de création des Views - TicketFile

Ce guide permet de créer les vues nécessaires pour le Module 2 (Tickets & File d'attente).

---

## 1. Prérequis

### 1.1 Problème Vite ?

Les vues originales utilisent `@vite()` qui nécessite les assets compilés :
```php
@vite('resources/css/app.css')
```

**2 solutions :**

**Solution A** (recommandée) - Compiler les assets :
```bash
npm install
npm run build
```

**Solution B** - Remplacer par CSS classique :
```php
// Remplacer
@vite('resources/css/app.css')
// Par
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
```

Ou utiliser des styles inline simples (comme dans ce guide).

---

## 2. Vues à créer

### Structure des dossiers :
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

**Page de création de ticket - Sélection du service**

```blade
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prendre un ticket - TicketFile</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        h1 { color: #333; }
        .service-list { list-style: none; padding: 0; }
        .service-item { border: 2px solid #ddd; margin: 10px 0; padding: 20px; border-radius: 8px; cursor: pointer; transition: 0.3s; }
        .service-item:hover { border-color: #007bff; background: #f8f9fa; }
        .service-item input { margin-right: 10px; }
        .service-name { font-size: 18px; font-weight: bold; color: #333; }
        .service-desc { color: #666; margin-top: 5px; }
        .btn { background: #007bff; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; margin-top: 20px; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>Prendre un ticket</h1>
    <p>Sélectionnez un service :</p>
    
    <form action="{{ route('tickets.store') }}" method="POST">
        @csrf
        
        <ul class="service-list">
            @foreach($services as $service)
            <li>
                <label class="service-item">
                    <input type="radio" name="service_id" value="{{ $service->id }}" required>
                    <span class="service-name">{{ $service->name }}</span>
                    @if($service->description)
                    <div class="service-desc">{{ $service->description }}</div>
                    @endif
                </label>
            </li>
            @endforeach
        </ul>
        
        @error('service_id')
        <p style="color: red;">Veuillez sélectionner un service</p>
        @enderror
        
        <button type="submit" class="btn">Valider</button>
    </form>
</body>
</html>
```

**Éléments clés :**
- Liste des services actifs (via `$services` controller)
- `@csrf` - Protection CSRF Laravel
- `route('tickets.store')` - Route de soumission
- `name="service_id"` - Nom du champ

---

## 4. tickets/show.blade.php

**Page de suivi du ticket avec auto-refresh**

```blade
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi ticket #{{ $ticket->ticket_number }} - TicketFile</title>
    <meta http-equiv="refresh" content="10">
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; text-align: center; }
        .ticket-number { font-size: 72px; font-weight: bold; color: #333; margin: 20px 0; }
        .service-name { font-size: 24px; color: #666; }
        .status { font-size: 20px; padding: 10px 20px; border-radius: 4px; display: inline-block; margin: 20px 0; }
        .status.en_attente { background: #fff3cd; color: #856404; }
        .status.appele { background: #d4edda; color: #155724; }
        .status.traite { background: #c3e6cb; color: #155724; }
        .status.absent { background: #f8d7da; color: #721c24; }
        .status.annule { background: #e2e3e5; color: #383d41; }
        .position { font-size: 18px; color: #333; margin: 20px 0; }
        .estimated-time { font-size: 16px; color: #666; }
        .call-message { background: #28a745; color: white; padding: 30px; border-radius: 8px; font-size: 28px; animation: pulse 1s infinite; }
        @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }
    </style>
</head>
<body>
    <p class="service-name">{{ $service->name }}</p>
    <div class="ticket-number">#{{ $ticket->ticket_number }}</div>
    
    <div class="status {{ $ticket->status }}">
        @switch($ticket->status)
            @case('en_attente') En attente @break
            @case('appele') <div class="call-message">C'est votre tour !<br>Présentez-vous au guichet</div> @break
            @case('traite') Ticket traité ✓ @break
            @case('absent') Absent @break
            @case('annule') Annulé @break
        @endswitch
    </div>
    
    @if($ticket->status === 'en_attente')
    <div class="position">
        <p>Vous êtes le <strong>{{ $position }}</strong>{{ $position === 1 ? 'er' : 'ème' }} dans la file</p>
    </div>
    <div class="estimated-time">
        <p>Temps d'attente estimé : environ {{ $position * 5 }} minutes</p>
    </div>
    @endif
    
    <p style="color: #999; font-size: 14px; margin-top: 30px;">Mise à jour automatique toutes les 10 secondes</p>
</body>
</html>
```

**Éléments clés :**
- `<meta http-equiv="refresh" content="10">` - Auto-refresh toutes les 10 sec
- `$ticket->ticket_number` - Numéro du ticket
- `$position` - Position dans la file (calculée par controller)
- `$service->name` - Nom du service
- Code couleur par statut
- Message spécial quand status = "appele"

---

## 5. services/index.blade.php

**Liste des services (Admin)**

```blade
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - TicketFile Admin</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #f8f9fa; }
        .btn { padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background: #007bff; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .status-active { color: green; }
        .status-inactive { color: red; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Services</h1>
        <a href="{{ route('services.create') }}" class="btn btn-primary">Nouveau service</a>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Description</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($services as $service)
            <tr>
                <td>{{ $service->name }}</td>
                <td>{{ $service->description ?? '-' }}</td>
                <td><span class="{{ $service->is_active ? 'status-active' : 'status-inactive' }}">{{ $service->is_active ? 'Actif' : 'Inactif' }}</span></td>
                <td>
                    <a href="{{ route('services.edit', $service) }}" class="btn btn-primary">Modifier</a>
                    <form action="{{ route('services.destroy', $service) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr?')">Supprimer</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    @if($services->isEmpty())
    <p>Aucun service créé.</p>
    @endif
</body>
</html>
```

**Éléments clés :**
- `$services` - Variable passée par ServiceController@index
- `@method('DELETE')` - Protection pour表单 DELETE
- `confirm()` - Confirmation avant suppression

---

## 6. services/create.blade.php

**Création d'un service (Admin)**

```blade
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau service - TicketFile</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-primary { background: #007bff; color: white; }
    </style>
</head>
<body>
    <h1>Nouveau service</h1>
    
    <form action="{{ route('services.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="name">Nom du service *</label>
            <input type="text" name="name" id="name" required>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="4"></textarea>
        </div>
        
        <div class="form-group">
            <label>
                <input type="checkbox" name="is_active" value="1" checked>
                Service actif
            </label>
        </div>
        
        <button type="submit" class="btn btn-primary">Créer</button>
    </form>
</body>
</html>
```

---

## 7. services/edit.blade.php

**Modification d'un service (Admin)**

```blade
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier service - TicketFile</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-primary { background: #007bff; color: white; }
    </style>
</head>
<body>
    <h1>Modifier service</h1>
    
    <form action="{{ route('services.update', $service) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="name">Nom du service *</label>
            <input type="text" name="name" id="name" value="{{ $service->name }}" required>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="4">{{ $service->description }}</textarea>
        </div>
        
        <div class="form-group">
            <label>
                <input type="checkbox" name="is_active" value="1" {{ $service->is_active ? 'checked' : '' }}>
                Service actif
            </label>
        </div>
        
        <button type="submit" class="btn btn-primary">Mettre à jour</button>
    </form>
</body>
</html>
```

---

## 8. counters/index.blade.php

**Liste des guichets (Admin)**

```blade
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guichets - TicketFile Admin</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #f8f9fa; }
        .btn { padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background: #007bff; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Guichets</h1>
        <a href="{{ route('counters.create') }}" class="btn btn-primary">Nouveau guichet</a>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Service</th>
                <th>Agent</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($counters as $counter)
            <tr>
                <td>{{ $counter->name }}</td>
                <td>{{ $counter->service->name ?? '-' }}</td>
                <td>{{ $counter->agent->name ?? 'Non assigné' }}</td>
                <td>
                    <a href="{{ route('counters.edit', $counter) }}" class="btn btn-primary">Modifier</a>
                    <form action="{{ route('counters.destroy', $counter) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr?')">Supprimer</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    @if($counters->isEmpty())
    <p>Aucun guichet créé.</p>
    @endif
</body>
</html>
```

---

## 9. counters/create.blade.php

**Création d'un guichet (Admin)**

```blade
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau guichet - TicketFile</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-primary { background: #007bff; color: white; }
    </style>
</head>
<body>
    <h1>Nouveau guichet</h1>
    
    <form action="{{ route('counters.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="name">Nom du guichet *</label>
            <input type="text" name="name" id="name" required>
        </div>
        
        <div class="form-group">
            <label for="service_id">Service *</label>
            <select name="service_id" id="service_id" required>
                <option value="">Sélectionner un service</option>
                @foreach($services as $service)
                <option value="{{ $service->id }}">{{ $service->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="agent_user_id">Agent (optionnel)</label>
            <select name="agent_user_id" id="agent_user_id">
                <option value="">Sélectionner un agent</option>
                @foreach($agents as $agent)
                <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                @endforeach
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Créer</button>
    </form>
</body>
</html>
```

**Note :** Cette vue nécessite `$services` et `$agents` passés par le controller.

---

## 10. counters/edit.blade.php

**Modification d'un guichet (Admin)**

```blade
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier guichet - TicketFile</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-primary { background: #007bff; color: white; }
    </style>
</head>
<body>
    <h1>Modifier guichet</h1>
    
    <form action="{{ route('counters.update', $counter) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="name">Nom du guichet *</label>
            <input type="text" name="name" id="name" value="{{ $counter->name }}" required>
        </div>
        
        <div class="form-group">
            <label for="service_id">Service *</label>
            <select name="service_id" id="service_id" required>
                @foreach($services as $service)
                <option value="{{ $service->id }}" {{ $counter->service_id == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="agent_user_id">Agent (optionnel)</label>
            <select name="agent_user_id" id="agent_user_id">
                <option value="">Aucun agent</option>
                @foreach($agents as $agent)
                <option value="{{ $agent->id }}" {{ $counter->agent_user_id == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
                @endforeach
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Mettre à jour</button>
    </form>
</body>
</html>
```

---

## 11. Commandes de vérification

```bash
# Créer un service
php artisan tinker
>>> App\Models\Service::create(['name' => 'Paiement', 'description' => 'Paiements et recettes', 'is_active' => true])

# Créer un agent
>>> App\Models\User::create(['name' => 'Agent 1', 'email' => 'agent1@test.com', 'password' => bcrypt('password'), 'role' => 'agent'])

# Créer un guichet
>>> App\Models\Counter::create(['name' => 'Guichet 1', 'service_id' => 1, 'agent_user_id' => 2])
```

---

## 12. Résumé des fichiers à créer

| Fichier | Controller | Méthode |
|---------|-------------|---------|
| `tickets/create.blade.php` | TicketController | create |
| `tickets/show.blade.php` | TicketController | show |
| `services/index.blade.php` | ServiceController | index |
| `services/create.blade.php` | ServiceController | create |
| `services/edit.blade.php` | ServiceController | edit |
| `counters/index.blade.php` | CounterController | index |
| `counters/create.blade.php` | CounterController | create |
| `counters/edit.blade.php` | CounterController | edit |

---

*Guide créé le 14 avril 2026*