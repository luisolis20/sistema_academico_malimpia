<?php

namespace App\Http\Controllers;
//Importación de clases necesarias para el controlador EstudianteMatriculaHistorialController
use Illuminate\Http\Request;//Importación de la clase Request para manejar las solicitudes HTTP
use App\Models\Matriculas;//Importación de la clase Matriculas

//Clase EstudianteMatriculaHistorialController que representa un controlador en la aplicación para manejar las operaciones relacionadas con los estudiantes y sus matriculas
class EstudianteMatriculaHistorialController extends Controller
{
    /**
     * Función que muestra una lista de estudiantes y sus matriculas, las cuales pueden ser filtradas por página y por búsqueda.
     * La función index es la encargada de mostrar una lista de estudiantes y sus matriculas, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     */
    public function getHistorialMatriculas(string $id_estudiante)
    {
        // Cargamos la matrícula con todo su árbol de relaciones estructurales
        $matriculas = Matriculas::with([
            'curso.periodo',
            'curso.nivel',
            'curso.especialidad',
            'curso.docentetutor',
            'curso.curso_asignaturas.asignatura',
            'representante'
        ])//Incluir todas las relaciones necesarias para mostrar los datos de las matrículas(curso, nivel, especialidad, docentetutor, asignatura, representante)
        ->where('id_estudiante', $id_estudiante)//Filtrar solo las matrículas del estudiante especificado
        ->orderBy('id_matricula', 'desc') // Mostrar las más recientes primero
        ->get();//Obtener los datos de la consulta
        //Si no hay datos, devolver un mensaje de error indicando que no se encontraron datos
        if ($matriculas->isEmpty()) {
            return response()->json([
                'error' => false,
                'data' => [],
                'mensaje' => 'El estudiante no cuenta con registros de matrícula'
            ]);
        }

        // Estructuramos la respuesta limpia para el Frontend
        $data = $matriculas->map(function ($m) {//Iterar sobre cada matrícula
            $curso = $m->curso;//Obtener el curso del curso actual asignado en la matrícula
            
            // Mapeamos las asignaturas del curso actual asignado en la matrícula
            $asignaturas = $curso && $curso->curso_asignaturas //Si existe una tabla 'curso_asignaturas'
                ? $curso->curso_asignaturas->map(function ($ca) {//Iterar sobre cada asignatura
                    return [//Devolver los datos de la asignatura
                        'id_asignatura' => $ca->id_asignatura,//Asignar el id de la asignatura
                        'nombre' => optional($ca->asignatura)->nombre ?? 'Asignatura no definida'//Asignar el nombre de la asignatura
                    ];
                }) 
                : [];

            return [//Devolver los datos de la matrícula junto con las asignaturas
                'id_matricula'     => $m->id_matricula,
                'fecha_matricula'  => $m->fecha_matricula ? date('d/m/Y', strtotime($m->fecha_matricula)) : 'S/F',
                'estado_matricula' => $m->estado ?? 'Activo',
                'es_nuevo'         => $m->es_nuevo ? 'Sí' : 'No',
                'periodo'          => optional($curso->periodo)->nombre ?? 'N/A',//Obtener el nombre del periodo del curso
                'nivel'            => optional($curso->nivel)->nombre ?? 'N/A',//Obtener el nombre del nivel del curso
                'paralelo'         => $curso->paralelo ?? 'A',
                'especialidad'     => optional($curso->especialidad)->nombre ?? 'Educación General',//Obtener el nombre de la especialidad del curso
                'tutor'            => $curso && $curso->docentetutor 
                                        ? "{$curso->docentetutor->nombres} {$curso->docentetutor->apellidos}" 
                                        : 'Por Asignar',//Obtener el nombre del docente tutor del curso
                'representante'    => $m->representante 
                                        ? "{$m->representante->nombres} {$m->representante->apellidos}" 
                                        : 'Inscrito por la Institución',//Obtener el nombre del representante del estudiante
                'asignaturas'      => $asignaturas
            ];
        });
        //Devolver los datos en formato JSON
        return response()->json([
            'error' => false,
            'data' => $data
        ], 200);
    }
}
