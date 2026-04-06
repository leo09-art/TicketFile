<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard Utilisateur - TicketFile</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-linear-to-br from-slate-50 via-indigo-50 to-blue-100 text-gray-800">
    <div class="min-h-screen">
        <header class="border-b border-gray-200 bg-white/90 backdrop-blur shadow-sm">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <div>
                    <p class="text-sm font-medium text-indigo-700">TicketFile</p>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">Dashboard Utilisateur</h1>
                </div>

                <x-logout-button id="logout-form">Déconnexion</x-logout-button>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-[2fr_1fr]">
                <section class="rounded-2xl bg-white p-8 shadow-xl ring-1 ring-gray-200">
                    <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">Rôle : Usager</span>
                    <h2 class="mt-4 text-3xl font-bold text-gray-900">Bienvenue {{ Auth::user()->name }}</h2>
                    <p class="mt-3 text-gray-700">
                        Vous accédez à votre espace personnel pour suivre vos demandes et consulter vos informations.
                    </p>

                    <div class="mt-8 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-xl bg-indigo-50 p-4">
                            <p class="text-sm text-indigo-700">Mes demandes</p>
                            <p class="mt-1 text-2xl font-bold text-indigo-900">—</p>
                        </div>
                        <div class="rounded-xl bg-emerald-50 p-4">
                            <p class="text-sm text-emerald-700">Demandes en cours</p>
                            <p class="mt-1 text-2xl font-bold text-emerald-900">—</p>
                        </div>
                    </div>
                </section>

                <aside class="rounded-2xl bg-white p-6 text-slate-900 shadow-xl ring-1 ring-gray-200">
                    <h3 class="text-lg font-semibold">Mon espace</h3>
                    <p class="mt-2 text-sm text-gray-700">
                        Suivez vos tickets et vos échanges avec l’équipe.
                    </p>
                    <div class="mt-5 space-y-3 text-sm text-gray-800">
                        <div class="rounded-lg bg-slate-100 px-4 py-3 transition hover:bg-slate-200">Créer une nouvelle demande</div>
                        <div class="rounded-lg bg-slate-100 px-4 py-3 transition hover:bg-slate-200">Consulter l’historique</div>
                        <div class="rounded-lg bg-slate-100 px-4 py-3 transition hover:bg-slate-200">Modifier mon profil</div>
                    </div>
                </aside>
            </div>
        </main>
    </div>
</body>
</html>

