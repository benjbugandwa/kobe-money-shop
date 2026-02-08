<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Kobe Money Shop</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <script src="https://cdn.tailwindcss.com"></script>-->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles


</head>

<body class="min-h-screen flex flex-col bg-gradient-to-br from-gray-900 via-gray-800 to-black text-gray-100">

    {{-- NAVBAR --}}
    <nav class="bg-gray-900/80 backdrop-blur border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">

            {{-- Logo / Nom --}}
            <a href="{{ url('/dashboard') }}">
                <div class="text-lg font-bold tracking-wide">
                    💰 Kobe Money Shop
                </div>
            </a>


            {{-- User menu --}}


            {{-- Menu Rapports  --}}
            <div class="relative group">
                <!-- Bouton Administration -->
                <button class="flex items-center gap-1 text-gray-300 hover:text-white">
                    Rapports
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Menu déroulant -->
                <div
                    class="absolute left-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-lg
               opacity-0 invisible
               group-hover:opacity-100 group-hover:visible
               transition-all duration-200">

                    <a href="{{ route('rapports.index') }}" class="block px-4 py-2 hover:bg-gray-700">
                        Exporter les rapports
                    </a>


                </div>
            </div>
            {{-- Fin menu Rapports  --}}






            {{-- Menu administration  --}}
            <div class="relative group">
                <!-- Bouton Administration -->
                <button class="flex items-center gap-1 text-gray-300 hover:text-white">
                    Administration
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Menu déroulant -->
                <div
                    class="absolute left-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-lg
               opacity-0 invisible
               group-hover:opacity-100 group-hover:visible
               transition-all duration-200">

                    <a href="{{ route('admin.cotisations') }}" class="block px-4 py-2 hover:bg-gray-700">
                        Cotisations
                    </a>

                    <a href="{{ route('admin.emprunts') }}" class="block px-4 py-2 hover:bg-gray-700">
                        Emprunts
                    </a>

                    <a href="{{ route('admin.users') }}" class="block px-4 py-2 hover:bg-gray-700">
                        Utilisateurs
                    </a>
                </div>
            </div>

            {{-- Fin menu administration  --}}










            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-2 focus:outline-none">
                    <span class="text-sm hidden sm:block">
                        {{ auth()->user()->name }}
                    </span>

                    <div class="w-9 h-9 rounded-full bg-gray-700 flex items-center justify-center">
                        👤
                    </div>
                </button>

                {{-- Dropdown --}}
                <div x-show="open" @click.outside="open = false" x-transition
                    class="absolute right-0 mt-2 w-40 bg-gray-900 border border-gray-800 rounded-lg shadow-lg overflow-hidden">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-800">
                            🚪 Se déconnecter
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </nav>

    {{-- CONTENU --}}
    <main class="flex-1 flex items-center justify-center px-4 py-8">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="bg-gray-900/80 border-t border-gray-800 text-center text-sm text-gray-400 py-4">
        © {{ date('Y') }} Kobe Money Shop — Gestion transparente de la caisse commune
    </footer>

    @livewireScripts

    <!-- Toast Notifications -->
    <div x-data="{ show: false, message: '', type: '' }"
        x-on:toast.window="
        show = true;
        message = $event.detail.message;
        type = $event.detail.type;
        setTimeout(() => show = false, 3000);
    "
        x-show="show" x-transition class="fixed bottom-5 right-5 px-4 py-3 rounded shadow-lg text-white"
        :class="type === 'success' ? 'bg-green-600' : 'bg-red-600'">
        <span x-text="message"></span>
    </div>
    <!-- En Toast Notifications -->

</body>

</html>
