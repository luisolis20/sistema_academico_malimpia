<?php

namespace App\Models;
//Importación de clases necesarias para el modelo Familia
use Illuminate\Database\Eloquent\Model;

//Clase Familia que representa una familia en la aplicación
class Familia extends Model
{
    //Uso de traits para agregar funcionalidades al modelo Familia
    protected $table = 'familia';//Nombre de la tabla en la base de datos
    //Lista de atributos que se pueden guardar en la tabla
    protected $fillable = [
        'id_representante',
        'id_estudiante',
        'parentesco',
    ];
    //Función que devuelve la relación entre el modelo Familia y el modelo Personas (una familia puede tener dos personas)
    public function familiapersona1(){
        return $this->belongsTo(Personas::class, 'id_representante', 'id_persona');//Devuelve la relación entre el modelo Familia y el modelo Personas, indicando que una familia puede tener dos personas mediante la clave foránea 'id_representante' en la tabla 'familia' y la clave primaria 'id_persona' en la tabla 'personas'
    }
    //Función que devuelve la relación entre el modelo Familia y el modelo Personas (una familia puede tener dos personas)
    public function familiapersona2(){
        return $this->belongsTo(Personas::class, 'id_estudiante', 'id_persona');//Devuelve la relación entre el modelo Familia y el modelo Personas, indicando que una familia puede tener dos personas mediante la clave foránea 'id_estudiante' en la tabla 'familia' y la clave primaria 'id_persona' en la tabla 'personas'
    }
}
