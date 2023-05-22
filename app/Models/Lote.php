<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Produto;

class Lote extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'produto_id', 'data_fabricacao', 'quantidade_fabricada', 'quantidade_disponivel', 'valor_unitario'
    ];    

    function produto(){
    	return $this->hasOne(Produto::class, 'id', 'produto_id');
    }

}