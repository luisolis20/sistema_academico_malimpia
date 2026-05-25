<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Conducta extends Model
{
    
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
    public function matricula(){
        return $this->belongsTo(Matriculas::class, 'id_matricula', 'id_matricula');
    }
}
