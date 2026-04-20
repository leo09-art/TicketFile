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
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                        onsubmit="return confirm('Supprimer {{ $user->name }} ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-semibold transition">🗑 Supprimer</button>
                    </form>
                    @else
                    <span class="text-xs text-gray-300 dark:text-gray-600 italic">Vous</span>
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
@endsection
