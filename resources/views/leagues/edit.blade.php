<x-layouts.app title="Ligi Düzenle">
    <div>
        <div class="mb-6">
            <a href="{{ route('super_admin.leagues.show', $league->id) }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors mb-2 inline-block">&larr; Lige Dön</a>
            <h1 class="text-3xl font-bold text-white">Ligi Düzenle</h1>
        </div>

        @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg mb-6 text-sm">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('super_admin.leagues.update', $league->id) }}" class="bg-surface-700 border border-border rounded-xl p-6">
            @csrf
            @method('PUT')
            @include('leagues.form', ['league' => $league])
            <div class="flex gap-3 mt-6">
                <button class="bg-accent hover:bg-accent-hover text-white px-6 py-2.5 rounded-lg text-sm font-medium">Güncelle</button>
                <a href="{{ route('super_admin.leagues.show', $league->id) }}" class="text-gray-400 hover:text-white px-4 py-2.5 text-sm">İptal</a>
            </div>
        </form>
    </div>
</x-layouts.app>
