<div class="w-full max-w-6xl">

    {{-- TITRE --}}
    <h1 class="text-2xl font-bold mb-8 text-center">
        Tableau de bord
    </h1>

    {{-- STATS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-gray-900 rounded-xl p-6 shadow">
            <p class="text-gray-400 text-sm">Total cotisations</p>
            <p class="text-2xl font-bold text-green-400">
                {{ number_format($totalCotisations) }} USD
            </p>
        </div>

        <div class="bg-gray-900 rounded-xl p-6 shadow">
            <p class="text-gray-400 text-sm">Total emprunts</p>
            <p class="text-2xl font-bold text-red-400">
                {{ number_format($totalEmprunts) }} USD
            </p>
        </div>

        <div class="bg-gray-900 rounded-xl p-6 shadow">
            <p class="text-gray-400 text-sm">Total intérêts payés</p>
            <p class="text-2xl font-bold text-red-400">
                {{ number_format($beneficeTotal) }} USD
            </p>
        </div>

        <div class="bg-gray-900 rounded-xl p-6 shadow">
            <p class="text-gray-400 text-sm">Solde de la caisse</p>
            <p class="text-2xl font-bold text-blue-400">
                {{ number_format($soldeCaisse) }} USD
            </p>
        </div>
    </div>

    {{-- TABLES --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        {{-- Cotisations --}}
        <div class="bg-gray-900 rounded-xl p-6 shadow">
            <h2 class="font-semibold mb-4">Mes cotisations</h2>

            <table class="w-full text-sm">
                <thead class="text-gray-400 border-b border-gray-800">
                    <tr>
                        <th class="py-2 text-left">Date</th>
                        <th>Libellé</th>
                        <th class="text-right">Montant</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mesCotisations as $c)
                        <tr class="border-b border-gray-800">
                            <td class="py-2">{{ $c->date_cotisation }}</td>
                            <td>{{ $c->libelle }}</td>
                            <td class="text-right text-green-400">
                                {{ number_format($c->montant) }} USD
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-gray-500">
                                Aucune cotisation
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Emprunts --}}
        <div class="bg-gray-900 rounded-xl p-6 shadow">
            <h2 class="font-semibold mb-4">Mes emprunts</h2>

            <table class="w-full text-sm">
                <thead class="text-gray-400 border-b border-gray-800">
                    <tr>
                        <th>Date</th>
                        <th>Échéance</th>
                        <th>Montant</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mesEmprunts as $e)
                        <tr class="border-b border-gray-800">
                            <td class="py-2">{{ $e->date_emprunt }}</td>
                            <td>{{ $e->date_echeance }}</td>
                            <td class="text-yellow-400">
                                {{ number_format($e->montant_initial) }} USD
                            </td>
                            <td>
                                <span
                                    class="px-2 py-1 rounded text-xs
                                    {{ $e->statut_emprunt === 'remboursé' ? 'bg-green-600' : 'bg-red-600' }}">
                                    {{ ucfirst($e->statut_emprunt) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-gray-500">
                                Aucun emprunt
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
