<?php

namespace App\Models;
//Importación de clases necesarias para el modelo Cronograma_matriculas
use Illuminate\Database\Eloquent\Model;

//Clase Cronograma_matriculas que representa un cronograma de matrículas en la aplicación
class Cronograma_matriculas extends Model
{
    //Definición de las propiedades del modelo Cronograma_matriculas
    protected $table = 'cronograma_matriculas';//Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_cronograma';//Nombre de la columna que identifica cada registro
    protected $keyType = 'int';//Tipo de dato de la clave primaria
    public $incrementing = true;//Indica que la clave primaria es autoincrementable
    public $timestamps = false;
    //Lista de atributos que se pueden guardar en la tabla
    protected $fillable = [
        'id_periodo',
        'id_nivel',
        'id_especialidad',
        'fecha_inicio',
        'fecha_fin',
    ];
    //Función que devuelve la relación entre el modelo Cronograma_matriculas y el modelo Periodos_lectivos (un cronograma de matrículas puede pertenecer a un periodo lectivo)
    public function periodo(){
        return $this->belongsTo(Periodos_lectivos::class, 'id_periodo', 'id_periodo');//Devuelve la relación entre el modelo Cronograma_matriculas y el modelo Periodos_lectivos, indicando que un cronograma de matrículas puede pertenecer a un periodo lectivo mediante la clave foránea 'id_periodo' en la tabla 'cronograma_matriculas' y la clave primaria 'id_periodo' en la tabla 'periodos_lectivos'
    }
    //Función que devuelve la relación entre el modelo Cronograma_matriculas y el modelo Niveles_academicos (un cronograma de matrículas puede pertenecer a un nivel académico)
    public function nivel(){
        return $this->belongsTo(Niveles_academicos::class, 'id_nivel', 'id_nivel');//Devuelve la relación entre el modelo Cronograma_matriculas y el modelo Niveles_academicos, indicando que un cronograma de matrículas puede pertenecer a un nivel académico mediante la clave foránea 'id_nivel' en la tabla 'cronograma_matriculas' y la clave primaria 'id_nivel' en la tabla 'niveles_academicos'
    }
    //Función que devuelve la relación entre el modelo Cronograma_matriculas y el modelo Especialidades (un cronograma de matrículas puede pertenecer a una especialidad)
    public function especialidad(){
        return $this->belongsTo(Especialidades::class, 'id_especialidad', 'id_especialidad');//Devuelve la relación entre el modelo Cronograma_matriculas y el modelo Especialidades, indicando que un cronograma de matrículas puede pertenecer a una especialidad mediante la clave foránea 'id_especialidad' en la tabla 'cronograma_matriculas' y la clave primaria 'id_especialidad' en la tabla 'especialidades'
    }
}
