<div class="max-w-7xl mx-auto py-8">
    <h1 class="text-2xl font-bold text-gray-100 mb-6">
        Gestion des emprunts
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
            Aucun cycle en cours. Les nouveaux emprunts ne peuvent pas etre enregistres.
        </div>
    @endif

    <div class="flex flex-wrap gap-4 mb-6">
        <div>
            <label class="text-xs text-gray-400">Statut</label>
            <select wire:model.live="filterStatut"
                class="bg-gray-800 border-gray-700 text-gray-300 rounded px-3 py-2 text-sm">
                <option value="">Tous</option>
                <option value="en_cours">En cours</option>
                <option value="en_retard">En retard</option>
                <option value="remboursé">Rembourse</option>
            </select>
        </div>

        <div>
            <label class="text-xs text-gray-400">Membre</label>
            <select wire:model.live="filterUser"
                class="bg-gray-800 border-gray-700 text-gray-300 rounded px-3 py-2 text-sm">
                <option value="">Tous</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div wire:loading class="text-sm text-gray-400 mb-4">
        Chargement...
    </div>

    <div class="bg-gray-900 rounded-xl shadow-lg overflow-hidden">
        <div class="flex justify-end mb-4">
            <button wire:click="openModal" wire:loading.attr="disabled"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg disabled:opacity-60">
                <span wire:loading.remove wire:target="openModal">Nouvel emprunt</span>
                <span wire:loading wire:target="openModal">Ouverture...</span>
            </button>
        </div>

        <table class="w-full text-sm text-gray-300">
            <thead class="bg-gray-800 text-gray-400">
                <tr>
                    <th class="px-6 py-3 text-left">Membre</th>
                    <th class="px-6 py-3 text-left">Cycle</th>
                    <th class="px-6 py-3 text-left">Montant initial</th>
                    <th class="px-6 py-3 text-left">Taux (%)</th>
                    <th class="px-6 py-3 text-left">Penalite</th>
                    <th class="px-6 py-3 text-left">Montant final</th>
                    <th class="px-6 py-3 text-left">Date emprunt</th>
                    <th class="px-6 py-3 text-left">Echeance</th>
                    <th class="px-6 py-3 text-left">Statut</th>
                    <th class="px-6 py-3 text-left">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($emprunts as $emprunt)
                    <tr class="border-b border-gray-800 hover:bg-gray-800/50">
                        <td class="px-6 py-4">{{ $emprunt->user->name }}</td>
                        <td class="px-6 py-4 text-gray-400">{{ $emprunt->cycle?->num_cycle ?? '-' }}</td>
                        <td class="px-6 py-4 font-semibold text-yellow-400">
                            {{ number_format($emprunt->montant_initial, 0, ',', ' ') }} USD
                        </td>
                        <td class="px-6 py-4">{{ $emprunt->taux_interet }} %</td>
                        <td class="px-6 py-4 font-semibold text-red-300">
                            {{ number_format($emprunt->montant_penalite, 0, ',', ' ') }} USD
                        </td>
                        <td class="px-6 py-4 font-semibold text-green-400">
                            {{ number_format($emprunt->montant_final, 0, ',', ' ') }} USD
                        </td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($emprunt->date_emprunt)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($emprunt->date_echeance)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            @if ($emprunt->statut_emprunt === 'en_cours')
                                <span class="px-3 py-1 rounded-full text-xs bg-yellow-500/20 text-yellow-400">En cours</span>
                            @elseif ($emprunt->statut_emprunt === 'en_retard')
                                <span class="px-3 py-1 rounded-full text-xs bg-red-500/20 text-red-400">En retard</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs bg-green-500/20 text-green-400">Rembourse</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if (in_array($emprunt->statut_emprunt, ['en_cours', 'en_retard'], true))
                                <button wire:click="confirmStatutChange({{ $emprunt->id }})"
                                    class="text-sm text-orange-400 hover:text-orange-300">
                                    Marquer rembourse
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-6 py-6 text-center text-gray-400">
                            Aucun emprunt enregistre.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $emprunts->links() }}
    </div>

    @if ($showModal)
        <div class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 px-4">
            <div class="bg-gray-900 w-full max-w-2xl rounded-xl p-6 shadow-xl">
                <h2 class="text-lg font-bold text-gray-100 mb-4">
                    Nouvel emprunt
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2 bg-gray-800/70 rounded-lg p-4">
                        <p class="text-sm text-gray-400">
                            Solde actuel de la caisse :
                            <span class="font-semibold text-green-400">
                                {{ number_format($this->soldeCaisse(), 0, ',', ' ') }} USD
                            </span>
                        </p>
                        @if ($cycleEnCours)
                            <p class="text-sm text-gray-400 mt-1">
                                Cycle :
                                <span class="font-semibold text-indigo-300">{{ $cycleEnCours->num_cycle }}</span>
                            </p>
                        @endif
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm text-gray-400">Membre</label>
                        <select wire:model="user_id" class="w-full bg-gray-800 border-gray-700 rounded">
                            <option value="">Selectionner</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="text-red-400 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm text-gray-400">Date emprunt</label>
                        <input type="date" wire:model="date_emprunt" max="{{ now()->toDateString() }}"
                            class="w-full bg-gray-800 border-gray-700 rounded">
                        @error('date_emprunt')
                            <p class="text-red-400 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm text-gray-400">Date echeance</label>
                        <input type="date" wire:model="date_echeance" class="w-full bg-gray-800 border-gray-700 rounded">
                        @error('date_echeance')
                            <p class="text-red-400 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm text-gray-400">Montant initial</label>
                        <input type="number" wire:model="montant_initial" min="20" step="0.01"
                            class="w-full bg-gray-800 border-gray-700 rounded">
                        @error('montant_initial')
                            <p class="text-red-400 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm text-gray-400">Taux (%)</label>
                        <input type="number" wire:model="taux_interet" min="0" step="0.01"
                            class="w-full bg-gray-800 border-gray-700 rounded">
                        @error('taux_interet')
                            <p class="text-red-400 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm text-gray-400">Montant final</label>
                        <input type="number" wire:model="montant_final" readonly
                            class="w-full bg-gray-800 border-gray-700 rounded text-green-400 font-semibold">
                        @error('montant_final')
                            <p class="text-red-400 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm text-gray-400">Observation</label>
                        <textarea wire:model="observation" rows="3" class="w-full bg-gray-800 border-gray-700 rounded"></textarea>
                        @error('observation')
                            <p class="text-red-400 text-xs">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="$set('showModal', false)" class="px-4 py-2 bg-gray-700 rounded">
                        Annuler
                    </button>
                    <button wire:click="save" wire:loading.attr="disabled"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded disabled:opacity-60">
                        <span wire:loading.remove wire:target="save">Enregistrer</span>
                        <span wire:loading wire:target="save">Enregistrement...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($confirmingStatutChange)
        <div class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 px-4">
            <div class="bg-gray-900 w-full max-w-md rounded-xl p-6 shadow-xl">
                <h2 class="text-lg font-bold text-gray-100 mb-4">
                    Confirmation
                </h2>

                <p class="text-gray-300 mb-6">
                    Voulez-vous vraiment marquer cet emprunt comme rembourse ?
                    Cette action est irreversible.
                </p>

                <div class="flex justify-end gap-3">
                    <button wire:click="$set('confirmingStatutChange', false)" class="px-4 py-2 bg-gray-700 rounded">
                        Annuler
                    </button>
                    <button wire:click="changeStatut" wire:loading.attr="disabled"
                        class="px-4 py-2 bg-green-600 hover:bg-green-500 text-white rounded disabled:opacity-60">
                        <span wire:loading.remove wire:target="changeStatut">Confirmer</span>
                        <span wire:loading wire:target="changeStatut">Traitement...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
