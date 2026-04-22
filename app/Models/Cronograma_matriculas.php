<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Cronograma_matriculas extends Model
{
    
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
    public function periodo(){
        return $this->belongsTo(Periodos_lectivos::class, 'id_periodo', 'id_periodo');
    }
    public function nivel(){
        return $this->belongsTo(Niveles_academicos::class, 'id_nivel', 'id_nivel');
    }
    public function especialidad(){
        return $this->belongsTo(Especialidades::class, 'id_especialidad', 'id_especialidad');
    }
}
