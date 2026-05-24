<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Niveles_academicos extends Model
{
    
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
    public function cursos(){
        return $this->hasMany(Cursos::class, 'id_nivel', 'id_nivel');
    }
    public function especialidades(){
        return $this->hasMany(Especialidades::class, 'id_nivel', 'id_nivel');
    }
}
