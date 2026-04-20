<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Horarios_clases extends Model
{
    
    protected $table = 'horarios_clases';//Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_horario';//Nombre de la columna que identifica cada registro
    protected $keyType = 'int';//Tipo de dato de la clave primaria
    public $incrementing = true;//Indica que la clave primaria es autoincrementable
    public $timestamps = false;
    //Lista de atributos que se pueden guardar en la tabla
    protected $fillable = [
        'id_curso_asignatura',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
    ];
    public function curso_asignatura(){
        return $this->belongsTo(Curso_Asignaturas::class, 'id_curso_asignatura', 'id_curso_asignatura');
    }
}
