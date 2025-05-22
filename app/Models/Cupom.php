<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cupom extends Model
{
    protected $fillable = ['codigo', 'desconto', 'minimo_subtotal', 'validade'];

    protected $dates = ['validade'];
}
