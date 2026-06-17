<div class="w-full max-w-7xl mx-auto space-y-8">
    <div class="text-center">
        <h1 class="text-2xl font-bold">
            Tableau de bord
        </h1>
        <p class="mt-2 text-sm text-gray-400">
            Situation personnelle et vue globale de la caisse commune
        </p>
    </div>

    @if ($cycleEnCours)
        <div class="bg-gray-900 border border-gray-800 rounded-lg p-4 text-sm text-gray-300 text-center">
            Cycle en cours :
            <span class="font-semibold text-indigo-300">{{ $cycleEnCours->num_cycle }}</span>
            <span class="text-gray-500">
                ({{ $cycleEnCours->date_debut->format('d/m/Y') }} - {{ $cycleEnCours->date_fin->format('d/m/Y') }})
            </span>
        </div>
    @else
        <div class="bg-red-900/40 border border-red-800 rounded-lg p-4 text-sm text-red-200 text-center">
            Aucun cycle en cours. Les donnees d'emprunts, d'interets et de caisse sont donc a zero.
        </div>
    @endif

    <section class="space-y-4">
        <div>
            <h2 class="text-lg font-semibold">Indicateurs cles du cycle</h2>
            <p class="text-sm text-gray-400">Les ratios comparent les montants du cycle en cours aux cotisations totales du cycle.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-5 shadow">
                <p class="text-sm text-gray-400">Demande d'emprunts</p>
                <p class="mt-2 text-3xl font-bold text-red-300">
                    {{ number_format($tauxDemandeEmpruntsCycleEnCours, 1, ',', ' ') }} %
                </p>
                <p class="mt-2 text-xs text-gray-500">
                    Capital encore sorti / cotisations du cycle
                </p>
                <div class="mt-4 h-2 rounded-full bg-gray-800 overflow-hidden">
                    <div class="h-full rounded-full bg-red-400" style="width: {{ min(100, max(0, $tauxDemandeEmpruntsCycleEnCours)) }}%"></div>
                </div>
            </div>

            <div class="bg-gray-900 border border-gray-800 rounded-lg p-5 shadow">
                <p class="text-sm text-gray-400">Rendement des emprunts</p>
                <p class="mt-2 text-3xl font-bold text-amber-300">
                    {{ number_format($tauxRendementInteretsCycleEnCours, 1, ',', ' ') }} %
                </p>
                <p class="mt-2 text-xs text-gray-500">
                    Interets et penalites encaisses / cotisations
                </p>
                <div class="mt-4 h-2 rounded-full bg-gray-800 overflow-hidden">
                    <div class="h-full rounded-full bg-amber-300" style="width: {{ min(100, max(0, $tauxRendementInteretsCycleEnCours)) }}%"></div>
                </div>
            </div>

            <div class="bg-gray-900 border border-gray-800 rounded-lg p-5 shadow">
                <p class="text-sm text-gray-400">Ma part des cotisations</p>
                <p class="mt-2 text-3xl font-bold text-emerald-300">
                    {{ number_format($partCotisationsMembreCycleEnCours, 1, ',', ' ') }} %
                </p>
                <p class="mt-2 text-xs text-gray-500">
                    Mes cotisations / cotisations totales
                </p>
                <div class="mt-4 h-2 rounded-full bg-gray-800 overflow-hidden">
                    <div class="h-full rounded-full bg-emerald-300" style="width: {{ min(100, max(0, $partCotisationsMembreCycleEnCours)) }}%"></div>
                </div>
            </div>

            <div class="bg-gray-900 border border-gray-800 rounded-lg p-5 shadow">
                <p class="text-sm text-gray-400">Disponibilite de la caisse</p>
                <p class="mt-2 text-3xl font-bold text-sky-300">
                    {{ number_format($tauxCouvertureCaisseCycleEnCours, 1, ',', ' ') }} %
                </p>
                <p class="mt-2 text-xs text-gray-500">
                    Solde estime / cotisations du cycle
                </p>
                <div class="mt-4 h-2 rounded-full bg-gray-800 overflow-hidden">
                    <div class="h-full rounded-full bg-sky-300" style="width: {{ min(100, max(0, $tauxCouvertureCaisseCycleEnCours)) }}%"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="space-y-4">
        <h2 class="text-lg font-semibold">Rubrique Cotisations</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-5 shadow">
                <p class="text-gray-400 text-sm">Mes cotisations cloturees</p>
                <p class="mt-2 text-2xl font-bold text-green-300">
                    {{ number_format($cotisationsCyclesClotures, 0, ',', ' ') }} USD
                </p>
            </div>

            <div class="bg-gray-900 border border-gray-800 rounded-lg p-5 shadow">
                <p class="text-gray-400 text-sm">Mes cotisations du cycle</p>
                <p class="mt-2 text-2xl font-bold text-green-400">
                    {{ number_format($cotisationsCycleEnCours, 0, ',', ' ') }} USD
                </p>
            </div>

            <div class="bg-gray-900 border border-gray-800 rounded-lg p-5 shadow">
                <p class="text-gray-400 text-sm">Total de mes cotisations</p>
                <p class="mt-2 text-2xl font-bold text-emerald-300">
                    {{ number_format($totalCotisationsMembre, 0, ',', ' ') }} USD
                </p>
            </div>

            <div class="bg-gray-900 border border-gray-800 rounded-lg p-5 shadow">
                <p class="text-gray-400 text-sm">Cotisations du cycle</p>
                <p class="mt-2 text-2xl font-bold text-teal-300">
                    {{ number_format($totalCotisationsCycleEnCours, 0, ',', ' ') }} USD
                </p>
            </div>
        </div>
    </section>

    <section class="space-y-4">
        <h2 class="text-lg font-semibold">Rubrique Emprunts</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-5 shadow">
                <p class="text-gray-400 text-sm">Mes emprunts ouverts du cycle</p>
                <p class="mt-2 text-2xl font-bold text-red-400">
                    {{ number_format($totalEmpruntsCycleEnCours, 0, ',', ' ') }} USD
                </p>
            </div>

            <div class="bg-gray-900 border border-gray-800 rounded-lg p-5 shadow">
                <p class="text-gray-400 text-sm">Emprunts ouverts du cycle</p>
                <p class="mt-2 text-2xl font-bold text-orange-300">
                    {{ number_format($capitalEmpruntsOuvertsCycleEnCours, 0, ',', ' ') }} USD
                </p>
            </div>

            <div class="bg-gray-900 border border-gray-800 rounded-lg p-5 shadow">
                <p class="text-gray-400 text-sm">Interets attendus sur emprunts ouverts</p>
                <p class="mt-2 text-2xl font-bold text-purple-300">
                    {{ number_format($interetsAttendusEmpruntsOuvertsCycleEnCours, 0, ',', ' ') }} USD
                </p>
            </div>
        </div>
    </section>

    <section class="space-y-4">
        <h2 class="text-lg font-semibold">Rubrique Interets et caisse</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-5 shadow">
                <p class="text-gray-400 text-sm">Mes interets payes du cycle</p>
                <p class="mt-2 text-2xl font-bold text-orange-300">
                    {{ number_format($interetsPayesCycleEnCours, 0, ',', ' ') }} USD
                </p>
            </div>

            <div class="bg-gray-900 border border-gray-800 rounded-lg p-5 shadow">
                <p class="text-gray-400 text-sm">Interets et penalites encaisses</p>
                <p class="mt-2 text-2xl font-bold text-amber-300">
                    {{ number_format($interetsPayesTousEmpruntsCycleEnCours, 0, ',', ' ') }} USD
                </p>
            </div>

            <div class="bg-gray-900 border border-gray-800 rounded-lg p-5 shadow">
                <p class="text-gray-400 text-sm">Solde caisse du cycle</p>
                <p class="mt-2 text-2xl font-bold text-blue-400">
                    {{ number_format($soldeCaisseCycleEnCours, 0, ',', ' ') }} USD
                </p>
            </div>
        </div>
    </section>

    <section class="space-y-4">
        <h2 class="text-lg font-semibold">Lecture rapide du cycle</h2>

        @php
            $barScale = max(
                $totalCotisationsCycleEnCours,
                $capitalEmpruntsOuvertsCycleEnCours,
                $interetsPayesTousEmpruntsCycleEnCours,
                max(0, $soldeCaisseCycleEnCours),
                1
            );

            $cycleBars = [
                ['label' => 'Cotisations', 'value' => $totalCotisationsCycleEnCours, 'color' => 'bg-teal-300'],
                ['label' => 'Capital sorti', 'value' => $capitalEmpruntsOuvertsCycleEnCours, 'color' => 'bg-red-400'],
                ['label' => 'Interets encaisses', 'value' => $interetsPayesTousEmpruntsCycleEnCours, 'color' => 'bg-amber-300'],
                ['label' => 'Solde estime', 'value' => $soldeCaisseCycleEnCours, 'color' => $soldeCaisseCycleEnCours >= 0 ? 'bg-sky-300' : 'bg-red-500'],
            ];
        @endphp

        <div class="bg-gray-900 border border-gray-800 rounded-lg p-5 shadow space-y-4">
            @foreach ($cycleBars as $bar)
                @php
                    $barWidth = min(100, max(0, abs($bar['value']) / $barScale * 100));
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-[10rem_1fr_8rem] gap-2 sm:gap-4 sm:items-center">
                    <div class="text-sm text-gray-300">{{ $bar['label'] }}</div>
                    <div class="h-3 rounded-full bg-gray-800 overflow-hidden">
                        <div class="h-full rounded-full {{ $bar['color'] }}" style="width: {{ $barWidth }}%"></div>
                    </div>
                    <div class="text-sm sm:text-right font-semibold {{ $bar['value'] >= 0 ? 'text-gray-100' : 'text-red-300' }}">
                        {{ number_format($bar['value'], 0, ',', ' ') }} USD
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-gray-900 border border-gray-800 rounded-lg p-5 shadow">
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

        <div class="bg-gray-900 border border-gray-800 rounded-lg p-5 shadow">
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
    </section>
</div>
