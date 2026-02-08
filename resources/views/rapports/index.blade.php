@extends('layouts.app')

@section('content')
    <div class="w-full max-w-lg mx-auto">

        <div class="bg-gray-900 border border-gray-800 rounded-xl shadow-lg p-6">

            <h2 class="text-xl font-bold text-gray-100 mb-6 flex items-center gap-2">
                📄 Génération du rapport
            </h2>

            <form method="POST" action="{{ route('rapports.pdf') }}" class="space-y-5">
                @csrf

                {{-- Date début --}}
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">
                        Date de début
                    </label>
                    <input type="date" name="date_debut"
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2
                           text-gray-100 focus:ring focus:ring-indigo-500 focus:border-indigo-500"
                        value="{{ now()->startOfMonth()->toDateString() }}" required>
                </div>

                {{-- Date fin --}}
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">
                        Date de fin
                    </label>
                    <input type="date" name="date_fin"
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2
                           text-gray-100 focus:ring focus:ring-indigo-500 focus:border-indigo-500"
                        value="{{ now()->toDateString() }}" required>
                </div>

                {{-- Bouton --}}
                <div class="pt-4">
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2
                           bg-red-600 hover:bg-red-700 text-white
                           font-semibold py-2.5 rounded-lg transition">
                        📥 Générer le PDF
                    </button>
                </div>
            </form>

            <p class="text-xs text-gray-400 text-center mt-6">
                Le rapport inclura vos cotisations et emprunts sur la période sélectionnée.
            </p>

        </div>

    </div>
@endsection
