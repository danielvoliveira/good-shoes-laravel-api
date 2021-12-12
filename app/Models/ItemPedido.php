<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPedido extends Model
{
    use HasFactory;

    $table = 'item_pedido';

    protected $fillable = [
        'lote_id', 'pedido_id', 'quantidade'
    ];    
}