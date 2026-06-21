<?php

namespace App\Models;
//Importamos la clase Model de Eloquent para poder utilizar sus funcionalidades
use Illuminate\Database\Eloquent\Model;

//Clase Asignaturas que representa una asignatura en la aplicación
class Asignaturas extends Model
{
    //Definimos las propiedades de la clase para configurar la conexión con la base de datos
    protected $table = 'asignaturas';//Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_asignatura';//Nombre de la columna que identifica cada registro
    protected $keyType = 'int';//Tipo de dato de la clave primaria
    public $incrementing = true;//Indica que la clave primaria es autoincrementable
    //Lista de atributos que se pueden guardar en la tabla
    protected $fillable = [
        'nombre',
        'estado',
    ];
    //Función que devuelve la relación entre el modelo Asignaturas y el modelo Curso_Asignaturas (una asignatura puede tener varios cursos de asignatura)
    public function curso_asignaturas(){
        return $this->hasMany(Curso_Asignaturas::class, 'id_asignatura', 'id_asignatura');//Devuelve la relación entre el modelo Asignaturas y el modelo Curso_Asignaturas, indicando que una asignatura puede tener varios cursos de asignatura mediante la clave foránea 'id_asignatura' en la tabla 'curso_asignaturas' y la clave primaria 'id_asignatura' en la tabla 'asignaturas'
    }
}
