<x-layouts.app title="Antrenör Kontrol Paneli">
    <div>
        <div class="mb-6">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">TACTICAL COMMAND CENTER</p>
            <h1 class="text-3xl font-bold text-white">Antrenör Kontrol Paneli</h1>
        </div>

        {{-- Placeholder content --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-surface-700 border border-border rounded-xl p-6">
                <p class="text-gray-500 text-sm">Oyuncu Sayısı</p>
                <p class="text-4xl font-bold text-white mt-2">--</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-6">
                <p class="text-gray-500 text-sm">Son Performans Trendi</p>
                <p class="text-lg font-semibold text-white mt-2">Takım Verimliliği</p>
                <div class="h-24 flex items-end gap-1 mt-4">
                    @for($i = 0; $i < 8; $i++)
                        <div class="flex-1 bg-accent/20 rounded-t" style="height: {{ rand(30, 100) }}%"></div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
