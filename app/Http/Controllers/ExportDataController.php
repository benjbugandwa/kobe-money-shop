<?php

namespace App\Http\Controllers;

use App\Models\Cycle;
use App\Models\Cotisation;
use App\Models\Emprunt;
use App\Services\EmpruntPenaltyService;
use App\Services\ExcelExportService;
use Illuminate\Http\Request;
use RuntimeException;

class ExportDataController extends Controller
{
    public function index()
    {
        return view('admin.export-data', [
            'cycles' => Cycle::latest('date_debut')->get(),
        ]);
    }

    public function export(Request $request, ExcelExportService $excelExportService)
    {
        $validated = $request->validate([
            'cycle_id' => 'required|exists:cycles,id',
        ]);

        app(EmpruntPenaltyService::class)->refreshOpenLoans();

        $cycle = Cycle::findOrFail($validated['cycle_id']);
        $cotisations = Cotisation::with('user')
            ->where('cycle_id', $cycle->id)
            ->orderBy('date_cotisation')
            ->get();
        $emprunts = Emprunt::with('user')
            ->where('cycle_id', $cycle->id)
            ->orderBy('date_emprunt')
            ->get();

        try {
            $filePath = $excelExportService->build([
                [
                    'name' => 'Emprunts',
                    'rows' => $this->empruntRows($emprunts),
                ],
                [
                    'name' => 'Cotisations',
                    'rows' => $this->cotisationRows($cotisations),
                ],
            ]);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return response()
            ->download($filePath, 'export_' . $cycle->num_cycle . '.xlsx')
            ->deleteFileAfterSend(true);
    }

    private function empruntRows($emprunts): array
    {
        $rows = [[
            'Membre',
            'Email',
            'Date emprunt',
            'Date echeance',
            'Montant initial',
            'Taux interet',
            'Interets payes',
            'Penalite',
            'Montant final',
            'Statut',
            'Observation',
        ]];

        foreach ($emprunts as $emprunt) {
            $rows[] = [
                $emprunt->user?->name,
                $emprunt->user?->email,
                optional($emprunt->date_emprunt)->format('Y-m-d'),
                optional($emprunt->date_echeance)->format('Y-m-d'),
                $emprunt->montant_initial,
                $emprunt->taux_interet,
                $emprunt->interets_payes,
                $emprunt->montant_penalite,
                $emprunt->montant_final,
                $emprunt->statut_emprunt,
                $emprunt->observation,
            ];
        }

        return $rows;
    }

    private function cotisationRows($cotisations): array
    {
        $rows = [[
            'Membre',
            'Email',
            'Date cotisation',
            'Libelle',
            'Montant',
        ]];

        foreach ($cotisations as $cotisation) {
            $rows[] = [
                $cotisation->user?->name,
                $cotisation->user?->email,
                optional($cotisation->date_cotisation)->format('Y-m-d'),
                $cotisation->libelle,
                $cotisation->montant,
            ];
        }

        return $rows;
    }
}
