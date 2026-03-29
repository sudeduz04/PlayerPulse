<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Akıllı Futbolcu Analizi' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface-900 text-white min-h-screen">
    <div class="flex min-h-screen">
        <x-sidebar />

        <div class="flex-1 flex flex-col min-h-screen">
            <x-navbar :title="$title ?? ''" />

            <main class="flex-1 p-6">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-accent/15 border border-accent/30 rounded-lg text-accent text-sm">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-500/15 border border-red-500/30 rounded-lg text-red-400 text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
