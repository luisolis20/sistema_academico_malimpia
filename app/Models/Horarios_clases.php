<?php

namespace App\Models;
//Importación de clases necesarias para el modelo Horarios_clases   
use Illuminate\Database\Eloquent\Model;

//Clase Horarios_clases que representa un horario de clase en la aplicación
class Horarios_clases extends Model
{
    //Definición de las propiedades del modelo Horarios_clases
    protected $table = 'horarios_clases';//Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_horario';//Nombre de la columna que identifica cada registro
    protected $keyType = 'int';//Tipo de dato de la clave primaria
    public $incrementing = true;//Indica que la clave primaria es autoincrementable
    public $timestamps = false;
    //Lista de atributos que se pueden guardar en la tabla
    protected $fillable = [
        'id_curso_asignatura',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
    ];
    //Función que devuelve la relación entre el modelo Horarios_clases y el modelo Curso_Asignaturas (un horario de clase puede pertenecer a un curso de asignatura)
    public function curso_asignatura(){
        return $this->belongsTo(Curso_Asignaturas::class, 'id_curso_asignatura', 'id_curso_asignatura');//Devuelve la relación entre el modelo Horarios_clases y el modelo Curso_Asignaturas, indicando que un horario de clase puede pertenecer a un curso de asignatura mediante la clave foránea 'id_curso_asignatura' en la tabla 'horarios_clases' y la clave primaria 'id_curso_asignatura' en la tabla 'curso_asignaturas'
    }
}
