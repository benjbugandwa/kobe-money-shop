<div class="max-w-6xl mx-auto py-8">

    <h1 class="text-2xl font-bold text-gray-100 mb-6">
        👤 Gestion des utilisateurs
    </h1>

    <div class="bg-gray-900 rounded-xl shadow-lg overflow-hidden">
        <table class="w-full text-sm text-gray-300">
            <thead class="bg-gray-800 text-gray-400">
                <tr>
                    <th class="px-6 py-3 text-left">Nom</th>
                    <th class="px-6 py-3 text-left">Email</th>
                    <th class="px-6 py-3 text-left">Rôle</th>
                    <th class="px-6 py-3 text-center">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($users as $user)
                    <tr class="border-b border-gray-800 hover:bg-gray-800/50">
                        <td class="px-6 py-4">{{ $user->name }}</td>
                        <td class="px-6 py-4">{{ $user->email }}</td>

                        <td class="px-6 py-4">
                            @if ($user->user_role === 'admin')
                                <span class="px-2 py-1 bg-green-600/20 text-green-400 rounded text-xs">
                                    Admin
                                </span>
                            @else
                                <span class="px-2 py-1 bg-gray-600/20 text-gray-400 rounded text-xs">
                                    Membre
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-center">
                            <button wire:click="confirmToggleRole({{ $user->id }})"
                                class="px-3 py-1 text-xs rounded bg-indigo-600 hover:bg-indigo-700 text-white">
                                Changer rôle
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>


    <!-- Confirmation Modal -->
    @if ($confirmingUserId)
        <div class="fixed inset-0 flex items-center justify-center bg-black/60 z-50">
            <div class="bg-gray-900 rounded-xl p-6 w-full max-w-md shadow-xl">

                <h2 class="text-lg font-semibold text-white mb-4">
                    Confirmation requise
                </h2>

                <p class="text-gray-300 mb-6">
                    Voulez-vous vraiment changer le rôle de
                    <span class="font-bold text-white">
                        {{ $confirmingUserName }}
                    </span> ?
                </p>

                <div class="flex justify-end space-x-3">
                    <button wire:click="$set('confirmingUserId', null)"
                        class="px-4 py-2 rounded bg-gray-700 hover:bg-gray-600 text-gray-200">
                        Annuler
                    </button>

                    <button wire:click="toggleRole" class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white">
                        Confirmer
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Fin confirmation Modal -->

</div>
