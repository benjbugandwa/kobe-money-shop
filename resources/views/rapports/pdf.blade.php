<h2 style="text-align:center;">Rapport financier</h2>

<p><strong>Membre :</strong> {{ $user->name }}</p>
<p><strong>Période :</strong> {{ $periode['date_debut'] }} → {{ $periode['date_fin'] }}</p>
<p><strong>Généré le :</strong> {{ $dateGen->format('d/m/Y H:i') }}</p>

<hr>

<h4>Cotisations</h4>
<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>Date</th>
            <th>Montant</th>
            <th>Libellé</th>
        </tr>
    </thead>
    <tbody>
        @forelse($cotisations as $c)
            <tr>
                <td>{{ $c->date_cotisation }}</td>
                <td>{{ $c->montant }} FC</td>
                <td>{{ $c->libelle }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3">Aucune cotisation</td>
            </tr>
        @endforelse
    </tbody>
</table>

<br>

<h4>Emprunts en cours</h4>
<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>Date</th>
            <th>Montant</th>
            <th>Échéance</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>
        @forelse($emprunts as $e)
            <tr>
                <td>{{ $e->date_emprunt }}</td>
                <td>{{ $e->montant_initial }} FC</td>
                <td>{{ $e->date_echeance }}</td>
                <td>{{ $e->statut_emprunt }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4">Aucun emprunt en cours</td>
            </tr>
        @endforelse
    </tbody>
</table>
