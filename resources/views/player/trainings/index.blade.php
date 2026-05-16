<x-layouts.app title="Antrenman Geçmişi">
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-white">Antrenman Geçmişi</h1>
                <p class="text-gray-500 text-sm mt-1">Katılım durumun ve antrenman performans puanların.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('player.trainings.index') }}"
              class="bg-surface-700 border border-border rounded-xl p-4 mb-6">
            <div class="flex flex-wrap items-end gap-4">
                <div class="min-w-[160px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Başlangıç</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                           class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                </div>
                <div class="min-w-[160px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Bitiş</label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                           class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                </div>
                <div class="min-w-[180px]">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Katılım</label>
                    <select name="attendance_status"
                            class="w-full px-4 py-2.5 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                        <option value="">Tümü</option>
                        <option value="attended" {{ ($filters['attendance_status'] ?? '') === 'attended' ? 'selected' : '' }}>Katıldı</option>
                        <option value="absent" {{ ($filters['attendance_status'] ?? '') === 'absent' ? 'selected' : '' }}>Gelmedi</option>
                        <option value="excused" {{ ($filters['attendance_status'] ?? '') === 'excused' ? 'selected' : '' }}>İzinli</option>
                    </select>
                </div>
                <button type="submit"
                        class="bg-accent hover:bg-accent-hover text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Filtrele
                </button>
                @if(!empty($filters['date_from']) || !empty($filters['date_to']) || !empty($filters['attendance_status']))
                    <a href="{{ route('player.trainings.index') }}"
                       class="text-gray-400 hover:text-white px-3 py-2.5 text-sm transition-colors">Temizle</a>
                @endif
            </div>
        </form>

        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Toplam</p>
                <p class="text-2xl font-bold text-white">{{ $summary['total_trainings'] }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Katıldı</p>
                <p class="text-2xl font-bold text-accent">{{ $summary['attended'] }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Gelmedi</p>
                <p class="text-2xl font-bold text-red-400">{{ $summary['absent'] }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Katılım</p>
                <p class="text-2xl font-bold text-blue-400">%{{ $summary['attendance_rate'] }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Ort. Puan</p>
                <p class="text-2xl font-bold text-purple-400">{{ $summary['average_score'] ?? '-' }}</p>
            </div>
        </div>

        <div class="bg-surface-700 border border-border rounded-xl overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3">Antrenman</th>
                        <th class="px-4 py-3">Takım</th>
                        <th class="px-4 py-3">Tarih</th>
                        <th class="px-4 py-3 text-center">Katılım</th>
                        <th class="px-4 py-3 text-center">Genel</th>
                        <th class="px-4 py-3 text-center">Hız</th>
                        <th class="px-4 py-3 text-center">Dayanıklılık</th>
                        <th class="px-4 py-3 text-center">Teknik</th>
                        <th class="px-4 py-3 text-center">Disiplin</th>
                        <th class="px-4 py-3">Yorum</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($performances as $performance)
                        <tr class="hover:bg-surface-600 transition-colors">
                            <td class="px-4 py-3 text-white font-medium">{{ $performance->training?->title ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-300">{{ $performance->training?->team?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-300">{{ $performance->training?->training_date?->format('d.m.Y') ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                @switch($performance->attendance_status)
                                    @case('attended')
                                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-accent/15 text-accent">Katıldı</span>
                                        @break
                                    @case('absent')
                                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-red-500/15 text-red-400">Gelmedi</span>
                                        @break
                                    @case('excused')
                                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-yellow-500/15 text-yellow-400">İzinli</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-4 py-3 text-center text-white">{{ $performance->performance_score ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-gray-300">{{ $performance->speed_score ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-gray-300">{{ $performance->endurance_score ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-gray-300">{{ $performance->technique_score ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-gray-300">{{ $performance->discipline_score ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-400 text-xs max-w-[180px] truncate" title="{{ $performance->coach_comment }}">{{ $performance->coach_comment ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-8 text-center text-gray-500">Filtrelere uygun antrenman kaydı bulunmuyor.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($performances->hasPages())
            <div class="mt-6">{{ $performances->withQueryString()->links() }}</div>
        @endif
    </div>
</x-layouts.app>
