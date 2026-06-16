@extends('layouts.app')

@section('content')
    <div class="fixed inset-0 bg-black/70 z-40 flex items-center justify-center px-4">
        <div class="bg-gray-900 border border-gray-800 rounded-xl shadow-xl w-full max-w-lg p-6">
            <div class="mb-6">
                <h1 class="text-xl font-bold text-gray-100">Exporter les donnees</h1>
                <p class="text-sm text-gray-400">Selectionnez un cycle pour telecharger les emprunts et cotisations.</p>
            </div>

            <form method="POST" action="{{ route('admin.export-data.download') }}" x-data="{ loading: false }"
                x-on:submit="loading = true" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm text-gray-400 mb-1">Cycle</label>
                    <select name="cycle_id" required class="w-full rounded bg-gray-800 border-gray-700 text-gray-100">
                        <option value="">Selectionner un cycle</option>
                        @foreach ($cycles as $cycle)
                            <option value="{{ $cycle->id }}" @selected(old('cycle_id') == $cycle->id)>
                                {{ $cycle->num_cycle }} -
                                {{ $cycle->date_debut->format('d/m/Y') }} au {{ $cycle->date_fin->format('d/m/Y') }}
                                ({{ $cycle->statut }})
                            </option>
                        @endforeach
                    </select>
                    @error('cycle_id')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded disabled:opacity-60"
                        x-bind:disabled="loading">
                        <span x-show="!loading">Exporter</span>
                        <span x-show="loading">Generation...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
