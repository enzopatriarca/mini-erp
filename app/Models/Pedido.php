<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = ['itens', 'subtotal', 'frete', 'total', 'cep', 'endereco', 'status'];

    protected $casts = ['itens' => 'array'];
}
