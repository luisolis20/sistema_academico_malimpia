<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Asistencia extends Model
{
    
    protected $table = 'asistencia';//Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_asistencia';//Nombre de la columna que identifica cada registro
    protected $keyType = 'int';//Tipo de dato de la clave primaria
    public $incrementing = true;//Indica que la clave primaria es autoincrementable
    public $timestamps = false;
    //Lista de atributos que se pueden guardar en la tabla
    protected $fillable = [
        'id_matricula',
        'id_curso_asignatura',
        'fecha',//Solo de tipo DATE
        'estado', //Estados disponibles: Presente, Ausente, Justificado
        'url_justificativo',
    ];
    public function matricula(){
        return $this->belongsTo(Matriculas::class, 'id_matricula', 'id_matricula');
    }
    public function curso_asignatura(){
        return $this->belongsTo(Curso_Asignaturas::class, 'id_curso_asignatura', 'id_curso_asignatura');
    }
}
