<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Rol extends Model
{
    
    protected $table = 'roles';//Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_rol';//Nombre de la columna que identifica cada registro
    protected $keyType = 'int';//Tipo de dato de la clave primaria
    public $incrementing = true;//Indica que la clave primaria es autoincrementable
    //Lista de atributos que se pueden guardar en la tabla
    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];
    public function usuarios()
    {
        return $this->hasMany(User::class, 'id_rol');
    }
}
