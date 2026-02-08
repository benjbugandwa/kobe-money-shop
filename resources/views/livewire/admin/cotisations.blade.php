<div class="max-w-7xl mx-auto py-8">

    <h1 class="text-2xl font-bold text-gray-100 mb-6">
        💰 Gestion des cotisations
    </h1>

    {{-- Filtres par Membre et par date --}}

    <div class="bg-gray-900 rounded-xl p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <!-- Filtre membre -->
            <div>
                <label class="block text-sm text-gray-400 mb-1">
                    Membre
                </label>
                <select wire:model.live="userId" class="w-full rounded bg-gray-800 border-gray-700 text-gray-200">
                    <option value="">Tous les membres</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date début -->
            <div>
                <label class="block text-sm text-gray-400 mb-1">
                    Date début
                </label>
                <input type="date" wire:model="dateDebut"
                    class="w-full rounded bg-gray-800 border-gray-700 text-gray-200">
            </div>

            <!-- Date fin -->
            <div>
                <label class="block text-sm text-gray-400 mb-1">
                    Date fin
                </label>
                <input type="date" wire:model="dateFin"
                    class="w-full rounded bg-gray-800 border-gray-700 text-gray-200">
            </div>

            <!-- Reset -->
            <div class="flex items-end">
                <button wire:click="resetFilters"
                    class="w-full px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded text-gray-200">
                    Réinitialiser
                </button>
            </div>

        </div>
    </div>


    {{-- Fin Filtres --}}

    <div class="bg-gray-900 rounded-xl shadow-lg overflow-hidden">


        {{-- Bouton ajout cotisation --}}
        <div class="flex justify-end mb-4">
            <button wire:click="openCreateModal" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded">
                ➕ Ajouter une cotisation
            </button>
        </div>

        {{-- Fin Bouton --}}










        <table class="w-full text-sm text-gray-300">
            <thead class="bg-gray-800 text-gray-400">
                <tr>
                    <th class="px-6 py-3 text-left">Membre</th>
                    <th class="px-6 py-3 text-left">Libellé</th>
                    <th class="px-6 py-3 text-left">Montant</th>
                    <th class="px-6 py-3 text-left">Date</th>
                </tr>
            </thead>

            <tbody>
                @forelse($cotisations as $cotisation)
                    <tr class="border-b border-gray-800 hover:bg-gray-800/50">
                        <td class="px-6 py-4">
                            {{ $cotisation->user->name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $cotisation->libelle }}
                        </td>
                        <td class="px-6 py-4 font-semibold text-green-400">
                            {{ number_format($cotisation->montant, 0, ',', ' ') }} USD
                        </td>
                        <td class="px-6 py-4">
                            {{ $cotisation->date_cotisation->format('d/m/Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-6 text-center text-gray-400">
                            Aucune cotisation enregistrée.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $cotisations->links() }}
    </div>


    {{-- Modal creation cotisation --}}
    @if ($showCreateModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black/60 z-50">
            <div class="bg-gray-900 rounded-xl p-6 w-full max-w-lg shadow-xl">

                <h2 class="text-lg font-semibold text-white mb-4">
                    Nouvelle cotisation
                </h2>

                <div class="space-y-4">

                    <!-- Membre -->
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">
                            Membre
                        </label>
                        <select wire:model.defer="newUserId"
                            class="w-full rounded bg-gray-800 border-gray-700 text-gray-200">
                            <option value="">Sélectionner un membre</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('newUserId')
                            <span class="text-red-400 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Date -->
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">
                            Date de cotisation
                        </label>
                        <input type="date" wire:model.defer="newDateCotisation" max="{{ now()->toDateString() }}"
                            class="w-full rounded bg-gray-800 border-gray-700 text-gray-200">
                        @error('newDateCotisation')
                            <span class="text-red-400 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Montant -->
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">
                            Montant (USD)
                        </label>
                        <input type="number" wire:model.defer="newMontant" placeholder="Minimum 5 USD"
                            class="w-full rounded bg-gray-800 border-gray-700 text-gray-200">
                        @error('newMontant')
                            <span class="text-red-400 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Libellé -->
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">
                            Libellé
                        </label>
                        <input type="text" wire:model.defer="newLibelle" placeholder="ex: Cotisation mois de janvier"
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
                        class="btn btn-primary w-100">
                        <!-- Texte normal -->
                        <span wire:loading.remove wire:target="save">
                            Enregistrer
                        </span>

                        <!-- Loader -->
                        <span wire:loading wire:target="save">
                            <span class="spinner-border spinner-border-sm me-2"></span>
                            Enregistrement...
                        </span>
                    </button>

                </div>

            </div>
        </div>
    @endif

    {{-- Fin modale creation cotisation --}}
</div>
