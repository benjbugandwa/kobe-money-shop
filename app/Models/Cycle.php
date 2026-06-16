<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Cycle extends Model
{
    use HasFactory;

    public const STATUT_EN_COURS = 'En cours';
    public const STATUT_CLOTURE = 'Cloturé';

    protected $fillable = [
        'num_cycle',
        'date_debut',
        'date_fin',
        'created_by',
        'statut',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Cycle $cycle): void {
            $cycle->num_cycle ??= static::generateNextNumber();
            $cycle->created_by ??= Auth::id();
            $cycle->statut ??= self::STATUT_EN_COURS;
        });
    }

    public static function generateNextNumber(): string
    {
        $lastId = (int) static::max('id');

        return 'CYL-' . str_pad((string) ($lastId + 1), 4, '0', STR_PAD_LEFT);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cotisations()
    {
        return $this->hasMany(Cotisation::class);
    }

    public function emprunts()
    {
        return $this->hasMany(Emprunt::class);
    }

    public function scopeEnCours($query)
    {
        return $query->where('statut', self::STATUT_EN_COURS);
    }

    public static function current(): ?self
    {
        return static::enCours()->latest('date_debut')->first();
    }
}
