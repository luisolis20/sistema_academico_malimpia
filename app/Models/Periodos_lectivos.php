<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Periodos_lectivos extends Model
{
    
    protected $table = 'periodos_lectivos';//Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_periodo';//Nombre de la columna que identifica cada registro
    protected $keyType = 'int';//Tipo de dato de la clave primaria
    public $incrementing = true;//Indica que la clave primaria es autoincrementable
     public $timestamps = false;
    //Lista de atributos que se pueden guardar en la tabla
    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'matriculas_abiertas',
        'estado_activo',
    ];
    public function cursos(){
        return $this->hasMany(Cursos::class, 'id_periodo', 'id_periodo');
    }
}
