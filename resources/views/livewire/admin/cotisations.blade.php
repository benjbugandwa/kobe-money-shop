<div class="max-w-7xl mx-auto py-8">
    <h1 class="text-2xl font-bold text-gray-100 mb-6">
        Gestion des cotisations
    </h1>

    @if ($cycleEnCours)
        <div class="bg-gray-900 rounded-lg p-4 mb-6 text-sm text-gray-300">
            Cycle en cours :
            <span class="font-semibold text-indigo-300">{{ $cycleEnCours->num_cycle }}</span>
            <span class="text-gray-500">
                ({{ $cycleEnCours->date_debut->format('d/m/Y') }} - {{ $cycleEnCours->date_fin->format('d/m/Y') }})
            </span>
        </div>
    @else
        <div class="bg-red-900/40 border border-red-800 rounded-lg p-4 mb-6 text-sm text-red-200">
            Aucun cycle en cours. Les nouvelles cotisations ne peuvent pas etre enregistrees.
        </div>
    @endif

    <div class="bg-gray-900 rounded-xl p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm text-gray-400 mb-1">Membre</label>
                <select wire:model.live="userId" class="w-full rounded bg-gray-800 border-gray-700 text-gray-200">
                    <option value="">Tous les membres</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-1">Date debut</label>
                <input type="date" wire:model="dateDebut"
                    class="w-full rounded bg-gray-800 border-gray-700 text-gray-200">
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-1">Date fin</label>
                <input type="date" wire:model="dateFin"
                    class="w-full rounded bg-gray-800 border-gray-700 text-gray-200">
            </div>

            <div class="flex items-end">
                <button wire:click="resetFilters"
                    class="w-full px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded text-gray-200">
                    Reinitialiser
                </button>
            </div>
        </div>
    </div>

    <div class="bg-gray-900 rounded-xl shadow-lg overflow-hidden">
        <div class="flex justify-end mb-4">
            <button wire:click="openCreateModal" wire:loading.attr="disabled"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded disabled:opacity-60">
                <span wire:loading.remove wire:target="openCreateModal">Ajouter une cotisation</span>
                <span wire:loading wire:target="openCreateModal">Ouverture...</span>
            </button>
        </div>

        <table class="w-full text-sm text-gray-300">
            <thead class="bg-gray-800 text-gray-400">
                <tr>
                    <th class="px-6 py-3 text-left">Membre</th>
                    <th class="px-6 py-3 text-left">Cycle</th>
                    <th class="px-6 py-3 text-left">Libelle</th>
                    <th class="px-6 py-3 text-left">Montant</th>
                    <th class="px-6 py-3 text-left">Date</th>
                </tr>
            </thead>

            <tbody>
                @forelse($cotisations as $cotisation)
                    <tr class="border-b border-gray-800 hover:bg-gray-800/50">
                        <td class="px-6 py-4">{{ $cotisation->user->name }}</td>
                        <td class="px-6 py-4 text-gray-400">{{ $cotisation->cycle?->num_cycle ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $cotisation->libelle }}</td>
                        <td class="px-6 py-4 font-semibold text-green-400">
                            {{ number_format($cotisation->montant, 0, ',', ' ') }} USD
                        </td>
                        <td class="px-6 py-4">{{ $cotisation->date_cotisation->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-6 text-center text-gray-400">
                            Aucune cotisation enregistree.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $cotisations->links() }}
    </div>

    @if ($showCreateModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black/60 z-50 px-4">
            <div class="bg-gray-900 rounded-xl p-6 w-full max-w-lg shadow-xl">
                <h2 class="text-lg font-semibold text-white mb-4">
                    Nouvelle cotisation
                </h2>

                <div class="space-y-4">
                    @if ($cycleEnCours)
                        <div class="bg-gray-800/70 rounded-lg p-3 text-sm text-gray-300">
                            Cycle :
                            <span class="font-semibold text-indigo-300">{{ $cycleEnCours->num_cycle }}</span>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Membre</label>
                        <select wire:model.defer="newUserId"
                            class="w-full rounded bg-gray-800 border-gray-700 text-gray-200">
                            <option value="">Selectionner un membre</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('newUserId')
                            <span class="text-red-400 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Date de cotisation</label>
                        <input type="date" wire:model.defer="newDateCotisation" max="{{ now()->toDateString() }}"
                            class="w-full rounded bg-gray-800 border-gray-700 text-gray-200">
                        @error('newDateCotisation')
                            <span class="text-red-400 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Montant (USD)</label>
                        <input type="number" wire:model.defer="newMontant" min="5" step="0.01"
                            class="w-full rounded bg-gray-800 border-gray-700 text-gray-200">
                        @error('newMontant')
                            <span class="text-red-400 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Libelle</label>
                        <input type="text" wire:model.defer="newLibelle"
                            class="w-full rounded bg-gray-800 border-gray-700 text-gray-200">
                        @error('newLibelle')
                            <span class="text-red-400 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button wire:click="closeCreateModal"
                        class="px-4 py-2 rounded bg-gray-700 hover:bg-gray-600 text-gray-200">
                        Annuler
                    </button>
                    <button type="submit" wire:click="save" wire:loading.attr="disabled" wire:target="save"
                        class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white disabled:opacity-60">
                        <span wire:loading.remove wire:target="save">Enregistrer</span>
                        <span wire:loading wire:target="save">Enregistrement...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
