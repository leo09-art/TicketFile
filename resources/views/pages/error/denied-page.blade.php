<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Accès refusé - TicketFile</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-linear-to-br from-red-50 via-white to-orange-100 flex items-center justify-center p-4 text-gray-800">
    <div class="w-full max-w-lg rounded-2xl bg-white p-8 text-center shadow-xl ring-1 ring-red-100">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-3xl">
            ⛔
        </div>

        <h1 class="mt-6 text-3xl font-bold text-gray-900">Accès refusé</h1>
        <p class="mt-3 text-gray-600">
            Vous n'avez pas les permissions nécessaires pour accéder à cette page.
        </p>

        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
            <a href="{{ route('login') }}" class="rounded-lg bg-indigo-600 px-5 py-3 font-semibold text-white transition hover:bg-indigo-700">
                Retour à la connexion
            </a>
            <a href="{{ url('/') }}" class="rounded-lg bg-gray-100 px-5 py-3 font-semibold text-gray-700 transition hover:bg-gray-200">
                Accueil
            </a>
        </div>
    </div>
</body>
</html>


