<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cupom extends Model
{
    use HasFactory;

    protected $table = 'cupons';

    protected $fillable = [
        'id',
        'codigo',
        'desconto',
        'minimo_subtotal',
        'validade',
    ];

    protected $casts = [
        'desconto'        => 'decimal:2',
        'minimo_subtotal' => 'decimal:2',
        'validade'        => 'date',
    ];
}
