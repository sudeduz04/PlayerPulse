<x-layouts.app title="Oyuncu Paneli">
    <div>
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-white">Oyuncu Paneli</h1>
            <p class="text-gray-500 text-sm mt-1">Kişisel performans ve gelişim verileriniz.</p>
        </div>

        {{-- Placeholder stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-surface-700 border border-border rounded-xl p-6">
                <p class="text-gray-500 text-sm">Genel Puan</p>
                <p class="text-4xl font-bold text-white mt-2">--</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-6">
                <p class="text-gray-500 text-sm">Antrenman Katılımı</p>
                <p class="text-4xl font-bold text-white mt-2">--%</p>
            </div>
            <div class="bg-surface-700 border border-border rounded-xl p-6">
                <p class="text-gray-500 text-sm">Son Maç Puanı</p>
                <p class="text-4xl font-bold text-white mt-2">--</p>
            </div>
        </div>
    </div>
</x-layouts.app>
