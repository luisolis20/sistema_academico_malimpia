<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Cursos extends Model
{
    
    protected $table = 'cursos';//Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_curso';//Nombre de la columna que identifica cada registro
    protected $keyType = 'int';//Tipo de dato de la clave primaria
    public $incrementing = true;//Indica que la clave primaria es autoincrementable
    
    //Lista de atributos que se pueden guardar en la tabla
    protected $fillable = [
        'id_periodo',
        'id_nivel',
        'id_especialidad',
        'paralelo',
        'id_docente_tutor',
        'estado'
    ];
    public function periodo(){
        return $this->belongsTo(Periodos_lectivos::class, 'id_periodo', 'id_periodo');
    }
    public function nivel(){
        return $this->belongsTo(Niveles_academicos::class, 'id_nivel', 'id_nivel');
    }
    public function especialidad(){
        return $this->belongsTo(Especialidades::class, 'id_especialidad', 'id_especialidad');
    }
    public function docentetutor(){
        return $this->belongsTo(Personas::class, 'id_docente_tutor', 'id_persona');
    }
    public function curso_asignaturas(){
        return $this->hasMany(Curso_Asignaturas::class, 'id_curso', 'id_curso');
    }
    public function matriculas(){
        return $this->hasMany(Matriculas::class, 'id_curso', 'id_curso');
    }
}
