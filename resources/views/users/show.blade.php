<x-layouts.app title="{{ $user->name }} {{ $user->surname }}">
    <div>
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route('super_admin.users.index') }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors mb-2 inline-block">&larr; Kullanıcılara Dön</a>
                <h1 class="text-3xl font-bold text-white">{{ $user->name }} {{ $user->surname }}</h1>
                <p class="text-gray-500 text-sm mt-1">{{ $user->email }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('super_admin.users.edit', $user->id) }}"
                   class="bg-surface-600 hover:bg-surface-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium border border-border transition-colors">
                    Düzenle
                </a>
                @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('super_admin.users.destroy', $user->id) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?')"
                                class="bg-red-500/15 text-red-400 hover:bg-red-500/25 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                            Sil
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="bg-accent/10 border border-accent/30 text-accent px-4 py-3 rounded-lg mb-6 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- User Card --}}
            <div class="bg-surface-700 border border-border rounded-xl p-6 text-center">
                <div class="w-20 h-20 mx-auto rounded-full bg-accent/20 border-2 border-accent/30 flex items-center justify-center text-3xl font-bold text-accent mb-4">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <h2 class="text-xl font-bold text-white">{{ $user->name }} {{ $user->surname }}</h2>
                <p class="text-gray-400 text-sm mt-1">{{ $user->email }}</p>
                <div class="mt-3 flex items-center justify-center gap-2">
                    @switch($user->role)
                        @case('super_admin')
                            <span class="px-3 py-1 text-xs rounded-full bg-yellow-500/15 text-yellow-400">Süper Yönetici</span>
                            @break
                        @case('manager')
                            <span class="px-3 py-1 text-xs rounded-full bg-purple-500/15 text-purple-400">Yönetici</span>
                            @break
                        @case('coach')
                            <span class="px-3 py-1 text-xs rounded-full bg-blue-500/15 text-blue-400">Antrenör</span>
                            @break
                        @case('player')
                            <span class="px-3 py-1 text-xs rounded-full bg-accent/15 text-accent">Oyuncu</span>
                            @break
                    @endswitch
                    @if($user->status)
                        <span class="px-3 py-1 text-xs rounded-full bg-accent/15 text-accent">Aktif</span>
                    @else
                        <span class="px-3 py-1 text-xs rounded-full bg-red-500/15 text-red-400">Pasif</span>
                    @endif
                </div>
            </div>

            {{-- Details --}}
            <div class="lg:col-span-2 bg-surface-700 border border-border rounded-xl p-6">
                <h2 class="text-lg font-semibold text-white mb-4">Kullanıcı Bilgileri</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-5 gap-x-8">
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Ad</p>
                        <p class="text-white">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Soyad</p>
                        <p class="text-white">{{ $user->surname }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">E-posta</p>
                        <p class="text-white">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Telefon</p>
                        <p class="text-white">{{ $user->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Kayıt Tarihi</p>
                        <p class="text-white">{{ $user->created_at?->format('d.m.Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Son Güncelleme</p>
                        <p class="text-white">{{ $user->updated_at?->format('d.m.Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Linked Player (for player role) --}}
        @if($user->isRole('player'))
            <div class="mt-6 bg-surface-700 border border-border rounded-xl p-6">
                <h2 class="text-lg font-semibold text-white mb-4">Bağlı Oyuncu Profili</h2>
                @if($linkedPlayer)
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Oyuncu</p>
                            <a href="{{ route('super_admin.players.show', $linkedPlayer->id) }}" class="text-accent hover:text-accent-hover transition-colors font-medium">
                                {{ $linkedPlayer->first_name }} {{ $linkedPlayer->last_name }}
                            </a>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Takım</p>
                            <p class="text-white">{{ $linkedPlayer->team?->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Pozisyon</p>
                            <p class="text-white">{{ $linkedPlayer->position?->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Forma No</p>
                            <p class="text-white">#{{ $linkedPlayer->jersey_number }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-gray-400 text-sm">Bu kullanıcıya bağlı bir oyuncu profili bulunmuyor.</p>
                @endif
            </div>
        @endif

        {{-- Assigned Teams (for coach/manager) --}}
        @if(($user->isRole('coach') || $user->isRole('manager')) && $assignedTeams->count() > 0)
            <div class="mt-6 bg-surface-700 border border-border rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-border">
                    <h2 class="text-lg font-semibold text-white">Atanmış Takımlar</h2>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-surface-600 text-gray-400 text-xs uppercase">
                        <tr>
                            <th class="px-6 py-2.5 text-left">Takım</th>
                            <th class="px-6 py-2.5 text-center">Oyuncu Sayısı</th>
                            <th class="px-6 py-2.5 text-left">Kategori</th>
                            <th class="px-6 py-2.5 text-left">Sezon</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($assignedTeams as $team)
                            <tr class="hover:bg-surface-600 transition-colors">
                                <td class="px-6 py-3">
                                    <a href="{{ route('super_admin.teams.show', $team->id) }}" class="text-accent hover:text-accent-hover transition-colors font-medium">{{ $team->name }}</a>
                                </td>
                                <td class="px-6 py-3 text-center text-gray-300">{{ $team->players_count }}</td>
                                <td class="px-6 py-3 text-gray-400">{{ $team->age_category }}</td>
                                <td class="px-6 py-3 text-gray-400">{{ $team->season }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts.app>
