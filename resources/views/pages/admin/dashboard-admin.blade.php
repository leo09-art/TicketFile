<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard Admin - TicketFile</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-linear-to-br from-slate-50 via-indigo-50 to-blue-100 text-gray-800">
    <div class="min-h-screen">
        <header class="border-b border-white/60 bg-white/70 backdrop-blur shadow-sm">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <div>
                    <p class="text-sm font-medium text-indigo-600">TicketFile</p>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">Dashboard Admin</h1>
                </div>

                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700"
                    >
                        Déconnexion
                    </button>
                </form>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-[2fr_1fr]">
                <section class="rounded-2xl bg-white p-8 shadow-xl ring-1 ring-gray-200">
                    <span class="inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">Rôle : Administrateur</span>
                    <h2 class="mt-4 text-3xl font-bold text-gray-900">Bienvenue {{ Auth::user()->name }}</h2>
                    <p class="mt-3 max-w-2xl text-gray-600">
                        Vous avez accès à la gestion complète du système, à la supervision des utilisateurs et au suivi des tickets.
                    </p>

                    <div class="mt-8 grid gap-4 sm:grid-cols-3">
                        <div class="rounded-xl bg-indigo-50 p-4">
                            <p class="text-sm text-indigo-700">Utilisateurs</p>
                            <p class="mt-1 text-2xl font-bold text-indigo-900">—</p>
                        </div>
                        <div class="rounded-xl bg-blue-50 p-4">
                            <p class="text-sm text-blue-700">Tickets</p>
                            <p class="mt-1 text-2xl font-bold text-blue-900">—</p>
                        </div>
                        <div class="rounded-xl bg-emerald-50 p-4">
                            <p class="text-sm text-emerald-700">Services</p>
                            <p class="mt-1 text-2xl font-bold text-emerald-900">—</p>
                        </div>
                    </div>
                </section>

                <aside class="rounded-2xl bg-gray-900 p-6 text-white shadow-xl">
                    <h3 class="text-lg font-semibold">Actions rapides</h3>
                    <ul class="mt-4 space-y-3 text-sm text-gray-300">
                        <li class="rounded-lg bg-white/10 px-4 py-3">Gérer les utilisateurs</li>
                        <li class="rounded-lg bg-white/10 px-4 py-3">Consulter les tickets</li>
                        <li class="rounded-lg bg-white/10 px-4 py-3">Configurer les services</li>
                    </ul>
                </aside>
            </div>
        </main>
    </div>
</body>
</html>

