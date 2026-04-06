<form {{ $attributes->merge(['method' => 'POST', 'action' => route('logout')]) }}>
    @csrf
    <button
        type="submit"
        class="inline-flex items-center justify-center rounded-lg border border-slate-900 bg-slate-900 px-4 py-2 text-sm font-semibold text-white! shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:scale-[1.02] hover:bg-indigo-700 hover:border-indigo-700 hover:text-white! hover:shadow-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
    >
        {{ $slot->isEmpty() ? 'Deconnexion' : $slot }}
    </button>
</form>


