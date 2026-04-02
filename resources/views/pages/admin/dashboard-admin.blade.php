<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard Admin</title>
</head>
<body>

<h1>Dashboard Admin</h1>
<p>Bienvenue {{ Auth::user()->name }} (Admin)</p>

<nav>
    <ul>
{{--        <li><a href="{{ route('dashboard') }}">Accueil</a></li>--}}
        <li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Déconnexion</a></li>
    </ul>
</nav>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<!-- Contenu admin ici -->
<section>
    <h2>Gestion du système</h2>
    <p>Vous avez accès à la gestion complète du système.</p>
</section>

</body>
</html>

