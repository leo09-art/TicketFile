<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard Agent - TicketFile</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-linear-to-br from-blue-50 to-indigo-100 text-gray-800">
    <div class="mx-auto flex min-h-screen max-w-5xl flex-col px-4 py-6 sm:px-6 lg:px-8">
        <header class="flex items-center justify-between rounded-2xl bg-white/80 px-6 py-4 shadow-lg backdrop-blur">
            <div>
                <p class="text-sm font-medium text-indigo-600">TicketFile</p>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard de l'agent</h1>
            </div>

            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">
                    Déconnexion
                </button>
            </form>
        </header>

        <main class="mt-8 grid flex-1 gap-6 lg:grid-cols-[1.7fr_1fr]">
            <section class="rounded-2xl bg-white p-8 shadow-xl ring-1 ring-gray-200">
                <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Rôle : Agent</span>
                <h2 class="mt-4 text-3xl font-bold text-gray-900">Bonjour {{ Auth::user()->name }}</h2>
                <p class="mt-3 text-gray-600">
                    Vous gérez les demandes, le suivi des tickets et les interactions avec les usagers.
                </p>

                <div class="mt-8 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl bg-indigo-50 p-4">
                        <p class="text-sm text-indigo-700">Tickets en attente</p>
                        <p class="mt-1 text-2xl font-bold text-indigo-900">—</p>
                    </div>
                    <div class="rounded-xl bg-emerald-50 p-4">
                        <p class="text-sm text-emerald-700">Tickets traités</p>
                        <p class="mt-1 text-2xl font-bold text-emerald-900">—</p>
                    </div>
                </div>
            </section>

            <aside class="rounded-2xl bg-gray-900 p-6 text-white shadow-xl">
                <h3 class="text-lg font-semibold">Votre espace</h3>
                <p class="mt-2 text-sm text-gray-300">
                    Consultez et traitez les tickets assignés à votre compte.
                </p>
                <div class="mt-5 space-y-3 text-sm text-gray-200">
                    <div class="rounded-lg bg-white/10 px-4 py-3">Voir les tickets attribués</div>
                    <div class="rounded-lg bg-white/10 px-4 py-3">Répondre aux usagers</div>
                    <div class="rounded-lg bg-white/10 px-4 py-3">Historique des actions</div>
                </div>
            </aside>
        </main>
    </div>
</body>
</html>


