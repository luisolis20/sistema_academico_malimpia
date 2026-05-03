<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Calificaciones extends Model
{
    
    protected $table = 'calificaciones';//Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_calificacion';//Nombre de la columna que identifica cada registro
    protected $keyType = 'int';//Tipo de dato de la clave primaria
    public $incrementing = true;//Indica que la clave primaria es autoincrementable
    public $timestamps = false;
    //Lista de atributos que se pueden guardar en la tabla
    protected $fillable = [
        'id_matricula',
        'id_curso_asignatura',
        'q1_p1',
        'q1_p2',
        'q1_p3',
        'q1_examen',
        'q1_promedio',
        'q2_p1',
        'q2_p2',
        'q2_p3',
        'q2_examen',
        'q2_promedio',
        'promedio_anual',
        'nota_supletorio',
        'nota_remedial',
        'nota_gracia',
        'nota_final_definitiva',
        'estado_asignatura',
    ];
    public function matricula(){
        return $this->belongsTo(Matriculas::class, 'id_matricula', 'id_matricula');
    }
    public function curso_asignatura(){
        return $this->belongsTo(Curso_Asignaturas::class, 'id_curso_asignatura', 'id_curso_asignatura');
    }
}
