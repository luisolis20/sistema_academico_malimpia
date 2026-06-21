<?php

namespace App\Models;
//Importamos la clase Model de Eloquent para poder utilizar sus funcionalidades
use Illuminate\Database\Eloquent\Model;

//Definimos la clase Control_subida_notas que extiende de Model, lo que nos permite interactuar con la tabla 'control_subida_notas' en la base de datos
class Control_subida_notas extends Model
{
    //Definimos las propiedades de la clase para configurar la conexión con la base de datos
    protected $table = 'control_subida_notas';//Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_control';//Nombre de la columna que identifica cada registro
    protected $keyType = 'int';//Tipo de dato de la clave primaria
    public $incrementing = true;//Indica que la clave primaria es autoincrementable
    public $timestamps = false;
    //Lista de atributos que se pueden guardar en la tabla
    protected $fillable = [
        'id_periodo',
        'fase_evaluacion',//Fases: 'Q1_P1','Q1_P2','Q1_P3','Q1_EXAMEN','Q2_P1','Q2_P2','Q2_P3','Q2_EXAMEN','SUPLETORIO','REMEDIAL'
        'fecha_inicio',
        'fecha_fin',
        'habilitado',
    ];
    //Función que devuelve la relación entre el modelo Control_subida_notas y el modelo Periodos_lectivos (un control de subida de notas puede pertenecer a un periodo lectivo)
    public function periodo(){
        return $this->belongsTo(Periodos_lectivos::class, 'id_periodo', 'id_periodo');//Devuelve la relación entre el modelo Control_subida_notas y el modelo Periodos_lectivos, indicando que un control de subida de notas puede pertenecer a un periodo lectivo mediante la clave foránea 'id_periodo' en la tabla 'control_subida_notas' y la clave primaria 'id_periodo' en la tabla 'periodos_lectivos'
    }
}
