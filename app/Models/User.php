<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Tymon\JWTAuth\Contracts\JWTSubject;
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    protected $keyType = 'int';
    public $incrementing = true;
    protected $fillable = [
        'id_persona',
        'id_rol',
        'username',
        'clave',
        'estado'
    ];
   public function getJWTIdentifier()
    {
        return (string) $this->id_usuario;
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }
    public function persona()
    {
        return $this->belongsTo(Personas::class, 'id_persona', 'id_persona');
    }
}
