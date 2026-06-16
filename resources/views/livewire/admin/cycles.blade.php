<div class="w-full max-w-6xl mx-auto py-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-100">Gestion des cycles</h1>
            <p class="text-sm text-gray-400">Creation, controle et cloture du cycle de caisse.</p>
        </div>

        <button wire:click="openCreateModal" wire:loading.attr="disabled" @disabled($currentCycle)
            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg disabled:opacity-60 disabled:cursor-not-allowed">
            <span wire:loading.remove wire:target="openCreateModal">
                {{ $currentCycle ? 'Cycle deja en cours' : 'Nouveau cycle' }}
            </span>
            <span wire:loading wire:target="openCreateModal">Ouverture...</span>
        </button>
    </div>

    @if ($currentCycle)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gray-900 rounded-lg p-4">
                <p class="text-xs text-gray-400">Cycle en cours</p>
                <p class="font-semibold text-gray-100">{{ $currentCycle->num_cycle }}</p>
            </div>
            <div class="bg-gray-900 rounded-lg p-4">
                <p class="text-xs text-gray-400">Periode</p>
                <p class="font-semibold text-gray-100">
                    {{ $currentCycle->date_debut->format('d/m/Y') }} - {{ $currentCycle->date_fin->format('d/m/Y') }}
                </p>
            </div>
            <div class="bg-gray-900 rounded-lg p-4">
                <p class="text-xs text-gray-400">Cotisations du cycle</p>
                <p class="font-semibold text-green-400">{{ number_format($totalCotisations, 0, ',', ' ') }} USD</p>
            </div>
            <div class="bg-gray-900 rounded-lg p-4">
                <p class="text-xs text-gray-400">Interets a redistribuer</p>
                <p class="font-semibold text-blue-400">{{ number_format($totalInterets, 0, ',', ' ') }} USD</p>
            </div>
        </div>

        @if ($showCloseModal)
            <div class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center px-4">
                <div class="bg-gray-900 border border-gray-800 rounded-xl shadow-xl w-full max-w-5xl max-h-[90vh] overflow-y-auto">
                    <div class="p-6 border-b border-gray-800 flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-white">Cloturer le cycle en cours</h2>
                            <p class="text-sm text-gray-400">{{ $currentCycle->num_cycle }}</p>
                        </div>
                        <button wire:click="$set('showCloseModal', false)" class="px-3 py-1 bg-gray-700 rounded text-sm">
                            Fermer
                        </button>
                    </div>

                    <div class="p-6">
                        <table class="w-full text-sm text-gray-300">
                            <thead class="bg-gray-800 text-gray-400">
                                <tr>
                                    <th class="px-4 py-3 text-left">Membre</th>
                                    <th class="px-4 py-3 text-left">Email</th>
                                    <th class="px-4 py-3 text-right">Cotisations</th>
                                    <th class="px-4 py-3 text-right">Part</th>
                                    <th class="px-4 py-3 text-right">Montant a reverser</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($redistributions as $row)
                                    <tr class="border-b border-gray-800">
                                        <td class="px-4 py-3">{{ $row['name'] }}</td>
                                        <td class="px-4 py-3">{{ $row['email'] }}</td>
                                        <td class="px-4 py-3 text-right text-green-400">
                                            {{ number_format($row['cotisations'], 0, ',', ' ') }} USD
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            {{ number_format($row['pourcentage'], 2, ',', ' ') }} %
                                        </td>
                                        <td class="px-4 py-3 text-right text-blue-400 font-semibold">
                                            {{ number_format($row['montant'], 0, ',', ' ') }} USD
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                            Aucune cotisation sur ce cycle.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="flex justify-end gap-3 mt-6">
                            <button wire:click="$set('showCloseModal', false)" class="px-4 py-2 bg-gray-700 rounded">
                                Annuler
                            </button>
                            <button wire:click="closeCurrentCycle" wire:loading.attr="disabled"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded disabled:opacity-60">
                                <span wire:loading.remove wire:target="closeCurrentCycle">Cloturer</span>
                                <span wire:loading wire:target="closeCurrentCycle">Traitement...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="bg-gray-900 rounded-lg p-6 text-center text-gray-300">
            Aucun cycle en cours. Creez un cycle pour enregistrer des transactions.
        </div>
    @endif

    <div class="bg-gray-900 rounded-xl shadow-lg overflow-hidden mt-6">
        <table class="w-full text-sm text-gray-300">
            <thead class="bg-gray-800 text-gray-400">
                <tr>
                    <th class="px-6 py-3 text-left">Cycle</th>
                    <th class="px-6 py-3 text-left">Date debut</th>
                    <th class="px-6 py-3 text-left">Date fin</th>
                    <th class="px-6 py-3 text-left">Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cycles as $cycle)
                    <tr class="border-b border-gray-800">
                        <td class="px-6 py-4">{{ $cycle->num_cycle }}</td>
                        <td class="px-6 py-4">{{ $cycle->date_debut->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">{{ $cycle->date_fin->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded text-xs {{ $cycle->statut === 'En cours' ? 'bg-yellow-500/20 text-yellow-300' : 'bg-green-500/20 text-green-300' }}">
                                {{ $cycle->statut }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-6 text-center text-gray-500">Aucun cycle.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($showCreateModal)
        <div class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center px-4">
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 w-full max-w-lg shadow-xl"
                x-data="{
                    dateDebut: @entangle('date_debut'),
                    dateFin: @entangle('date_fin'),
                    tomorrow: '{{ now()->addDay()->toDateString() }}',
                    minDateFin() {
                        const base = this.dateDebut && this.dateDebut >= this.tomorrow ? new Date(this.dateDebut) : new Date(this.tomorrow);
                        if (this.dateDebut && this.dateDebut >= this.tomorrow) {
                            base.setDate(base.getDate() + 1);
                        }
                        return base.toISOString().slice(0, 10);
                    }
                }">
                <h2 class="text-lg font-semibold text-white mb-5">Nouveau cycle</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Date debut</label>
                        <input type="date" wire:model="date_debut" x-bind:max="dateFin || null"
                            class="w-full bg-gray-800 border-gray-700 rounded text-gray-100">
                        @error('date_debut')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Date fin</label>
                        <input type="date" wire:model="date_fin" x-bind:min="minDateFin()"
                            class="w-full bg-gray-800 border-gray-700 rounded text-gray-100">
                        @error('date_fin')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="$set('showCreateModal', false)" class="px-4 py-2 bg-gray-700 rounded">
                        Annuler
                    </button>
                    <button wire:click="createCycle" wire:loading.attr="disabled"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded disabled:opacity-60">
                        <span wire:loading.remove wire:target="createCycle">Enregistrer</span>
                        <span wire:loading wire:target="createCycle">Enregistrement...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
