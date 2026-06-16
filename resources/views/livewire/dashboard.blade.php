<div class="w-full max-w-6xl mx-auto">
    <h1 class="text-2xl font-bold mb-4 text-center">
        Tableau de bord
    </h1>

    @if ($cycleEnCours)
        <div class="bg-gray-900 rounded-lg p-4 mb-8 text-sm text-gray-300 text-center">
            Cycle en cours :
            <span class="font-semibold text-indigo-300">{{ $cycleEnCours->num_cycle }}</span>
            <span class="text-gray-500">
                ({{ $cycleEnCours->date_debut->format('d/m/Y') }} - {{ $cycleEnCours->date_fin->format('d/m/Y') }})
            </span>
        </div>
    @else
        <div class="bg-red-900/40 border border-red-800 rounded-lg p-4 mb-8 text-sm text-red-200 text-center">
            Aucun cycle en cours. Les donnees d'emprunts, d'interets et de caisse sont donc a zero.
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-gray-900 rounded-xl p-6 shadow">
            <p class="text-gray-400 text-sm">Mes cotisations cloturees</p>
            <p class="text-2xl font-bold text-green-300">
                {{ number_format($cotisationsCyclesClotures, 0, ',', ' ') }} USD
            </p>
        </div>

        <div class="bg-gray-900 rounded-xl p-6 shadow">
            <p class="text-gray-400 text-sm">Mes cotisations du cycle</p>
            <p class="text-2xl font-bold text-green-400">
                {{ number_format($cotisationsCycleEnCours, 0, ',', ' ') }} USD
            </p>
        </div>

        <div class="bg-gray-900 rounded-xl p-6 shadow">
            <p class="text-gray-400 text-sm">Total de mes cotisations</p>
            <p class="text-2xl font-bold text-emerald-300">
                {{ number_format($totalCotisationsMembre, 0, ',', ' ') }} USD
            </p>
        </div>

        <div class="bg-gray-900 rounded-xl p-6 shadow">
            <p class="text-gray-400 text-sm">Mes emprunts du cycle</p>
            <p class="text-2xl font-bold text-red-400">
                {{ number_format($totalEmpruntsCycleEnCours, 0, ',', ' ') }} USD
            </p>
        </div>

        <div class="bg-gray-900 rounded-xl p-6 shadow">
            <p class="text-gray-400 text-sm">Mes interets payes du cycle</p>
            <p class="text-2xl font-bold text-orange-300">
                {{ number_format($interetsPayesCycleEnCours, 0, ',', ' ') }} USD
            </p>
        </div>

        <div class="bg-gray-900 rounded-xl p-6 shadow">
            <p class="text-gray-400 text-sm">Interets payes du cycle</p>
            <p class="text-2xl font-bold text-amber-300">
                {{ number_format($interetsPayesTousEmpruntsCycleEnCours, 0, ',', ' ') }} USD
            </p>
        </div>

        <div class="bg-gray-900 rounded-xl p-6 shadow">
            <p class="text-gray-400 text-sm">Interets attendus du cycle</p>
            <p class="text-2xl font-bold text-purple-300">
                {{ number_format($interetsAttendusEmpruntsOuvertsCycleEnCours, 0, ',', ' ') }} USD
            </p>
        </div>

        <div class="bg-gray-900 rounded-xl p-6 shadow">
            <p class="text-gray-400 text-sm">Solde caisse du cycle</p>
            <p class="text-2xl font-bold text-blue-400">
                {{ number_format($soldeCaisseCycleEnCours, 0, ',', ' ') }} USD
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-gray-900 rounded-xl p-6 shadow">
            <h2 class="font-semibold mb-4">Mes cotisations du cycle en cours</h2>

            <table class="w-full text-sm">
                <thead class="text-gray-400 border-b border-gray-800">
                    <tr>
                        <th class="py-2 text-left">Date</th>
                        <th class="text-left">Libelle</th>
                        <th class="text-right">Montant</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mesCotisationsCycleEnCours as $cotisation)
                        <tr class="border-b border-gray-800">
                            <td class="py-2">{{ $cotisation->date_cotisation->format('d/m/Y') }}</td>
                            <td>{{ $cotisation->libelle }}</td>
                            <td class="text-right text-green-400">
                                {{ number_format($cotisation->montant, 0, ',', ' ') }} USD
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-gray-500">
                                Aucune cotisation pour le cycle en cours
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-gray-900 rounded-xl p-6 shadow">
            <h2 class="font-semibold mb-4">Mes emprunts du cycle en cours</h2>

            <table class="w-full text-sm">
                <thead class="text-gray-400 border-b border-gray-800">
                    <tr>
                        <th class="py-2 text-left">Date</th>
                        <th class="text-left">Echeance</th>
                        <th class="text-right">Montant</th>
                        <th class="text-right">Penalite</th>
                        <th class="text-left">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mesEmpruntsCycleEnCours as $emprunt)
                        <tr class="border-b border-gray-800">
                            <td class="py-2">{{ $emprunt->date_emprunt->format('d/m/Y') }}</td>
                            <td>{{ $emprunt->date_echeance->format('d/m/Y') }}</td>
                            <td class="text-right text-yellow-400">
                                {{ number_format($emprunt->montant_initial, 0, ',', ' ') }} USD
                            </td>
                            <td class="text-right text-red-300">
                                {{ number_format($emprunt->montant_penalite, 0, ',', ' ') }} USD
                            </td>
                            <td>
                                @if ($emprunt->statut_emprunt === 'remboursé')
                                    <span class="px-2 py-1 rounded text-xs bg-green-600">Rembourse</span>
                                @elseif ($emprunt->statut_emprunt === 'en_retard')
                                    <span class="px-2 py-1 rounded text-xs bg-red-600">En retard</span>
                                @else
                                    <span class="px-2 py-1 rounded text-xs bg-yellow-600">En cours</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500">
                                Aucun emprunt pour le cycle en cours
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
