<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Curso_Asignaturas extends Model
{
    
    protected $table = 'curso_asignatura';//Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_curso_asignatura';//Nombre de la columna que identifica cada registro
    protected $keyType = 'int';//Tipo de dato de la clave primaria
    public $incrementing = true;//Indica que la clave primaria es autoincrementable
    //Lista de atributos que se pueden guardar en la tabla
    protected $fillable = [
        'id_curso',
        'id_asignatura',
        'id_docente',
        'horas_semanales',
        'estado',
    ];
}
