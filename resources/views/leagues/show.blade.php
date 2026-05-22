<x-layouts.app title="Fikstür Detayı">
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route('super_admin.leagues.index') }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors mb-2 inline-block">&larr; Fikstüre Dön</a>
                <h1 class="text-3xl font-bold text-white">{{ $league->name }}</h1>
                <p class="text-gray-500 text-sm mt-1">{{ $league->season }} · {{ $league->teams_count }} takım · {{ $league->matches_count }} maç</p>
            </div>
            <a href="{{ route('super_admin.leagues.edit', $league->id) }}" class="bg-surface-600 hover:bg-surface-500 text-white px-4 py-2.5 rounded-lg text-sm">Düzenle</a>
        </div>

        @if(session('success'))
            <div class="bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-lg mb-6 text-sm">{{ session('success') }}</div>
        @endif
        @if(session('fixture_skipped'))
            <div class="bg-yellow-500/10 border border-yellow-500/30 text-yellow-300 px-4 py-3 rounded-lg mb-6 text-sm">
                Atlanan satır: {{ count(session('fixture_skipped')) }}
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg mb-6 text-sm">{{ $errors->first() }}</div>
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
            <form method="POST" enctype="multipart/form-data" action="{{ route('super_admin.leagues.fixtures.import', $league->id) }}" class="bg-surface-700 border border-border rounded-xl p-6">
                @csrf
                <h2 class="text-lg font-semibold text-white mb-3">Dosyadan Yükle</h2>
                <p class="text-gray-500 text-xs mb-4">Başlıklar: week,date,home_team,away_team,location</p>
                <input type="file" name="fixture_file" required accept=".csv,.xls,.xlsx" class="w-full text-sm text-gray-300">
                <button class="mt-4 bg-accent hover:bg-accent-hover text-white px-4 py-2.5 rounded-lg text-sm">Yükle</button>
            </form>

            <form method="POST" action="{{ route('super_admin.leagues.fixtures.import', $league->id) }}" class="xl:col-span-2 bg-surface-700 border border-border rounded-xl p-6">
                @csrf
                <h2 class="text-lg font-semibold text-white mb-3">Manuel Satır Ekle</h2>
                <div class="space-y-2">
                    @for($i = 0; $i < 5; $i++)
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                            <input name="rows[{{ $i }}][week]" class="px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm" placeholder="Hafta">
                            <input name="rows[{{ $i }}][date]" type="date" class="px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm">
                            <input name="rows[{{ $i }}][home_team]" class="px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm" placeholder="Ev sahibi">
                            <input name="rows[{{ $i }}][away_team]" class="px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm" placeholder="Deplasman">
                            <input name="rows[{{ $i }}][location]" class="px-3 py-2 bg-surface-600 border border-border rounded-lg text-white text-sm" placeholder="Saha">
                        </div>
                    @endfor
                </div>
                <button class="mt-4 bg-surface-600 hover:bg-surface-500 text-white px-4 py-2.5 rounded-lg text-sm">Satırları Kaydet</button>
            </form>
        </div>

        <div class="bg-surface-700 border border-border rounded-xl overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3">Hafta</th>
                        <th class="px-4 py-3">Tarih</th>
                        <th class="px-4 py-3">Ev Sahibi</th>
                        <th class="px-4 py-3">Deplasman</th>
                        <th class="px-4 py-3">Saha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($league->matches->sortBy(['week', 'match_date']) as $match)
                        <tr class="hover:bg-surface-600">
                            <td class="px-4 py-3 text-gray-300">{{ $match->week }}</td>
                            <td class="px-4 py-3 text-gray-300">{{ $match->match_date?->format('d.m.Y') }}</td>
                            <td class="px-4 py-3 text-white">{{ $match->homeTeam?->name ?? $match->team?->name }}</td>
                            <td class="px-4 py-3 text-white">{{ $match->awayTeam?->name ?? $match->opponent_team }}</td>
                            <td class="px-4 py-3 text-gray-300">{{ $match->location }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Henüz fikstür yüklenmedi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
