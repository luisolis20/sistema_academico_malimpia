<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Familia extends Model
{
    
    protected $table = 'familia';//Nombre de la tabla en la base de datos
    
    //Lista de atributos que se pueden guardar en la tabla
    protected $fillable = [
        'id_representante',
        'id_estudiante',
        'parentesco',
    ];
    public function familiapersona1(){
        return $this->belongsTo(Personas::class, 'id_representante', 'id_persona');
    }
    public function familiapersona2(){
        return $this->belongsTo(Personas::class, 'id_estudiante', 'id_persona');
    }
}
