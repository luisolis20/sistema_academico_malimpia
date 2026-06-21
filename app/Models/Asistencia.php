<?php

namespace App\Models;
//Importamos la clase Model de Eloquent para poder usar sus funcionalidades
use Illuminate\Database\Eloquent\Model;

//Clase Asistencia que representa una asistencia en la aplicación
class Asistencia extends Model
{
    //Definimos las propiedades de la clase para configurar la conexión con la base de datos
    protected $table = 'asistencia';//Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_asistencia';//Nombre de la columna que identifica cada registro
    protected $keyType = 'int';//Tipo de dato de la clave primaria
    public $incrementing = true;//Indica que la clave primaria es autoincrementable
    public $timestamps = false;//Indica que no se guardan las columnas de tiempo de creación y actualización
    //Lista de atributos que se pueden guardar en la tabla
    protected $fillable = [
        'id_matricula',
        'id_curso_asignatura',
        'fecha',//Solo de tipo DATE
        'estado', //Estados disponibles: Presente, Ausente, Justificado
        'url_justificativo',
    ];
    //Definimos las relaciones con otras tablas utilizando Eloquent
    public function matricula(){
        return $this->belongsTo(Matriculas::class, 'id_matricula', 'id_matricula');//Devuelve la relación entre el modelo Asistencia y el modelo Matriculas, indicando que una asistencia puede pertenecer a un matricula mediante la clave foránea 'id_matricula' en la tabla 'asistencia' y la clave primaria 'id_matricula' en la tabla 'matriculas'
    }
    //Función que devuelve la relación entre el modelo Asistencia y el modelo Curso_Asignaturas (una asistencia puede pertenecer a un curso de asignatura)
    public function curso_asignatura(){
        return $this->belongsTo(Curso_Asignaturas::class, 'id_curso_asignatura', 'id_curso_asignatura');//Devuelve la relación entre el modelo Asistencia y el modelo Curso_Asignaturas, indicando que una asistencia puede pertenecer a un curso de asignatura mediante la clave foránea 'id_curso_asignatura' en la tabla 'asistencia' y la clave primaria 'id_curso_asignatura' en la tabla 'curso_asignaturas'
    }
}
