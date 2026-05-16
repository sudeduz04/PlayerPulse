<x-layouts.app title="Değerlendirme Detayı">
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route($routePrefix . '.evaluations.index') }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors mb-2 inline-block">&larr; Değerlendirmelere Dön</a>
                <h1 class="text-3xl font-bold text-white">{{ $report->player?->first_name }} {{ $report->player?->last_name }}</h1>
                <p class="text-gray-500 text-sm mt-1">{{ $report->player?->team?->name }} &middot; {{ $report->report_date?->format('d.m.Y') }}</p>
            </div>
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isRole('coach'))
                <form method="POST" action="{{ route($routePrefix . '.evaluations.destroy', $report->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Bu değerlendirmeyi silmek istediğinize emin misiniz?')"
                            class="bg-red-500/15 text-red-400 hover:bg-red-500/25 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                        Sil
                    </button>
                </form>
            @endif
        </div>

        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            @foreach([
                'technical_development' => ['Teknik', 'text-blue-400'],
                'physical_development' => ['Fiziksel', 'text-purple-400'],
                'tactical_development' => ['Taktik', 'text-yellow-400'],
                'mental_development' => ['Mental', 'text-pink-400'],
                'overall_score' => ['Genel', 'text-accent'],
            ] as $field => [$label, $color])
                <div class="bg-surface-700 border border-border rounded-xl p-4">
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">{{ $label }}</p>
                    <p class="text-3xl font-bold {{ $color }}">{{ $report->$field ?? '-' }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-surface-700 border border-border rounded-xl p-6">
                <h2 class="text-lg font-semibold text-white mb-3">Güçlü Yönler</h2>
                <p class="text-gray-300 text-sm whitespace-pre-line">{{ $report->strengths ?: '-' }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-6">
                <h2 class="text-lg font-semibold text-white mb-3">Gelişim Alanları</h2>
                <p class="text-gray-300 text-sm whitespace-pre-line">{{ $report->weaknesses ?: '-' }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-6">
                <h2 class="text-lg font-semibold text-white mb-3">Öneriler</h2>
                <p class="text-gray-300 text-sm whitespace-pre-line">{{ $report->recommendations ?: '-' }}</p>
            </div>
        </div>

        <div class="mt-6 bg-surface-700 border border-border rounded-xl p-6">
            <h2 class="text-lg font-semibold text-white mb-4">Kayıt Bilgileri</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Oyuncu</p>
                    <p class="text-white">{{ $report->player?->first_name }} {{ $report->player?->last_name }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Pozisyon</p>
                    <p class="text-white">{{ $report->player?->position?->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Oluşturan</p>
                    <p class="text-white">{{ $report->creator?->name }} {{ $report->creator?->surname }}</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
