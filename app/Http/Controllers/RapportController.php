<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Cotisation;
use App\Models\Emprunt;
use Illuminate\Support\Facades\Auth;

class RapportController extends Controller
{
    public function index()
    {
        return view('rapports.index');
    }

    public function generatePdf(Request $request)
    {
        $request->validate([
            'date_debut' => 'required|date',
            'date_fin'   => 'required|date|after_or_equal:date_debut',
        ]);

        $user = Auth::user();

        $cotisations = Cotisation::where('user_id', $user->id)
            ->whereBetween('date_cotisation', [
                $request->date_debut,
                $request->date_fin
            ])->get();

        $emprunts = Emprunt::where('user_id', $user->id)
            ->where('statut_emprunt', 'en_cours')
            ->whereBetween('date_emprunt', [
                $request->date_debut,
                $request->date_fin
            ])->get();

        $pdf = Pdf::loadView('rapports.pdf', [
            'user'        => $user,
            'cotisations' => $cotisations,
            'emprunts'    => $emprunts,
            'dateGen'     => now(),
            'periode'     => $request->only('date_debut', 'date_fin'),
        ])->setPaper('A4');

        return $pdf->download('rapport_' . $user->name . '.pdf');
    }
}
