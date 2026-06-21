<?php

namespace App\Models;
//Importación de clases necesarias para el modelo Rol
use Illuminate\Database\Eloquent\Model;//Clase base para la creación de modelos en Laravel

//Clase Rol que representa un rol en la aplicación
class Rol extends Model
{
    //Uso de traits para agregar funcionalidades al modelo Rol
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
    //Función que devuelve la relación entre el modelo Rol y la tabla 'usuarios' (un rol pertenece a varios usuarios)
    public function usuarios()
    {
        return $this->hasMany(User::class, 'id_rol');//Devuelve la relación entre el modelo Rol y la tabla 'usuarios', indicando que un rol pertenece a varios usuarios mediante la clave foránea 'id_rol' en la tabla 'roles' y la clave primaria 'id_usuario' en la tabla 'usuarios'
    }
}
