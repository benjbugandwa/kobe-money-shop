<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>403 | Accès interdit</title>
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen flex items-center justify-center bg-gray-900 text-gray-100">

    <div class="text-center max-w-md">
        <h1 class="text-6xl font-bold text-red-500 mb-4">403</h1>

        <h2 class="text-2xl font-semibold mb-2">
            Accès réservé aux administrateurs
        </h2>

        <p class="text-gray-400 mb-6">
            Vous n’avez pas l’autorisation d’accéder à cette page.
        </p>

        <a href="{{ url('/dashboard') }}"
            class="inline-flex items-center justify-center
          px-6 py-3
          bg-indigo-600 text-white
          rounded-lg
          shadow-lg
          hover:bg-indigo-700
          focus:outline-none focus:ring-2 focus:ring-indigo-400
          transition">
            ← Retour au dashboard
        </a>
    </div>

</body>

</html>
