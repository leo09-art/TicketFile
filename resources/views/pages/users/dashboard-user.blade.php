<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard Utilisateur</title>
</head>
<body>

<h1>Dashboard Utilisateur</h1>
<p>Bienvenue {{ Auth::user()->name }} (Usager)</p>

<nav>
    <ul>
{{--        <li><a href="{{ route('dashboard') }}">Accueil</a></li>--}}
        <li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Déconnexion</a></li>
    </ul>
</nav>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<!-- Contenu usager ici -->
<section>
    <h2>Mon espace</h2>
    <p>Vous accédez à votre espace personnel.</p>
</section>

</body>
</html>

