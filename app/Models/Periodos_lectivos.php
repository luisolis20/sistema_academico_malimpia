<?php

namespace App\Models;
//Importación de clases necesarias para el modelo Periodos_lectivos
use Illuminate\Database\Eloquent\Model;

//Clase Periodos_lectivos que representa un periodo lectivo en la aplicación
class Periodos_lectivos extends Model
{
    //Uso de traits para agregar funcionalidades al modelo Periodos_lectivos
    protected $table = 'periodos_lectivos';//Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_periodo';//Nombre de la columna que identifica cada registro
    protected $keyType = 'int';//Tipo de dato de la clave primaria
    public $incrementing = true;//Indica que la clave primaria es autoincrementable
     public $timestamps = false;//Indica que no se guardan las columnas de tiempo de creación y actualización
    //Lista de atributos que se pueden guardar en la tabla
    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'matriculas_abiertas',
        'estado_activo',
    ];
    //Función que devuelve la relación entre el modelo Periodos_lectivos y el modelo Cursos (un periodo lectivo puede tener varios cursos)
    public function cursos(){
        return $this->hasMany(Cursos::class, 'id_periodo', 'id_periodo');//Devuelve la relación entre el modelo Periodos_lectivos y el modelo Cursos, indicando que un periodo lectivo puede tener varios cursos mediante la clave foránea 'id_periodo' en la tabla 'cursos' y la clave primaria 'id_curso' en la tabla 'cursos'
    }
    //Función que devuelve la relación entre el modelo Periodos_lectivos y el modelo Cronograma_matriculas (un periodo lectivo puede tener varios cronogramas de matrículas)
    public function cronograma_matriculas(){
        return $this->hasMany(Cronograma_matriculas::class, 'id_periodo', 'id_periodo');//Devuelve la relación entre el modelo Periodos_lectivos y el modelo Cronograma_matriculas, indicando que un periodo lectivo puede tener varios cronogramas de matrículas mediante la clave foránea 'id_periodo' en la tabla 'cronograma_matriculas' y la clave primaria 'id_cronograma_matricula' en la tabla 'cronograma_matriculas'
    }
    //Función que devuelve la relación entre el modelo Periodos_lectivos y el modelo Control_subida_notas (un periodo lectivo puede tener varios registros de subida de notas)
    public function control_subida_notas(){
        return $this->hasMany(Control_subida_notas::class, 'id_periodo', 'id_periodo');//Devuelve la relación entre el modelo Periodos_lectivos y el modelo Control_subida_notas, indicando que un periodo lectivo puede tener varios registros de subida de notas mediante la clave foránea 'id_periodo' en la tabla 'control_subida_notas' y la clave primaria 'id_control_subida_notas' en la tabla 'control_subida_notas'
    }
}
