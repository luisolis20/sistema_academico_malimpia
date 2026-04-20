<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Personas extends Model
{
    
    protected $table = 'personas';//Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_persona';//Nombre de la columna que identifica cada registro
    protected $keyType = 'int';//Tipo de dato de la clave primaria
    public $incrementing = true;//Indica que la clave primaria es autoincrementable
    //Lista de atributos que se pueden guardar en la tabla
    protected $fillable = [
        'cedula',
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'direccion',
        'telefono',
        'correo',
        'sexo',
        'foto',
        'estado',
    ];
    public function usuarios()
    {
        return $this->hasMany(User::class, 'id_persona');
    }
    public function familia1(){
        return $this->hasMany(Familia::class, 'id_representante');
    }
    public function familia2(){
        return $this->hasMany(Familia::class, 'id_estudiante');
    }
    public function cursos(){
        return $this->hasMany(Cursos::class, 'id_docente_tutor');
    }
    public function curso_asignaturas(){
        return $this->hasMany(Curso_Asignaturas::class, 'id_docente');
    }
}
