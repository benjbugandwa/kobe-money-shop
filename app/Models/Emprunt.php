<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Emprunt extends Model
{
    use HasFactory;

    protected $table = 'emprunts';

    protected $fillable = [
        'date_emprunt',
        'date_echeance',
        'montant_initial',
        'taux_interet',
        'interets_payes',
        'montant_final',
        'montant_penalite',
        'statut_emprunt',
        'observation',
        'user_id',
        'cycle_id',
        'statut_modifie_par',
        'statut_modifie_le',
    ];

    protected $casts = [
        'date_emprunt'      => 'date',
        'date_echeance'     => 'date',
        'montant_initial'   => 'decimal:2',
        'taux_interet'      => 'decimal:2',
            'montant_final'     => 'decimal:2',
            'montant_penalite'  => 'decimal:2',
            'statut_modifie_le' => 'datetime',
    ];

    /* =======================
     |  Relations
     ======================= */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cycle()
    {
        return $this->belongsTo(Cycle::class);
    }

    /* =======================
     |  Accessors (lecture)
     ======================= */

    public function getEstEnRetardAttribute(): bool
    {
        return $this->date_echeance->isPast()
            && $this->statut_emprunt !== 'remboursé';
    }

    public function getMontantTotalDuAttribute(): float
    {
        return (float) (
            $this->montant_final
            + $this->montant_penalite
        );
    }

    /* =======================
     |  Méthodes métier
     ======================= */

    public function calculerMontantFinal(): void
    {
        $this->montant_penalite ??= 0;
        $this->montant_final =
            $this->montant_initial +
            ($this->montant_initial * $this->taux_interet / 100) +
            $this->montant_penalite;
    }

    public function appliquerPenalite(float $montant): void
    {
        $this->montant_penalite += $montant;
        $this->statut_emprunt = 'en_retard';
    }

    public function scopeEnCours($query)
    {
        return $query->where('statut_emprunt', 'en_cours');
    }

    public function scopeRembourses($query)
    {
        return $query->where('statut_emprunt', 'remboursé');
    }

    public function calculerInterets(): float
    {
        return round(
            ($this->montant_initial * $this->taux_interet) / 100,
            2
        );
    }
}
