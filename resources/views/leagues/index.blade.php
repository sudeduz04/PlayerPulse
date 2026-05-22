<x-layouts.app title="Fikstür">
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-white">Fikstür</h1>
                <p class="text-gray-500 text-sm mt-1">Lig ve sezon fikstürlerini yönetin.</p>
            </div>
            <a href="{{ route('super_admin.leagues.create') }}" class="bg-accent hover:bg-accent-hover text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">Yeni Lig</a>
        </div>

        @if(session('success'))
            <div class="bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-lg mb-6 text-sm">{{ session('success') }}</div>
        @endif

        <form method="GET" class="bg-surface-700 border border-border rounded-xl p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <input name="search" value="{{ $filters['search'] ?? '' }}" class="px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm" placeholder="Lig ara">
                <input name="season" value="{{ $filters['season'] ?? '' }}" class="px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm" placeholder="Sezon">
                <button class="bg-surface-600 hover:bg-surface-500 text-white px-4 py-2.5 rounded-lg text-sm">Filtrele</button>
            </div>
        </form>

        <div class="bg-surface-700 border border-border rounded-xl overflow-hidden overflow-x-auto">
            <table class="w-full text-sm text-left min-w-[640px]">
                <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3">Lig</th>
                        <th class="px-4 py-3">Sezon</th>
                        <th class="px-4 py-3">Takım</th>
                        <th class="px-4 py-3">Maç</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($leagues as $league)
                        <tr class="hover:bg-surface-600">
                            <td class="px-4 py-3 text-white font-medium">{{ $league->name }}</td>
                            <td class="px-4 py-3 text-gray-300">{{ $league->season }}</td>
                            <td class="px-4 py-3 text-gray-300">{{ $league->teams_count }}</td>
                            <td class="px-4 py-3 text-gray-300">{{ $league->matches_count }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('super_admin.leagues.show', $league->id) }}" class="text-accent hover:text-accent-hover">Detay</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Kayıt yok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $leagues->links() }}</div>
    </div>
</x-layouts.app>
