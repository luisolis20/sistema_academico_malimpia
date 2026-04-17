<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Especialidades extends Model
{
    
    protected $table = 'especialidades';//Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_especialidad';//Nombre de la columna que identifica cada registro
    protected $keyType = 'int';//Tipo de dato de la clave primaria
    public $incrementing = true;//Indica que la clave primaria es autoincrementable
    //Lista de atributos que se pueden guardar en la tabla
    protected $fillable = [
        'nombre',
        'estado',
    ];
    public function cursos(){
        return $this->hasMany(Cursos::class, 'id_especialidad', 'id_especialidad');
    }
}
