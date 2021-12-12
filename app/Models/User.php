<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens; //utilizando o passport dentro de user

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function vendedor()
    {
        return $this->hasMany(Vendedor::class);
    }

    public function produto()
    {
        return $this->hasMany(Produto::class);
    }

    public function cliente()
    {
        return $this->hasMany(Cliente::class);
    }

    public function lote()
    {
        return $this->hasMany(Lote::class);
    }

    public function pedido()
    {
        return $this->hasMany(Pedido::class);
    }
/*
    //dentro de order
    public function customer()
    {
        return $this->hasOne(Custumer::class);
    }

    //dentro de customer
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    //dentro de order
    public function products()
    {
        return $this->hasMany(product::class);
    }    */

}
