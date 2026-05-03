<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Control_subida_notas extends Model
{
    
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
    public function periodo(){
        return $this->belongsTo(Periodos_lectivos::class, 'id_periodo', 'id_periodo');
    }
}
