<?php

namespace App\Models;
//Importación de clases necesarias para el modelo Calificaciones
use Illuminate\Database\Eloquent\Model;

//Clase Calificaciones que representa una calificación en la aplicación
class Calificaciones extends Model
{
    //Definición de las propiedades del modelo Calificaciones
    protected $table = 'calificaciones';//Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_calificacion';//Nombre de la columna que identifica cada registro
    protected $keyType = 'int';//Tipo de dato de la clave primaria
    public $incrementing = true;//Indica que la clave primaria es autoincrementable
    public $timestamps = false;//Indica que no se guardan las columnas de tiempo de creación y actualización
    //Lista de atributos que se pueden guardar en la tabla
    protected $fillable = [
        'id_matricula',
        'id_curso_asignatura',
        'q1_p1',
        'q1_p2',
        'q1_p3',
        'q1_examen',
        'q1_promedio',
        'q2_p1',
        'q2_p2',
        'q2_p3',
        'q2_examen',
        'q2_promedio',
        'promedio_anual',
        'nota_supletorio',
        'nota_remedial',
        'nota_gracia',
        'nota_final_definitiva',
        'estado_asignatura',
    ];
    //Función que devuelve la relación entre el modelo Calificaciones y el modelo Matriculas (una calificación puede pertenecer a un matricula)
    public function matricula(){
        return $this->belongsTo(Matriculas::class, 'id_matricula', 'id_matricula');//Devuelve la relación entre el modelo Calificaciones y el modelo Matriculas, indicando que una calificación puede pertenecer a un matricula mediante la clave foránea 'id_matricula' en la tabla 'calificaciones' y la clave primaria 'id_matricula' en la tabla 'matriculas'
    }
    //Función que devuelve la relación entre el modelo Calificaciones y el modelo Curso_Asignaturas (una calificación puede pertenecer a un curso de asignatura)
    public function curso_asignatura(){
        return $this->belongsTo(Curso_Asignaturas::class, 'id_curso_asignatura', 'id_curso_asignatura');//Devuelve la relación entre el modelo Calificaciones y el modelo Curso_Asignaturas, indicando que una calificación puede pertenecer a un curso de asignatura mediante la clave foránea 'id_curso_asignatura' en la tabla 'calificaciones' y la clave primaria 'id_curso_asignatura' en la tabla 'curso_asignaturas'
    }
}
