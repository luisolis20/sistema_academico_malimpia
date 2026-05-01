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
    public function curso(){
        return $this->belongsTo(Cursos::class, 'id_curso', 'id_curso');
    }
    public function asignatura(){
        return $this->belongsTo(Asignaturas::class, 'id_asignatura', 'id_asignatura');
    }
    public function docente(){
        return $this->belongsTo(Personas::class, 'id_docente', 'id_persona');
    }
    public function horarios_clases(){
        return $this->hasMany(Horarios_clases::class, 'id_curso_asignatura');
    }
    public function asistencias(){
        return $this->hasMany(Asistencia::class, 'id_curso_asignatura', 'id_curso_asignatura');
    }
}
