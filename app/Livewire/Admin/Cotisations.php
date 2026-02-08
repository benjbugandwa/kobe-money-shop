<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Cotisation;

class Cotisations extends Component
{
    use WithPagination;

    //Proprietes de filtre
    public $userId = '';
    public $dateDebut = '';
    public $dateFin = '';

    //Proprietes de creation
    public $showCreateModal = false;

    public $newUserId = '';
    public $newDateCotisation = '';
    public $newMontant = '';
    public $newLibelle = '';

    protected $paginationTheme = 'tailwind';

    public function render()
    {
        $query = \App\Models\Cotisation::with('user');

        // Filtre par membre
        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }

        // Filtre date début
        if ($this->dateDebut) {
            $query->whereDate('date_cotisation', '>=', $this->dateDebut);
        }

        // Filtre date fin
        if ($this->dateFin) {
            $query->whereDate('date_cotisation', '<=', $this->dateFin);
        }

        return view('livewire.admin.cotisations', [
            'cotisations' => $query
                ->orderBy('date_cotisation', 'desc')
                ->paginate(10),

            'users' => \App\Models\User::orderBy('name')->get(),
        ]);
    }

    public function updated($property)
    {
        if (in_array($property, ['userId', 'dateDebut', 'dateFin'])) {
            $this->resetPage();
        }
    }

    public function resetFilters()
    {
        $this->reset(['userId', 'dateDebut', 'dateFin']);
        $this->resetPage();
    }


    // Autres méthodes pour la création des cotisations

    //Validation des données

    protected function rules()
    {
        return [
            'newUserId' => 'required|exists:users,id',
            'newDateCotisation' => 'required|date|before_or_equal:today',
            'newMontant' => 'required|numeric|min:5',
            'newLibelle' => 'required|string|min:3|max:255',
        ];
    }

    protected function messages()
    {
        return [
            'newUserId.required' => 'Veuillez sélectionner un membre.',
            'newUserId.exists'   => 'Le membre sélectionné est invalide.',

            'newDateCotisation.required' =>
            'La date de cotisation est obligatoire.',
            'newDateCotisation.date' =>
            'La date de cotisation n’est pas valide.',
            'newDateCotisation.before_or_equal' =>
            'La date de cotisation ne peut pas être ultérieure à aujourd’hui.',

            'newMontant.required' =>
            'Le montant de la cotisation est obligatoire.',
            'newMontant.numeric' =>
            'Le montant doit être un nombre.',
            'newMontant.min' =>
            'Le montant minimum de cotisation est de 5 USD.',

            'newLibelle.required' =>
            'Le libellé est obligatoire.',
            'newLibelle.min' =>
            'Le libellé doit contenir au moins 3 caractères.',
            'newLibelle.max' =>
            'Le libellé ne peut pas dépasser 255 caractères.',
        ];
    }






    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset([
            'newUserId',
            'newDateCotisation',
            'newMontant',
            'newLibelle',
        ]);

        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    /*
    public function saveCotisation()
    {
        $this->validate();

        \App\Models\Cotisation::create([
            'user_id' => $this->newUserId,
            'date_cotisation' => $this->newDateCotisation,
            'montant' => $this->newMontant,
            'libelle' => $this->newLibelle,
        ]);

        $this->showCreateModal = false;

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Cotisation ajoutée avec succès.'
        ]);

        $this->resetPage();
    }
        */

    public function save()
    {
        $this->validate();

        \App\Models\Cotisation::create([
            'user_id' => $this->newUserId,
            'date_cotisation' => $this->newDateCotisation,
            'montant' => $this->newMontant,
            'libelle' => $this->newLibelle,
        ]);

        $this->showCreateModal = false;

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Cotisation ajoutée avec succès.'
        ]);

        $this->resetPage();
    }
}
