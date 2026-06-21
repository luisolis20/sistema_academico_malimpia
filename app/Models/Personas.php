<?php

namespace App\Models;
//Importación de clases necesarias para el modelo Personas
use Illuminate\Database\Eloquent\Model;

//Clase Personas que representa a una persona en la aplicación
class Personas extends Model
{
    //Uso de traits para agregar funcionalidades al modelo Personas
    protected $table = 'personas';//Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_persona';//Nombre de la columna que identifica cada registro
    protected $keyType = 'int';//Tipo de dato de la clave primaria
    public $incrementing = true;//Indica que la clave primaria es autoincrementable
    //Lista de atributos que se pueden guardar en la tabla
    protected $fillable = [
        'cedula',
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'direccion',
        'telefono',
        'correo',
        'sexo',
        'foto',
        'estado',
    ];
    //Función que devuelve la relación entre el modelo Personas y el modelo User (una persona pertenece a un usuario)
    public function usuarios()
    {
        return $this->hasMany(User::class, 'id_persona');//Devuelve la relación entre el modelo Personas y el modelo User, indicando que una persona pertenece a un usuario mediante la clave foránea 'id_persona' en la tabla 'personas' y la clave primaria 'id_usuario' en la tabla 'usuarios'
    }
    //Función que devuelve la relacion entre el modelo Personas y el modelo Familia (una persona puede ser representante o estudiante de varios miembros de la familia)
    public function familia1(){
        return $this->hasMany(Familia::class, 'id_representante');//Devuelve la relación entre el modelo Personas y el modelo Familia, indicando que una persona es representante de varios miembros de la familia mediante la clave foránea 'id_representante' en la tabla 'familias' y la clave primaria 'id_familia' en la tabla 'familias'
    }
    //Función que devuelve la relación entre el modelo Personas y el modelo Familia (una persona puede ser representante o estudiante de varios miembros de la familia)
    public function familia2(){
        return $this->hasMany(Familia::class, 'id_estudiante');//Devuelve la relación entre el modelo Personas y el modelo Familia, indicando que una persona es estudiante de varios miembros de la familia mediante la clave foránea 'id_estudiante' en la tabla 'familias' y la clave primaria 'id_familia' en la tabla 'familias'
    }
    //Función que devuelve la relación entre el modelo Personas y el modelo Cursos (una persona puede ser docente tutor de varios cursos)
    public function cursos(){
        return $this->hasMany(Cursos::class, 'id_docente_tutor');//Devuelve la relación entre el modelo Personas y el modelo Cursos, indicando que una persona es docente tutor de varios cursos mediante la clave foránea 'id_docente_tutor' en la tabla 'cursos' y la clave primaria 'id_curso' en la tabla 'cursos'
    }
    //Función que devuelve la relación entre el modelo Personas y el modelo Curso_Asignaturas (una persona puede tener varias asignaturas en varios cursos)
    public function curso_asignaturas(){
        return $this->hasMany(Curso_Asignaturas::class, 'id_docente');//Devuelve la relación entre el modelo Personas y el modelo Curso_Asignaturas, indicando que una persona es docente de varias asignaturas en varios cursos mediante la clave foránea 'id_docente' en la tabla 'curso_asignaturas' y la clave primaria 'id_curso_asignatura' en la tabla 'curso_asignaturas'
    }
    //Función que devuelve la relación entre el modelo Personas y el modelo Matriculas (una persona puede ser estudiante de varios cursos)
    public function matriculasestudiantes(){
        return $this->hasMany(Matriculas::class, 'id_estudiante');//Devuelve la relación entre el modelo Personas y el modelo Matriculas, indicando que una persona es estudiante de varias matrículas mediante la clave foránea 'id_estudiante' en la tabla 'matriculas' y la clave primaria 'id_matricula' en la tabla 'matriculas'
    }
    //Función que devuelve la relación entre el modelo Personas y el modelo Matriculas (una persona puede ser representante de varios cursos)
    public function matriculasrepresentantes(){
        return $this->hasMany(Matriculas::class, 'id_representante');//Devuelve la relación entre el modelo Personas y el modelo Matriculas, indicando que una persona es representante de varias matrículas mediante la clave foránea 'id_representante' en la tabla 'matriculas' y la clave primaria 'id_matricula' en la tabla 'matriculas'
    }
}
