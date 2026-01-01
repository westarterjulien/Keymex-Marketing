<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Validation BAT - KEYMEX Marketing' }}</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>K</text></svg>">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-gradient-to-br from-gray-50 via-gray-100 to-gray-50">
    {{-- Background pattern --}}
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-keymex-red/5 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-keymex-red/5 rounded-full blur-3xl"></div>
    </div>

    <div class="min-h-full flex flex-col">
        {{-- Header --}}
        <header class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-40 border-b border-gray-100">
            <div class="mx-auto max-w-4xl px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 bg-keymex-red rounded-xl shadow-lg shadow-keymex-red/20">
                        <span class="text-white font-bold text-lg">K</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xl font-bold text-gray-900 tracking-tight">KEYMEX</span>
                        <span class="text-xs text-gray-500 -mt-1">Marketing</span>
                    </div>
                </div>
            </div>
        </header>

        {{-- Main Content --}}
        <main class="flex-grow">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>

        {{-- Footer --}}
        <footer class="bg-white/60 backdrop-blur-sm border-t border-gray-100">
            <div class="mx-auto max-w-4xl px-4 py-6 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <svg class="w-4 h-4 text-keymex-red" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>Lien securise</span>
                    </div>
                    <p class="text-sm text-gray-400">
                        KEYMEX &copy; {{ date('Y') }} - Service Marketing
                    </p>
                </div>
            </div>
        </footer>
    </div>

    @livewireScripts
</body>
</html>
