<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Boleto extends Model
{
    protected $fillable = [
        'cliente_id',
        'desc',
        'valor',
        'vencimento',
        'status',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
