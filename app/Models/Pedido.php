<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Lote;

class Pedido extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'cliente_id', 'vendedor_id', 'data_pedido'
    ];

    protected $casts = [
        'item_pedido' => 'array'
    ];

    function item_pedido(){
    	return $this->belongsToMany(Lote::class, 'item_pedido', 'pedido_id', 'lote_id')
    		->with(['produto'])
    		->withPivot('quantidade');
    }
}