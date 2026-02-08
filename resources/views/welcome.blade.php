<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kobe money shop</title>

    <!-- Tailwind CDN (pour test rapide) -->
    <!-- <script src="https://cdn.tailwindcss.com"></script>-->

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#22c55e'
                    }
                }
            }
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-black text-gray-100">

    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-6xl w-full grid grid-cols-1 md:grid-cols-2 gap-10 items-center">

            <!-- Image -->
            <div class="flex justify-center">
                <img src="https://images.unsplash.com/photo-1554224155-8d04cb21cd6c" alt="Caisse commune"
                    class="rounded-2xl shadow-2xl w-full max-w-md object-cover" />
            </div>

            <!-- Texte -->
            <div class="bg-gray-900/60 backdrop-blur-lg rounded-2xl p-8 shadow-xl">
                <h1 class="text-3xl md:text-4xl font-bold mb-4">
                    Kobe Money Shop
                </h1>

                <p class="text-gray-300 mb-6 leading-relaxed">
                    Une solution simple et transparente pour gérer l’argent en groupe.
                    Collectez, suivez et gérez les contributions en toute confiance,
                    que ce soit pour une famille, une association ou un groupe d’amis.
                </p>

                <ul class="mb-8 space-y-2 text-gray-400">
                    <li>✔ Contributions centralisées</li>
                    <li>✔ Suivi clair des dépenses</li>
                    <li>✔ Accès sécurisé par compte Google</li>
                </ul>

                <!-- Bouton Google -->
                <a href="{{ route('google.login') }}"
                    class="flex items-center justify-center gap-3 w-full bg-white text-gray-800 font-semibold py-3 rounded-xl hover:bg-gray-200 transition">
                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-6 h-6" />
                    Continuer avec Google
                </a>
            </div>

        </div>
    </div>

</body>

</html>
