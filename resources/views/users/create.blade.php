<x-layouts.app title="Yeni Kullanıcı">
    <div>
        <div class="mb-6">
            <a href="{{ route('super_admin.users.index') }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors mb-2 inline-block">&larr; Kullanıcılara Dön</a>
            <h1 class="text-3xl font-bold text-white">Yeni Kullanıcı Ekle</h1>
        </div>

        <div class="bg-surface-700 border border-border rounded-xl p-6">
            <form method="POST" action="{{ route('super_admin.users.store') }}">
                @csrf

                <div class="space-y-5">
                    {{-- Name --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-300 mb-1.5">Ad <span class="text-red-400">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm"
                                   placeholder="Kullanıcı adı">
                            @error('name') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="surname" class="block text-sm font-medium text-gray-300 mb-1.5">Soyad <span class="text-red-400">*</span></label>
                            <input type="text" name="surname" id="surname" value="{{ old('surname') }}" required
                                   class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm"
                                   placeholder="Kullanıcı soyadı">
                            @error('surname') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Email & Phone --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-300 mb-1.5">E-posta <span class="text-red-400">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                   class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm"
                                   placeholder="ornek@email.com">
                            @error('email') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-300 mb-1.5">Telefon</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                   class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm"
                                   placeholder="05XX XXX XX XX">
                            @error('phone') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300 mb-1.5">Şifre <span class="text-red-400">*</span></label>
                        <input type="password" name="password" id="password" required
                               class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm"
                               placeholder="En az 6 karakter">
                        @error('password') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    {{-- Role & Status --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-300 mb-1.5">Rol <span class="text-red-400">*</span></label>
                            <select name="role" id="role" required
                                    class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                                <option value="">Seçin...</option>
                                <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>Süper Yönetici</option>
                                <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>Yönetici</option>
                                <option value="coach" {{ old('role') === 'coach' ? 'selected' : '' }}>Antrenör</option>
                                <option value="player" {{ old('role') === 'player' ? 'selected' : '' }}>Oyuncu</option>
                            </select>
                            @error('role') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-300 mb-1.5">Durum <span class="text-red-400">*</span></label>
                            <select name="status" id="status" required
                                    class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                                <option value="1" {{ old('status', '1') === '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Pasif</option>
                            </select>
                            @error('status') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-6 pt-6 border-t border-border">
                    <button type="submit"
                            class="bg-accent hover:bg-accent-hover text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                        Kullanıcı Ekle
                    </button>
                    <a href="{{ route('super_admin.users.index') }}"
                       class="text-gray-400 hover:text-white px-4 py-2.5 text-sm transition-colors">İptal</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
