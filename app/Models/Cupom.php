<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cupom extends Model
{
    use HasFactory;

    protected $table = 'cupons';

    protected $fillable = ['codigo', 'desconto', 'minimo_subtotal', 'validade'];

    protected $dates = ['validade'];
}
