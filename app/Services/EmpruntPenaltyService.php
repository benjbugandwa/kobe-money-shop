<?php

namespace App\Services;

use App\Models\Emprunt;
use Carbon\CarbonInterface;

class EmpruntPenaltyService
{
    public function refreshOpenLoans(?CarbonInterface $today = null): int
    {
        $updated = 0;

        Emprunt::whereIn('statut_emprunt', ['en_cours', 'en_retard'])
            ->get()
            ->each(function (Emprunt $emprunt) use ($today, &$updated): void {
                if ($this->refresh($emprunt, $today)) {
                    $updated++;
                }
            });

        return $updated;
    }

    public function refresh(Emprunt $emprunt, ?CarbonInterface $today = null): bool
    {
        if ($emprunt->statut_emprunt === 'remboursé' || ! $emprunt->date_echeance) {
            return false;
        }

        $today ??= now();
        $daysLate = $emprunt->date_echeance->diffInDays($today, false);
        $latePeriods = $daysLate >= 30 ? intdiv((int) $daysLate, 30) : 0;

        $penalty = round((float) $emprunt->montant_initial * 0.10 * $latePeriods, 2);
        $interest = round((float) $emprunt->montant_initial * (float) $emprunt->taux_interet / 100, 2);
        $finalAmount = round((float) $emprunt->montant_initial + $interest + $penalty, 2);
        $status = $penalty > 0 ? 'en_retard' : 'en_cours';

        if (
            (float) $emprunt->montant_penalite === $penalty
            && (float) $emprunt->montant_final === $finalAmount
            && $emprunt->statut_emprunt === $status
        ) {
            return false;
        }

        $emprunt->forceFill([
            'montant_penalite' => $penalty,
            'montant_final' => $finalAmount,
            'statut_emprunt' => $status,
        ])->save();

        return true;
    }
}
