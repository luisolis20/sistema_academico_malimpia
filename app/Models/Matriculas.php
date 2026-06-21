<?php

namespace App\Models;
//Importación de clases necesarias para el modelo Matriculas
use Illuminate\Database\Eloquent\Model;

//Clase Matriculas que representa una matrícula en la aplicación
class Matriculas extends Model
{
    //Uso de traits para agregar funcionalidades al modelo Matriculas
    protected $table = 'matriculas';//Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_matricula';//Nombre de la columna que identifica cada registro
    protected $keyType = 'int';//Tipo de dato de la clave primaria
    public $incrementing = true;//Indica que la clave primaria es autoincrementable
    public $timestamps = false;
    //Lista de atributos que se pueden guardar en la tabla
    protected $fillable = [
        'id_estudiante',
        'id_curso',
        'id_representante',
        'fecha_matricula',
        'es_nuevo',
        'estado',
    ];
    //Función que devuelve la relación entre el modelo Matriculas y el modelo Personas (una matrícula puede pertenecer a un estudiante)
    public function estudiante(){
        
        return $this->belongsTo(Personas::class, 'id_estudiante', 'id_persona');//Devuelve la relación entre el modelo Matriculas y el modelo Personas, indicando que una matrícula puede pertenecer a un estudiante mediante la clave foránea 'id_estudiante' en la tabla 'matriculas' y la clave primaria 'id_persona' en la tabla 'personas'
    }
    //Función que devuelve la relación entre el modelo Matriculas y el modelo Cursos (una matrícula puede pertenecer a un curso)
    public function curso(){
        return $this->belongsTo(Cursos::class, 'id_curso', 'id_curso');//Devuelve la relación entre el modelo Matriculas y el modelo Cursos, indicando que una matrícula puede pertenecer a un curso mediante la clave foránea 'id_curso' en la tabla 'matriculas' y la clave primaria 'id_curso' en la tabla 'cursos'
    }
    //Función que devuelve la relación entre el modelo Matriculas y el modelo Personas (una matrícula puede pertenecer a un representante)
    public function representante(){
        return $this->belongsTo(Personas::class, 'id_representante', 'id_persona');//Devuelve la relación entre el modelo Matriculas y el modelo Personas, indicando que una matrícula puede pertenecer a un representante mediante la clave foránea 'id_representante' en la tabla 'matriculas' y la clave primaria 'id_persona' en la tabla 'personas'
    }
    //Función que devuelve la relación entre el modelo Matriculas y el modelo Asistencia (una matrícula puede tener varias asistencias) 
    public function asistencias(){
        return $this->hasMany(Asistencia::class, 'id_matricula', 'id_matricula');//Devuelve la relación entre el modelo Matriculas y el modelo Asistencia, indicando que una matrícula puede tener varias asistencias mediante la clave foránea 'id_matricula' en la tabla 'asistencias' y la clave primaria 'id_matricula' en la tabla 'matriculas'
    }
    //Función que devuelve la relación entre el modelo Matriculas y el modelo Calificaciones (una matrícula puede tener varias calificaciones)
    public function calificaciones(){
        return $this->hasMany(Calificaciones::class, 'id_matricula', 'id_matricula');//Devuelve la relación entre el modelo Matriculas y el modelo Calificaciones, indicando que una matrícula puede tener varias calificaciones mediante la clave foránea 'id_matricula' en la tabla 'calificaciones' y la clave primaria 'id_matricula' en la tabla 'matriculas'
    }
    //Función que devuelve la relación entre el modelo Matriculas y el modelo Conducta (una matrícula puede tener varias conductas)
    public function conductas(){
        return $this->hasMany(Conducta::class, 'id_matricula', 'id_matricula');//Devuelve la relación entre el modelo Matriculas y el modelo Conducta, indicando que una matrícula puede tener varias conductas mediante la clave foránea 'id_matricula' en la tabla 'conductas' y la clave primaria 'id_matricula' en la tabla 'matriculas'
    }
}
