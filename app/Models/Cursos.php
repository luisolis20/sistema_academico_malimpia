<?php

namespace App\Models;
//Importación de clases necesarias para el modelo Cursos
use Illuminate\Database\Eloquent\Model;

//Definición del modelo Cursos que representa la tabla 'cursos' en la base de datos
class Cursos extends Model
{
    //Definición de las propiedades del modelo Cursos
    protected $table = 'cursos';//Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_curso';//Nombre de la columna que identifica cada registro
    protected $keyType = 'int';//Tipo de dato de la clave primaria
    public $incrementing = true;//Indica que la clave primaria es autoincrementable
    
    //Lista de atributos que se pueden guardar en la tabla
    protected $fillable = [
        'id_periodo',
        'id_nivel',
        'id_especialidad',
        'paralelo',
        'id_docente_tutor',
        'estado'
    ];
    //Función que devuelve la relación entre el modelo Cursos y el modelo Periodos_lectivos (un curso puede pertenecer a un periodo lectivo)
    public function periodo(){
        return $this->belongsTo(Periodos_lectivos::class, 'id_periodo', 'id_periodo');//Devuelve la relación entre el modelo Cursos y el modelo Periodos_lectivos, indicando que un curso puede pertenecer a un periodo lectivo mediante la clave foránea 'id_periodo' en la tabla 'cursos' y la clave primaria 'id_periodo' en la tabla 'periodos_lectivos'
    }
    //Función que devuelve la relación entre el modelo Cursos y el modelo Niveles_academicos (un curso puede pertenecer a un nivel académico)
    public function nivel(){
        return $this->belongsTo(Niveles_academicos::class, 'id_nivel', 'id_nivel');//Devuelve la relación entre el modelo Cursos y el modelo Niveles_academicos, indicando que un curso puede pertenecer a un nivel académico mediante la clave foránea 'id_nivel' en la tabla 'cursos' y la clave primaria 'id_nivel' en la tabla 'niveles_academicos'
    }
    //Función que devuelve la relación entre el modelo Cursos y el modelo Especialidades (un curso puede pertenecer a una especialidad)
    public function especialidad(){
        return $this->belongsTo(Especialidades::class, 'id_especialidad', 'id_especialidad');//Devuelve la relación entre el modelo Cursos y el modelo Especialidades, indicando que un curso puede pertenecer a una especialidad mediante la clave foránea 'id_especialidad' en la tabla 'cursos' y la clave primaria 'id_especialidad' en la tabla 'especialidades'
    }
    //Función que devuelve la relación entre el modelo Cursos y el modelo Personas (un curso puede pertenecer a un docente tutor)
    public function docentetutor(){
        return $this->belongsTo(Personas::class, 'id_docente_tutor', 'id_persona');//Devuelve la relación entre el modelo Cursos y el modelo Personas, indicando que un curso puede pertenecer a un docente tutor mediante la clave foránea 'id_docente_tutor' en la tabla 'cursos' y la clave primaria 'id_persona' en la tabla 'personas'
    }
    //Función que devuelve la relación entre el modelo Cursos y el modelo Curso_Asignaturas (un curso puede tener muchas asignaturas)
    public function curso_asignaturas(){
        return $this->hasMany(Curso_Asignaturas::class, 'id_curso', 'id_curso');//Devuelve la relación entre el modelo Cursos y el modelo Curso_Asignaturas, indicando que un curso puede tener muchas asignaturas mediante la clave foránea 'id_curso' en la tabla 'curso_asignaturas' y la clave primaria 'id_curso' en la tabla 'cursos'
    }
    //Función que devuelve la relación entre el modelo Cursos y el modelo Matriculas (un curso puede tener muchas matrículas)
    public function matriculas(){
        return $this->hasMany(Matriculas::class, 'id_curso', 'id_curso');//Devuelve la relación entre el modelo Cursos y el modelo Matriculas, indicando que un curso puede tener muchas matrículas mediante la clave foránea 'id_curso' en la tabla 'matriculas' y la clave primaria 'id_curso' en la tabla 'cursos'
    }
}
