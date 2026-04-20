<!doctype html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TicketFile')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        (function() {
            const t = localStorage.getItem('theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>
<body class="h-full bg-slate-100 dark:bg-gray-950 text-gray-800 dark:text-gray-100 transition-colors duration-200">

<div class="flex h-full min-h-screen">

    {{-- SIDEBAR --}}
    @auth
    <aside class="hidden lg:flex flex-col w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 shrink-0">

        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100 dark:border-gray-800">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white font-black text-sm">TF</div>
            <span class="font-bold text-gray-900 dark:text-white text-lg">TicketFile</span>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
            @if(Auth::user()->role === 'admin')
                @php $navItems = [
                    ['route'=>'admin.dashboard','icon'=>'📊','label'=>'Dashboard'],
                    ['route'=>'admin.services', 'icon'=>'🏢','label'=>'Services'],
                    ['route'=>'admin.counters', 'icon'=>'🖥️','label'=>'Guichets'],
                    ['route'=>'admin.users',    'icon'=>'👥','label'=>'Utilisateurs'],
                ]; @endphp
            @elseif(Auth::user()->role === 'agent')
                @php $navItems = [
                    ['route'=>'agent.dashboard','icon'=>'🖥️','label'=>'Mon guichet'],
                ]; @endphp
            @else
                @php $navItems = [
                    ['route'=>'usager.dashboard','icon'=>'🏠','label'=>'Accueil'],
                ]; @endphp
            @endif

            @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                {{ request()->routeIs($item['route']) ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' }}">
                <span class="text-base leading-none">{{ $item['icon'] }}</span>
                {{ $item['label'] }}
            </a>
            @endforeach
        </nav>

        <div class="px-4 py-4 border-t border-gray-100 dark:border-gray-800 space-y-3">
            {{-- Toggle dark mode --}}
            <button id="theme-toggle"
                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition text-sm font-medium text-gray-600 dark:text-gray-300">
                <span class="flex items-center gap-2">
                    <span id="theme-icon"></span>
                    <span id="theme-label">Mode sombre</span>
                </span>
                <span class="relative inline-flex h-5 w-9 items-center rounded-full bg-gray-300 dark:bg-indigo-600 transition-colors">
                    <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow transition-transform translate-x-1 dark:translate-x-4"></span>
                </span>
            </button>

            <div class="flex items-center gap-3 px-2">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 font-black text-sm">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400 capitalize">{{ Auth::user()->role }}</p>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-medium text-red-500 hover:bg-red-50 dark:hover:bg-red-950 transition">
                     Déconnexion
                </button>
            </form>
        </div>
    </aside>
    @endauth

    {{-- MAIN --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Topbar mobile --}}
        @auth
        <header class="lg:hidden flex items-center justify-between bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 px-4 py-3">
            <div class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600 text-white font-black text-xs">TF</div>
                <span class="font-bold text-gray-900 dark:text-white">TicketFile</span>
            </div>
            <div class="flex items-center gap-2">
                <button id="theme-toggle-mobile" class="h-8 w-8 flex items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition text-sm">
                    <span id="theme-icon-mobile">🌙</span>
                </button>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs font-medium text-red-500 hover:text-red-700 transition px-2 py-1">Déco.</button>
                </form>
            </div>
        </header>
        @endauth

        @hasSection('page-header')
        <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 px-6 lg:px-8 py-5">
            @yield('page-header')
        </div>
        @endif

        <main class="flex-1 px-4 lg:px-8 py-6">
            @yield('content')
        </main>
    </div>
</div>

<script>
    function applyTheme(dark) {
        dark ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', dark ? 'dark' : 'light');
        updateIcons();
    }
    function updateIcons() {
        const isDark = document.documentElement.classList.contains('dark');
        const icon = document.getElementById('theme-icon');
        const label = document.getElementById('theme-label');
        const iconMobile = document.getElementById('theme-icon-mobile');
        if (icon) icon.textContent = isDark ? '☀️' : '🌙';
        if (label) label.textContent = isDark ? 'Mode clair' : 'Mode sombre';
        if (iconMobile) iconMobile.textContent = isDark ? '☀️' : '🌙';
    }
    document.addEventListener('DOMContentLoaded', function() {
        updateIcons();
        const btn = document.getElementById('theme-toggle');
        const btnM = document.getElementById('theme-toggle-mobile');
        if (btn) btn.addEventListener('click', () => applyTheme(!document.documentElement.classList.contains('dark')));
        if (btnM) btnM.addEventListener('click', () => applyTheme(!document.documentElement.classList.contains('dark')));
    });
</script>

</body>
</html>
