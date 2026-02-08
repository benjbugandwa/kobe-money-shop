<div class="max-w-7xl mx-auto py-8">

    <h1 class="text-2xl font-bold text-gray-100 mb-6">
        🏦 Gestion des emprunts
    </h1>

    {{-- Filtres par statut et par membre --}}
    <div class="flex flex-wrap gap-4 mb-6">

        <!-- Filtre par statut -->
        <div>
            <label class="text-xs text-gray-400">Statut</label>
            <select wire:model.live="filterStatut"
                class="bg-gray-800 border-gray-700 text-gray-300 rounded px-3 py-2 text-sm">
                <option value="">Tous</option>
                <option value="en_cours">En cours</option>
                <option value="rembourse">Remboursé</option>
            </select>
        </div>

        <!-- Filtre par membre -->
        <div>
            <label class="text-xs text-gray-400">Membre</label>
            <select wire:model.live="filterUser"
                class="bg-gray-800 border-gray-700 text-gray-300 rounded px-3 py-2 text-sm">
                <option value="">Tous</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

    </div>

    {{-- Fin filtres par statut et par membre --}}

    {{-- Loader --}}
    <div wire:loading class="text-sm text-gray-400 mb-4">
        Chargement...
    </div>

    {{-- Fin loader --}}


    <div class="bg-gray-900 rounded-xl shadow-lg overflow-hidden">

        {{-- Bouton pour ouvrir la modale --}}
        <div class="flex justify-end mb-4">
            <button wire:click="openModal" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg">
                ➕ Nouvel emprunt
            </button>
        </div>
        {{-- Fin Bouton pour ouvrir la modale --}}

        <table class="w-full text-sm text-gray-300">
            <thead class="bg-gray-800 text-gray-400">
                <tr>
                    <th class="px-6 py-3 text-left">Membre</th>
                    <th class="px-6 py-3 text-left">Montant initial</th>
                    <th class="px-6 py-3 text-left">Taux (%)</th>
                    <th class="px-6 py-3 text-left">Montant final</th>
                    <th class="px-6 py-3 text-left">Date emprunt</th>
                    <th class="px-6 py-3 text-left">Échéance</th>
                    <th class="px-6 py-3 text-left">Statut</th>
                    <th class="px-6 py-3 text-left">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($emprunts as $emprunt)
                    <tr class="border-b border-gray-800 hover:bg-gray-800/50">

                        <td class="px-6 py-4">
                            {{ $emprunt->user->name }}
                        </td>

                        <td class="px-6 py-4 font-semibold text-yellow-400">
                            {{ number_format($emprunt->montant_initial, 0, ',', ' ') }} USD
                        </td>

                        <td class="px-6 py-4">
                            {{ $emprunt->taux_interet }} %
                        </td>

                        <td class="px-6 py-4 font-semibold text-green-400">
                            {{ number_format($emprunt->montant_final, 0, ',', ' ') }} USD
                        </td>

                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($emprunt->date_emprunt)->format('d/m/Y') }}
                        </td>

                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($emprunt->date_echeance)->format('d/m/Y') }}
                        </td>

                        <td class="px-6 py-4">
                            @if ($emprunt->statut_emprunt === 'en_cours')
                                <span class="px-3 py-1 rounded-full text-xs bg-yellow-500/20 text-yellow-400">
                                    En cours
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs bg-green-500/20 text-green-400">
                                    Remboursé
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            @if ($emprunt->statut_emprunt === 'en_cours' && $emprunt->user_id !== auth()->id())
                                <button wire:click="confirmStatutChange({{ $emprunt->id }})"
                                    class="text-sm text-orange-400 hover:text-orange-300">
                                    Marquer remboursé
                                </button>
                            @endif

                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-6 text-center text-gray-400">
                            Aucun emprunt enregistré.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $emprunts->links() }}
    </div>
    {{-- Modale pour ajouter un emprunt --}}
    @if ($showModal)
        <div class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">




            <div class="bg-gray-900 w-full max-w-lg rounded-xl p-6 shadow-xl">
                <h2 class="text-lg font-bold text-gray-100 mb-4">
                    ➕ Nouvel emprunt
                </h2>

                <div class="space-y-4">

                    <!--SOLDE CAISSE-->
                    <p class="text-sm text-gray-400 mb-2">
                        Solde actuel de la caisse :
                        <span class="font-semibold text-green-400">
                            {{ number_format($this->soldeCaisse(), 0, ',', ' ') }} USD
                        </span>
                    </p>



                    <!-- Membre -->
                    <div>
                        <label class="text-sm text-gray-400">Membre</label>
                        <select wire:model="user_id" class="w-full bg-gray-800 border-gray-700 rounded">
                            <option value="">-- Sélectionner --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="text-red-400 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Dates -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm text-gray-400">Date emprunt</label>
                            <input type="date" wire:model="date_emprunt" max="{{ now()->toDateString() }}"
                                class="w-full bg-gray-800 border-gray-700 rounded">
                        </div>

                        <div>
                            <label class="text-sm text-gray-400">Date échéance</label>
                            <input type="date" wire:model="date_echeance"
                                class="w-full bg-gray-800 border-gray-700 rounded">
                        </div>
                    </div>

                    <!-- Montants -->
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="text-sm text-gray-400">Montant initial</label>
                            <input type="number" wire:model="montant_initial"
                                class="w-full bg-gray-800 border-gray-700 rounded">
                        </div>

                        <div>
                            <label class="text-sm text-gray-400">Taux (%)</label>
                            <input type="number" wire:model="taux_interet"
                                class="w-full bg-gray-800 border-gray-700 rounded">
                        </div>

                        <div>
                            <label class="text-sm text-gray-400">Montant final</label>
                            <input type="number" wire:model="montant_final" readonly
                                class="w-full bg-gray-800 border-gray-700 rounded text-green-400 font-semibold">
                        </div>
                    </div>

                    <!-- Observation -->
                    <div>
                        <label class="text-sm text-gray-400">Observation</label>
                        <textarea wire:model="observation" class="w-full bg-gray-800 border-gray-700 rounded"></textarea>
                    </div>

                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="$set('showModal', false)" class="px-4 py-2 bg-gray-700 rounded">
                        Annuler
                    </button>

                    <button wire:click="save" wire:loading.attr="disabled"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded">
                        <span wire:loading.remove>Enregistrer</span>
                        <span wire:loading>Enregistrement...</span>
                    </button>
                </div>
            </div>

        </div>
    @endif

    {{-- Fin modale pour ajouter un emprunt --}}

    {{-- Modale de confirmation de changement de statut --}}
    @if ($confirmingStatutChange)
        <div class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">





            <div class="bg-gray-900 w-full max-w-md rounded-xl p-6 shadow-xl">
                <h2 class="text-lg font-bold text-gray-100 mb-4">
                    ⚠️ Confirmation
                </h2>

                <p class="text-gray-300 mb-6">
                    Voulez-vous vraiment marquer cet emprunt comme
                    <span class="text-green-400 font-semibold">remboursé</span> ?
                    <br>
                    Cette action est <strong>irréversible</strong>.
                </p>

                <div class="flex justify-end gap-3">
                    <button wire:click="$set('confirmingStatutChange', false)" class="px-4 py-2 bg-gray-700 rounded">
                        Annuler
                    </button>

                    <button wire:click="changeStatut" wire:loading.attr="disabled"
                        class="px-4 py-2 bg-green-600 hover:bg-green-500 text-white rounded">
                        <span wire:loading.remove>Confirmer</span>
                        <span wire:loading>Traitement...</span>
                    </button>
                </div>
            </div>

        </div>
    @endif

    {{-- Fin modale de confirmation de changement de statut --}}
</div>
