<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
//Importacion de clases necesarias para el modelo User
use Illuminate\Database\Eloquent\Factories\HasFactory; //Clase para la creación de fábricas de modelos (no utilizada en este caso)
use Illuminate\Foundation\Auth\User as Authenticatable; //Clase base para la autenticación de usuarios
use Illuminate\Notifications\Notifiable; //Clase para enviar notificaciones a los usuarios (no utilizada en este caso)
use Laravel\Sanctum\HasApiTokens; //Clase para la autenticación de tokens de API
use Tymon\JWTAuth\Contracts\JWTSubject; //Interfaz para la implementación de JWT (JSON Web Token) en el modelo User

//Clase User que representa a un usuario en la aplicación y extiende de Authenticatable para la autenticación
class User extends Authenticatable implements JWTSubject
{
    //Uso de traits para agregar funcionalidades al modelo User
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'usuarios'; //Nombre de la tabla en la base de datos asociada al modelo User
    protected $primaryKey = 'id_usuario'; //Nombre de la clave primaria en la tabla 'usuarios'
    protected $keyType = 'int'; //Tipo de dato de la clave primaria (entero) 
    public $incrementing = true; //Indica que la clave primaria es auto-incrementable
    //Definición de los atributos que se pueden asignar masivamente (fillable) en el modelo User (columnas de la tabla 'usuarios' que se pueden llenar mediante asignación masiva)
    protected $fillable = [
        'id_persona',
        'id_rol',
        'username',
        'clave',
        'estado'
    ];
    //Función que devuelve el identificador único del usuario para la generación del token JWT
    public function getJWTIdentifier()
    {
        return (string) $this->id_usuario;//Devuelve el valor de la clave primaria del usuario como cadena de texto
    }
    //Función que devuelve los claims personalizados para el token JWT (en este caso, no se agregan claims personalizados)
    public function getJWTCustomClaims()
    {
        return [];
    }
    //Función que devuelve la relación entre el modelo User y el modelo Rol (un usuario pertenece a un rol)
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');//Devuelve la relación entre el modelo User y el modelo Rol, indicando que un usuario pertenece a un rol específico mediante la clave foránea 'id_rol' en la tabla 'usuarios' y la clave primaria 'id_rol' en la tabla 'roles'
    }
    //Función que devuelve la relación entre el modelo User y la tabla 'personas' (un usuario pertenece a una persona)
    public function persona()
    {
        return $this->belongsTo(Personas::class, 'id_persona', 'id_persona');//Devuelve la relación entre el modelo User y la tabla 'personas', indicando que un usuario pertenece a una persona específica mediante la clave foránea 'id_persona' en la tabla 'usuarios' y la clave primaria 'id_persona' en la tabla 'personas'
    }
}
