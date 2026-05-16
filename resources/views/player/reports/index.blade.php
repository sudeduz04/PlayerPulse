<x-layouts.app title="Gelişim Raporlarım">
    <div>
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-white">Gelişim Raporlarım</h1>
            <p class="text-gray-500 text-sm mt-1">Antrenör değerlendirmelerini ve gelişim önerilerini takip et.</p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Toplam</p>
                <p class="text-2xl font-bold text-white">{{ $summary['total_reports'] }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Genel</p>
                <p class="text-2xl font-bold text-accent">{{ $summary['average_overall'] ?? '-' }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Teknik</p>
                <p class="text-2xl font-bold text-blue-400">{{ $summary['average_technical'] ?? '-' }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Fiziksel</p>
                <p class="text-2xl font-bold text-purple-400">{{ $summary['average_physical'] ?? '-' }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Taktik</p>
                <p class="text-2xl font-bold text-yellow-400">{{ $summary['average_tactical'] ?? '-' }}</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-4">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Mental</p>
                <p class="text-2xl font-bold text-pink-400">{{ $summary['average_mental'] ?? '-' }}</p>
            </div>
        </div>

        <div class="space-y-4">
            @forelse($reports as $report)
                <div class="bg-surface-700 border border-border rounded-xl p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
                        <div>
                            <p class="text-white font-semibold">{{ $report->report_date?->format('d.m.Y') }}</p>
                            <p class="text-gray-500 text-sm">{{ $report->creator?->name }} {{ $report->creator?->surname }}</p>
                        </div>
                        <div class="grid grid-cols-5 gap-3 text-center">
                            @foreach([
                                'technical_development' => 'Teknik',
                                'physical_development' => 'Fiziksel',
                                'tactical_development' => 'Taktik',
                                'mental_development' => 'Mental',
                                'overall_score' => 'Genel',
                            ] as $field => $label)
                                <div>
                                    <p class="text-gray-500 text-[10px] uppercase">{{ $label }}</p>
                                    <p class="text-white font-bold">{{ $report->$field ?? '-' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Güçlü Yönler</p>
                            <p class="text-gray-300 text-sm whitespace-pre-line">{{ $report->strengths ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Gelişim Alanları</p>
                            <p class="text-gray-300 text-sm whitespace-pre-line">{{ $report->weaknesses ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Öneriler</p>
                            <p class="text-gray-300 text-sm whitespace-pre-line">{{ $report->recommendations ?: '-' }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-surface-700 border border-border rounded-xl p-8 text-center text-gray-500">
                    Henüz gelişim raporu bulunmuyor.
                </div>
            @endforelse
        </div>

        @if($reports->hasPages())
            <div class="mt-6">{{ $reports->withQueryString()->links() }}</div>
        @endif
    </div>
</x-layouts.app>
