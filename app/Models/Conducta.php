<?php

namespace App\Models;
//Importamos la clase Model de Eloquent para poder utilizar sus funcionalidades
use Illuminate\Database\Eloquent\Model;

//Clase Conducta que representa una conducta en la aplicación
class Conducta extends Model
{
    //Definimos las propiedades de la clase para configurar la conexión con la base de datos
    protected $table = 'conducta';//Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_conducta';//Nombre de la columna que identifica cada registro
    protected $keyType = 'int';//Tipo de dato de la clave primaria
    public $incrementing = true;//Indica que la clave primaria es autoincrementable
    public $timestamps = false;
    //Lista de atributos que se pueden guardar en la tabla
    protected $fillable = [
        'id_matricula',
        'quimestre',//Quimestre 1 y Quimestre 2
        'calificacion_letra',//A, B, C, D, E
        'observacion', 
    ];
    //Función que devuelve la relación entre el modelo Conducta y el modelo Matriculas (una conducta puede pertenecer a un matricula)
    public function matricula(){
        return $this->belongsTo(Matriculas::class, 'id_matricula', 'id_matricula');//Devuelve la relación entre el modelo Conducta y el modelo Matriculas, indicando que una conducta puede pertenecer a un matricula mediante la clave foránea 'id_matricula' en la tabla 'conducta' y la clave primaria 'id_matricula' en la tabla 'matriculas'
    }
}
