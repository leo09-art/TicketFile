@extends('layouts.app')
@section('title', 'Utilisateurs — TicketFile')

@section('page-header')
<div class="flex items-center justify-between">
    <div>
        <p class="text-xs font-semibold text-indigo-500 uppercase tracking-widest mb-1">Admin</p>
        <h1 class="text-2xl font-black text-gray-900 dark:text-white">Utilisateurs</h1>
    </div>
    <a href="{{ route('admin.accounts.create') }}"
        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
        + Créer un compte
    </a>
</div>
@endsection

@section('content')
@if(session('success'))
<div class="mb-5 flex items-center gap-2 rounded-xl bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
     {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-5 flex items-center gap-2 rounded-xl bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 px-4 py-3 text-sm text-red-700 dark:text-red-400">
     {{ session('error') }}
</div>
@endif

<form method="GET" action="{{ route('admin.users') }}" class="mb-5 bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 p-4 grid gap-3 md:grid-cols-3">
    <div>
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Filtrer par rôle</label>
        <select name="role"
            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Tous les rôles</option>
            <option value="admin" {{ $selectedRole === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="agent" {{ $selectedRole === 'agent' ? 'selected' : '' }}>Agent</option>
            <option value="usager" {{ $selectedRole === 'usager' ? 'selected' : '' }}>Usager</option>
        </select>
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Trier par date de création</label>
        <select name="sort"
            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="created_desc" {{ $sort === 'created_desc' ? 'selected' : '' }}>Plus récents d'abord</option>
            <option value="created_asc" {{ $sort === 'created_asc' ? 'selected' : '' }}>Plus anciens d'abord</option>
        </select>
    </div>
    <div class="flex items-end gap-2">
        <button type="submit" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition">Appliquer</button>
        <a href="{{ route('admin.users') }}" class="px-4 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-sm font-semibold text-gray-700 dark:text-gray-300 transition">Réinitialiser</a>
    </div>
</form>

<div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-800">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Utilisateur</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Email</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Rôle</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Inscrit le</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($users as $user)
            <tr class="hover:bg-slate-50 dark:hover:bg-gray-800/50 transition">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400 font-black text-sm">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $user->name }}</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-xs">{{ $user->email }}</td>
                <td class="px-6 py-4">
                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold
                        @if($user->role === 'admin') bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-400
                        @elseif($user->role === 'agent') bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400
                        @else bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 @endif">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-gray-400 dark:text-gray-500 text-xs">{{ $user->created_at->format('d/m/Y') }}</td>
                <td class="px-6 py-4">
                    @if($user->id !== Auth::id())
                    <div class="flex items-center gap-3">
                        <button type="button"
                            onclick="document.getElementById('modal-edit-user-{{ $user->id }}').classList.remove('hidden')"
                            class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold transition">Modifier</button>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                            onsubmit="return confirm('Supprimer {{ $user->name }} ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-semibold transition">🗑 Supprimer</button>
                        </form>
                    </div>
                    @else
                    <button type="button"
                        onclick="document.getElementById('modal-edit-user-{{ $user->id }}').classList.remove('hidden')"
                        class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold transition">Modifier</button>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-gray-400">Aucun utilisateur.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@foreach($users as $user)
<div id="modal-edit-user-{{ $user->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="w-full max-w-md bg-white dark:bg-gray-900 rounded-3xl shadow-2xl p-7 ring-1 ring-gray-200 dark:ring-gray-700">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-black text-gray-900 dark:text-white">Modifier l'utilisateur</h2>
            <button type="button" onclick="document.getElementById('modal-edit-user-{{ $user->id }}').classList.add('hidden')"
                class="h-8 w-8 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-500 transition text-lg">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Nom <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ $user->name }}" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ $user->email }}" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Rôle <span class="text-red-500">*</span></label>
                <select name="role" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition cursor-pointer">
                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="agent" {{ $user->role === 'agent' ? 'selected' : '' }}>Agent</option>
                    <option value="usager" {{ $user->role === 'usager' ? 'selected' : '' }}>Usager</option>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-edit-user-{{ $user->id }}').classList.add('hidden')"
                    class="flex-1 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-sm font-semibold text-gray-700 dark:text-gray-300 transition">Annuler</button>
                <button type="submit"
                    class="flex-1 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endforeach
@endsection
