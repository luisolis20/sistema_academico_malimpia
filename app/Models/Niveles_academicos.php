<?php

namespace App\Models;
//Importación de clases necesarias para el modelo Niveles_academicos
use Illuminate\Database\Eloquent\Model;

//Clase Niveles_academicos que representa un nivel académico en la aplicación
class Niveles_academicos extends Model
{
    //Uso de traits para agregar funcionalidades al modelo Niveles_academicos
    protected $table = 'niveles_academicos';//Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_nivel';//Nombre de la columna que identifica cada registro
    protected $keyType = 'int';//Tipo de dato de la clave primaria
    public $incrementing = true;//Indica que la clave primaria es autoincrementable
    //Lista de atributos que se pueden guardar en la tabla
    protected $fillable = [
        'nombre',
        'orden_jerarquia',//Ejemplo: 1ro, 2do, 3ro, etc. para definir el orden de los niveles
        'estado',
    ];
    //Función que devuelve la relación entre el modelo Niveles_academicos y el modelo Cursos (un nivel académico puede tener varios cursos)
    public function cursos(){
        return $this->hasMany(Cursos::class, 'id_nivel', 'id_nivel');//Devuelve la relación entre el modelo Niveles_academicos y el modelo Cursos, indicando que un nivel académico puede tener varios cursos mediante la clave foránea 'id_nivel' en la tabla 'cursos' y la clave primaria 'id_curso' en la tabla 'cursos'
    }
    //Función que devuelve la relación entre el modelo Niveles_academicos y el modelo Especialidades (un nivel académico puede tener varias especialidades)
    public function especialidades(){
        return $this->hasMany(Especialidades::class, 'id_nivel', 'id_nivel');//Devuelve la relación entre el modelo Niveles_academicos y el modelo Especialidades, indicando que un nivel académico puede tener varias especialidades mediante la clave foránea 'id_nivel' en la tabla 'especialidades' y la clave primaria 'id_especialidad' en la tabla 'especialidades'
    }
}
