<x-layouts.guest title="Giriş Yap">
    <div class="w-full max-w-md mx-auto">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-accent rounded-full mb-4">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Akıllı Futbolcu Analizi</h1>
            <p class="text-gray-500 text-sm mt-1">Hesabınıza giriş yapın</p>
        </div>

        {{-- Login Card --}}
        <div class="bg-surface-800 border border-border rounded-2xl p-8">
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-1.5">E-posta</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500
                                  focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition-colors"
                           placeholder="ornek@email.com">
                    @error('email')
                        <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-1.5">Şifre</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-4 py-3 bg-surface-600 border border-border rounded-lg text-white placeholder-gray-500
                                  focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition-colors"
                           placeholder="••••••••">
                    @error('password')
                        <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember me --}}
                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember"
                           class="w-4 h-4 rounded border-border bg-surface-600 text-accent focus:ring-accent focus:ring-offset-0">
                    <label for="remember" class="ml-2 text-sm text-gray-400">Beni Hatırla</label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full py-3 bg-accent hover:bg-accent-hover text-white font-semibold rounded-lg
                               transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 focus:ring-offset-surface-800">
                    Giriş Yap
                </button>
            </form>
        </div>
    </div>
</x-layouts.guest>
