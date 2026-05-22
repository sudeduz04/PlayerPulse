<x-layouts.app title="Fikstür Detayı">
    <div>
        <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
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
        @if(session('fixture_skipped') && count(session('fixture_skipped')) > 0)
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
                <p class="text-gray-500 text-xs mb-4">Başlıklar: <code>week,date,home_team,away_team,location,status</code>. Yükleme arka planda işlenir.</p>
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

        @if($league->fixtureImports->isNotEmpty())
            <div class="bg-surface-700 border border-border rounded-xl overflow-hidden overflow-x-auto mb-6">
                <div class="px-5 py-3 border-b border-border flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-white">Son İçe Aktarımlar</h2>
                    <span class="text-xs text-gray-500">{{ $league->fixtureImports->count() }} kayıt</span>
                </div>
                <table class="w-full text-sm text-left min-w-[640px]">
                    <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3">Dosya / Kaynak</th>
                            <th class="px-4 py-3">Durum</th>
                            <th class="px-4 py-3 text-right">Eklenen</th>
                            <th class="px-4 py-3 text-right">Atlanan</th>
                            <th class="px-4 py-3">Tarih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($league->fixtureImports as $import)
                            <tr data-import-id="{{ $import->id }}" data-import-url="{{ route('super_admin.leagues.imports.status', [$league->id, $import->id]) }}">
                                <td class="px-4 py-3 text-white">
                                    {{ $import->original_filename ?? 'Manuel giriş' }}
                                    <div class="text-[10px] text-gray-500 mt-0.5">{{ $import->source }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="status-label px-2 py-0.5 text-[11px] rounded-full {{ \App\Support\StatusLabels::badgeClasses($import->status) }}">{{ \App\Support\StatusLabels::fixtureImport($import->status) }}</span>
                                    @if($import->error_message)
                                        <div class="text-[10px] text-red-400 mt-1 max-w-xs truncate" title="{{ $import->error_message }}">{{ $import->error_message }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right text-green-400 import-created">{{ $import->created_rows }}</td>
                                <td class="px-4 py-3 text-right text-yellow-300 import-skipped">{{ $import->skipped_rows }}</td>
                                <td class="px-4 py-3 text-gray-300 text-xs">{{ $import->created_at?->format('d.m.Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="bg-surface-700 border border-border rounded-xl overflow-hidden overflow-x-auto">
            <table class="w-full text-sm text-left min-w-[640px]">
                <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3">Hafta</th>
                        <th class="px-4 py-3">Tarih</th>
                        <th class="px-4 py-3">Ev Sahibi</th>
                        <th class="px-4 py-3">Deplasman</th>
                        <th class="px-4 py-3">Skor</th>
                        <th class="px-4 py-3">Durum</th>
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
                            <td class="px-4 py-3 text-gray-300">
                                @if(in_array($match->status, ['finished', 'first_half', 'half_time', 'second_half'], true))
                                    {{ $match->goals_for ?? 0 }} - {{ $match->goals_against ?? 0 }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-[11px] rounded-full {{ \App\Support\StatusLabels::badgeClasses($match->status) }}">{{ \App\Support\StatusLabels::matchStatus($match->status) }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-300">{{ $match->location }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">Henüz fikstür yüklenmedi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($league->fixtureImports->whereIn('status', ['queued', 'running'])->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const statusLabels = {
                    queued: 'Sıraya alındı',
                    running: 'İşleniyor',
                    completed: 'Tamamlandı',
                    failed: 'Başarısız',
                };
                document.querySelectorAll('tr[data-import-id]').forEach((row) => {
                    const url = row.dataset.importUrl;
                    const label = row.querySelector('.status-label');
                    const createdCell = row.querySelector('.import-created');
                    const skippedCell = row.querySelector('.import-skipped');
                    let initial = label?.textContent.trim();
                    if (!url || (initial !== statusLabels.queued && initial !== statusLabels.running)) return;
                    const timer = setInterval(async () => {
                        try {
                            const res = await window.axios.get(url);
                            const data = res.data.data;
                            if (label) label.textContent = data.status_label || statusLabels[data.status] || data.status;
                            if (createdCell) createdCell.textContent = data.created_rows;
                            if (skippedCell) skippedCell.textContent = data.skipped_rows;
                            if (data.status === 'completed' || data.status === 'failed') {
                                clearInterval(timer);
                                setTimeout(() => window.location.reload(), 800);
                            }
                        } catch (e) { /* ignore transient errors */ }
                    }, 3000);
                });
            });
        </script>
    @endif
</x-layouts.app>
