<?php

namespace App\Models;
//Importación de clases necesarias para el modelo Especialidades
use Illuminate\Database\Eloquent\Model;

//Clase Especialidades que representa una especialidad en la aplicación
class Especialidades extends Model
{
    //Uso de traits para agregar funcionalidades al modelo Especialidades
    protected $table = 'especialidades';//Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_especialidad';//Nombre de la columna que identifica cada registro
    protected $keyType = 'int';//Tipo de dato de la clave primaria
    public $incrementing = true;//Indica que la clave primaria es autoincrementable
    //Lista de atributos que se pueden guardar en la tabla
    protected $fillable = [
        'nombre',
        'estado',
    ];
    //Función que devuelve la relación entre el modelo Especialidades y el modelo Cursos (una especialidad puede tener varios cursos)
    public function cursos(){
        return $this->hasMany(Cursos::class, 'id_especialidad', 'id_especialidad');//Devuelve la relación entre el modelo Especialidades y el modelo Cursos, indicando que una especialidad puede tener varios cursos mediante la clave foránea 'id_especialidad' en la tabla 'cursos' y la clave primaria 'id_curso' en la tabla 'cursos'
    }
    //Función que devuelve la relación entre el modelo Especialidades y el modelo Niveles_academicos (una especialidad puede pertenecer a varios niveles académicos)
    public function niveles_academicos(){
        return $this->hasMany(Niveles_academicos::class, 'id_especialidad', 'id_especialidad');//Devuelve la relación entre el modelo Especialidades y el modelo Niveles_academicos, indicando que una especialidad puede pertenecer a varios niveles académicos mediante la clave foránea 'id_especialidad' en la tabla 'niveles_academicos' y la clave primaria 'id_nivel_academico' en la tabla 'niveles_academicos'
    }
}
