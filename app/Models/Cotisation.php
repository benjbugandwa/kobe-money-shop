<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cotisation extends Model
{
    use HasFactory;

    protected $table = 'cotisations';

    protected $fillable = [
        'date_cotisation',
        'montant',
        'libelle',
        'user_id',
        'cycle_id',
    ];

    protected $casts = [
        'date_cotisation' => 'date',
        'montant' => 'decimal:2',
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
}
