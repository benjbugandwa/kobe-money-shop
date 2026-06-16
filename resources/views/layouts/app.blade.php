<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Kobe Money Shop</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen flex flex-col bg-gradient-to-br from-gray-900 via-gray-800 to-black text-gray-100">
    <nav x-data="{ mobileOpen: false, reportsOpen: false, adminOpen: false, userOpen: false }"
        class="sticky top-0 z-40 bg-gray-900/95 backdrop-blur border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4">
            <div class="h-16 flex items-center justify-between gap-4">
                <a href="{{ route('dashboard') }}" class="text-base sm:text-lg font-bold tracking-wide shrink-0">
                    Kobe Money Shop
                </a>

                <div class="hidden md:flex items-center justify-center gap-6">
                    <div class="relative" @click.outside="reportsOpen = false">
                        <button type="button" @click="reportsOpen = !reportsOpen; adminOpen = false; userOpen = false"
                            class="flex items-center gap-1 text-gray-300 hover:text-white">
                            Rapports
                            <svg class="w-4 h-4 transition-transform" :class="reportsOpen ? 'rotate-180' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="reportsOpen" x-transition
                            class="absolute left-0 mt-3 w-56 bg-gray-900 border border-gray-800 rounded-lg shadow-lg overflow-hidden">
                            <a href="{{ route('rapports.index') }}" class="block px-4 py-3 text-sm hover:bg-gray-800">
                                Exporter les rapports
                            </a>
                        </div>
                    </div>

                    <div class="relative" @click.outside="adminOpen = false">
                        <button type="button" @click="adminOpen = !adminOpen; reportsOpen = false; userOpen = false"
                            class="flex items-center gap-1 text-gray-300 hover:text-white">
                            Administration
                            <svg class="w-4 h-4 transition-transform" :class="adminOpen ? 'rotate-180' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="adminOpen" x-transition
                            class="absolute left-0 mt-3 w-64 bg-gray-900 border border-gray-800 rounded-lg shadow-lg overflow-hidden">
                            <a href="{{ route('admin.cotisations') }}" class="block px-4 py-3 text-sm hover:bg-gray-800">Cotisations</a>
                            <a href="{{ route('admin.emprunts') }}" class="block px-4 py-3 text-sm hover:bg-gray-800">Emprunts</a>
                            <a href="{{ route('admin.cycles') }}" class="block px-4 py-3 text-sm hover:bg-gray-800">Cloturer le cycle en cours</a>
                            <a href="{{ route('admin.export-data') }}" class="block px-4 py-3 text-sm hover:bg-gray-800">Exporter les donnees</a>
                            <a href="{{ route('admin.users') }}" class="block px-4 py-3 text-sm hover:bg-gray-800">Utilisateurs</a>
                        </div>
                    </div>
                </div>

                <div class="hidden md:block relative" @click.outside="userOpen = false">
                    <button type="button" @click="userOpen = !userOpen; reportsOpen = false; adminOpen = false"
                        class="flex items-center gap-2 focus:outline-none text-gray-300 hover:text-white">
                        <span class="text-sm max-w-40 truncate">{{ auth()->user()->name }}</span>
                        <div class="w-9 h-9 rounded-full bg-gray-700 flex items-center justify-center">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    </button>

                    <div x-show="userOpen" x-transition
                        class="absolute right-0 mt-3 w-44 bg-gray-900 border border-gray-800 rounded-lg shadow-lg overflow-hidden">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-3 text-sm hover:bg-gray-800">
                                Se deconnecter
                            </button>
                        </form>
                    </div>
                </div>

                <button type="button" @click="mobileOpen = !mobileOpen"
                    class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-800 text-gray-200">
                    <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div x-show="mobileOpen" x-transition class="md:hidden border-t border-gray-800 py-3 space-y-2">
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg hover:bg-gray-800">Tableau de bord</a>

                <details class="rounded-lg bg-gray-800/50">
                    <summary class="cursor-pointer px-3 py-2 text-gray-200">Rapports</summary>
                    <div class="pb-2">
                        <a href="{{ route('rapports.index') }}" class="block px-6 py-2 text-sm text-gray-300 hover:bg-gray-800">
                            Exporter les rapports
                        </a>
                    </div>
                </details>

                <details class="rounded-lg bg-gray-800/50">
                    <summary class="cursor-pointer px-3 py-2 text-gray-200">Administration</summary>
                    <div class="pb-2">
                        <a href="{{ route('admin.cotisations') }}" class="block px-6 py-2 text-sm text-gray-300 hover:bg-gray-800">Cotisations</a>
                        <a href="{{ route('admin.emprunts') }}" class="block px-6 py-2 text-sm text-gray-300 hover:bg-gray-800">Emprunts</a>
                        <a href="{{ route('admin.cycles') }}" class="block px-6 py-2 text-sm text-gray-300 hover:bg-gray-800">Cloturer le cycle en cours</a>
                        <a href="{{ route('admin.export-data') }}" class="block px-6 py-2 text-sm text-gray-300 hover:bg-gray-800">Exporter les donnees</a>
                        <a href="{{ route('admin.users') }}" class="block px-6 py-2 text-sm text-gray-300 hover:bg-gray-800">Utilisateurs</a>
                    </div>
                </details>

                <div class="px-3 pt-2 border-t border-gray-800">
                    <p class="text-sm text-gray-400 mb-2 truncate">{{ auth()->user()->name }}</p>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 rounded-lg bg-gray-800 hover:bg-gray-700">
                            Se deconnecter
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-1 w-full px-3 sm:px-4 py-6 sm:py-8 overflow-x-hidden">
        <div class="w-full mx-auto">
            @yield('content')
        </div>
    </main>

    <footer class="bg-gray-900/80 border-t border-gray-800 text-center text-sm text-gray-400 px-4 py-4">
        &copy; {{ date('Y') }} Kobe Money Shop - Gestion transparente de la caisse commune
    </footer>

    @livewireScripts

    <div x-data="{ show: false, message: '', type: '' }"
        x-on:toast.window="
            show = true;
            message = $event.detail.message || ($event.detail[0] && $event.detail[0].message) || '';
            type = $event.detail.type || ($event.detail[0] && $event.detail[0].type) || 'success';
            setTimeout(() => show = false, 3000);
        "
        x-show="show" x-transition
        class="fixed bottom-5 left-4 right-4 sm:left-auto sm:right-5 sm:w-auto px-4 py-3 rounded shadow-lg text-white z-50"
        :class="type === 'success' ? 'bg-green-600' : 'bg-red-600'">
        <span x-text="message"></span>
    </div>

    @if (session('success') || session('error'))
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        type: @json(session('success') ? 'success' : 'error'),
                        message: @json(session('success') ?? session('error'))
                    }
                }));
            });
        </script>
    @endif
</body>

</html>
