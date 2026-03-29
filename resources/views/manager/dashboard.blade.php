<x-layouts.app title="Takım Yönetici Paneli">
    <div>
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-white">Takım Yönetici Paneli ve Raporlar</h1>
            <p class="text-gray-500 text-sm mt-1">Kulüp performans ve oyuncu verileri gerçek zamanlı analizleri.</p>
        </div>

        {{-- Placeholder stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-surface-700 border border-border rounded-xl p-6">
                <p class="text-gray-500 text-sm">Aktif Oyuncu Sayısı</p>
                <p class="text-4xl font-bold text-white mt-2">--</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-6">
                <p class="text-gray-500 text-sm">Takım Kondisyonu</p>
                <p class="text-4xl font-bold text-white mt-2">--%</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-6">
                <p class="text-gray-500 text-sm">Performans Trendi</p>
                <p class="text-xl font-semibold text-accent mt-2">--</p>
            </div>
        </div>
    </div>
</x-layouts.app>
