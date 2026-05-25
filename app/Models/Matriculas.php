<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Matriculas extends Model
{
    
    protected $table = 'matriculas';//Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_matricula';//Nombre de la columna que identifica cada registro
    protected $keyType = 'int';//Tipo de dato de la clave primaria
    public $incrementing = true;//Indica que la clave primaria es autoincrementable
    public $timestamps = false;
    //Lista de atributos que se pueden guardar en la tabla
    protected $fillable = [
        'id_estudiante',
        'id_curso',
        'id_representante',
        'fecha_matricula',
        'es_nuevo',
        'estado',
    ];
    public function estudiante(){
        return $this->belongsTo(Personas::class, 'id_estudiante', 'id_persona');
    }
    public function curso(){
        return $this->belongsTo(Cursos::class, 'id_curso', 'id_curso');
    }
    public function representante(){
        return $this->belongsTo(Personas::class, 'id_representante', 'id_persona');
    }
    public function asistencias(){
        return $this->hasMany(Asistencia::class, 'id_matricula', 'id_matricula');
    }
    public function calificaciones(){
        return $this->hasMany(Calificaciones::class, 'id_matricula', 'id_matricula');
    }
    public function conductas(){
        return $this->hasMany(Conducta::class, 'id_matricula', 'id_matricula');
    }
}
