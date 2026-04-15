<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Inscription - TicketFile</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
<div class="w-full max-w-md">
    <div class="bg-white rounded-lg shadow-xl p-8">
        {{-- En-tête du formulaire admin : retour + titre --}}
        <div class="mb-6 flex items-center justify-between gap-4">
            {{-- Flèche de retour : renvoie vers l'URL précédente consultée par l'admin --}}
            <a
                href="{{ url()->previous() }}"
                class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-indigo-50 text-3xl font-bold leading-none text-indigo-700 transition transform hover:scale-110 hover:bg-indigo-600 hover:text-white hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                aria-label="Retour"
                title="Retour"
            >
                ←
            </a>

            {{-- Titre principal du formulaire --}}
            <div class="flex-1 text-center">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">TicketFile</h1>
                <p class="text-center text-gray-600 mb-0">Créer un compte pour quelqu'un</p>
            </div>

            {{-- Espace visuel à droite pour garder le titre centré --}}
            <span class="h-12 w-12"></span>
        </div>

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <p class="text-red-800 font-semibold text-sm mb-2">Erreurs détectées :</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="text-red-700 text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Formulaire de création de compte --}}
        <form method="post" action="{{ route('register.store') }}">
            @csrf

            <div class="mb-5">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                    placeholder="Jean Dupont"
                    required
                >
                @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                    placeholder="votre@email.com"
                    required
                >
                @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Mot de passe</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                    placeholder="••••••••"
                    required
                >
                @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirmer le mot de passe</label>
                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                    placeholder="••••••••"
                    required
                >
                @error('password_confirmation')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Rôle</label>
                <select
                    name="role"
                    id="role"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition bg-white cursor-pointer"
                    required
                >
                    <option value="">-- Sélectionner un rôle --</option>
                    <option value="usager">Usager</option>
                    <option value="agent">Agent</option>
                    <option value="admin">Admin</option>
                </select>
                @error('role')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full mb-5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 transform hover:scale-105"
            >
                S'inscrire
            </button>
            <button
                type="reset"
                class="w-full bg-gray-400 hover:bg-gray-500 text-black font-semibold py-2 px-4 rounded-lg transition duration-200 transform hover:scale-105"
            >
                Annuler
            </button>
        </form>

        <div class="border-t border-gray-200 mt-6 pt-6">
            <p class="text-center text-gray-600 text-sm">
                Vous avez déjà un compte ?
                <a href="{{route("login")}}" class="text-indigo-600 font-semibold hover:text-indigo-700 transition">
                    Se connecter
                </a>
            </p>
        </div>
    </div>
</div>
</body>
</html>

