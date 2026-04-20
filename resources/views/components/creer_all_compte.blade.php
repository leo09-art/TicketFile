@extends('layouts.app')
@section('title', 'Créer un compte — TicketFile')

@section('page-header')
<div class="flex items-center gap-4">
    <a href="{{ route('admin.users') }}"
       class="h-9 w-9 flex items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 transition text-lg font-bold">
        ←
    </a>
    <div>
        <p class="text-xs font-semibold text-indigo-500 uppercase tracking-widest mb-1">Admin</p>
        <h1 class="text-2xl font-black text-gray-900 dark:text-white">Créer un compte</h1>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-lg">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 p-7">

        @if($errors->any())
        <div class="mb-5 rounded-xl bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 px-4 py-3 text-sm text-red-700 dark:text-red-400">
            <ul class="space-y-1">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Nom</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                    placeholder="Jean Dupont">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                    placeholder="jean@exemple.com">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Mot de passe</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                    placeholder="••••••••">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                    placeholder="••••••••">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Rôle</label>
                <select name="role" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition cursor-pointer">
                    <option value="">-- Sélectionner --</option>
                    <option value="usager" {{ old('role') === 'usager' ? 'selected' : '' }}>Usager</option>
                    <option value="agent"  {{ old('role') === 'agent'  ? 'selected' : '' }}>Agent</option>
                    <option value="admin"  {{ old('role') === 'admin'  ? 'selected' : '' }}>Admin</option>
                </select>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.users') }}"
                   class="flex-1 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-sm font-semibold text-gray-700 dark:text-gray-300 transition text-center">
                    Annuler
                </a>
                <button type="submit"
                    class="flex-1 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition">
                    Créer le compte
                </button>
            </div>
        </form>
    </div>
</div>
@endsection