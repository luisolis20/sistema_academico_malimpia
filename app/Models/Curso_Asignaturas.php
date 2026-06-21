<?php

namespace App\Models;
//Importamos la clase Model de Eloquent para poder utilizar sus funcionalidades
use Illuminate\Database\Eloquent\Model;

//Clase Curso_Asignaturas que representa una asignatura en la aplicación
class Curso_Asignaturas extends Model
{
    //Definimos las propiedades de la clase para configurar la conexión con la base de datos
    protected $table = 'curso_asignatura';//Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_curso_asignatura';//Nombre de la columna que identifica cada registro
    protected $keyType = 'int';//Tipo de dato de la clave primaria
    public $incrementing = true;//Indica que la clave primaria es autoincrementable
    //Lista de atributos que se pueden guardar en la tabla
    protected $fillable = [
        'id_curso',
        'id_asignatura',
        'id_docente',
        'horas_semanales',
        'estado',
    ];
    //Función que devuelve la relación entre el modelo Curso_Asignaturas y el modelo Cursos (una asignatura puede pertenecer a un curso)
    public function curso(){
        return $this->belongsTo(Cursos::class, 'id_curso', 'id_curso');//Devuelve la relación entre el modelo Curso_Asignaturas y el modelo Cursos, indicando que una asignatura puede pertenecer a un curso mediante la clave foránea 'id_curso' en la tabla 'curso_asignaturas' y la clave primaria 'id_curso' en la tabla 'cursos'
    }
    //Función que devuelve la relación entre el modelo Curso_Asignaturas y el modelo Asignaturas (una asignatura puede pertenecer a una asignatura)
    public function asignatura(){
        return $this->belongsTo(Asignaturas::class, 'id_asignatura', 'id_asignatura');//Devuelve la relación entre el modelo Curso_Asignaturas y el modelo Asignaturas, indicando que una asignatura puede pertenecer a una asignatura mediante la clave foránea 'id_asignatura' en la tabla 'curso_asignaturas' y la clave primaria 'id_asignatura' en la tabla 'asignaturas'
    }
    //Función que devuelve la relación entre el modelo Curso_Asignaturas y el modelo Personas (un docente puede pertenecer a varios cursos de asignatura)
    public function docente(){
        return $this->belongsTo(Personas::class, 'id_docente', 'id_persona');//Devuelve la relación entre el modelo Curso_Asignaturas y el modelo Personas, indicando que un docente puede pertenecer a varios cursos de asignatura mediante la clave foránea 'id_docente' en la tabla 'curso_asignaturas' y la clave primaria 'id_persona' en la tabla 'personas'
    }
    //Función que devuelve la relación entre el modelo Curso_Asignaturas y el modelo Horarios_clases (un horario de clase puede pertenecer a varios cursos de asignatura)
    public function horarios_clases(){
        return $this->hasMany(Horarios_clases::class, 'id_curso_asignatura');//Devuelve la relación entre el modelo Curso_Asignaturas y el modelo Horarios_clases, indicando que un horario de clase puede pertenecer a varios cursos de asignatura mediante la clave foránea 'id_curso_asignatura' en la tabla 'horarios_clases' y la clave primaria 'id_curso_asignatura' en la tabla 'curso_asignaturas'
    }
    //Función que devuelve la relación entre el modelo Curso_Asignaturas y el modelo Asistencia (un asistente puede pertenecer a varios cursos de asignatura)
    public function asistencias(){
        return $this->hasMany(Asistencia::class, 'id_curso_asignatura', 'id_curso_asignatura');//Devuelve la relación entre el modelo Curso_Asignaturas y el modelo Asistencia, indicando que un asistente puede pertenecer a varios cursos de asignatura mediante la clave foránea 'id_curso_asignatura' en la tabla 'asistencia' y la clave primaria 'id_curso_asignatura' en la tabla 'curso_asignaturas'
    }
    //Función que devuelve la relación entre el modelo Curso_Asignaturas y el modelo Calificaciones (un estudiante puede tener varias calificaciones en un curso de asignatura)
    public function calificaciones(){
        return $this->hasMany(Calificaciones::class, 'id_curso_asignatura', 'id_curso_asignatura');//Devuelve la relación entre el modelo Curso_Asignaturas y el modelo Calificaciones, indicando que un estudiante puede tener varias calificaciones en un curso de asignatura mediante la clave foránea 'id_curso_asignatura' en la tabla 'calificaciones' y la clave primaria 'id_curso_asignatura' en la tabla 'curso_asignaturas'
    }
}
