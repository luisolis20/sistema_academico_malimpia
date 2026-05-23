<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matriculas;

class EstudianteMatriculaHistorialController extends Controller
{
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
        ])
        ->where('id_estudiante', $id_estudiante)
        ->orderBy('id_matricula', 'desc') // Mostrar las más recientes primero
        ->get();

        if ($matriculas->isEmpty()) {
            return response()->json([
                'error' => false,
                'data' => [],
                'mensaje' => 'El estudiante no cuenta con registros de matrícula'
            ]);
        }

        // Estructuramos la respuesta limpia para el Frontend
        $data = $matriculas->map(function ($m) {
            $curso = $m->curso;
            
            // Mapeamos las asignaturas del curso actual asignado en la matrícula
            $asignaturas = $curso && $curso->curso_asignaturas 
                ? $curso->curso_asignaturas->map(function ($ca) {
                    return [
                        'id_asignatura' => $ca->id_asignatura,
                        'nombre' => optional($ca->asignatura)->nombre ?? 'Asignatura no definida'
                    ];
                }) 
                : [];

            return [
                'id_matricula'     => $m->id_matricula,
                'fecha_matricula'  => $m->fecha_matricula ? date('d/m/Y', strtotime($m->fecha_matricula)) : 'S/F',
                'estado_matricula' => $m->estado ?? 'Activo',
                'es_nuevo'         => $m->es_nuevo ? 'Sí' : 'No',
                'periodo'          => optional($curso->periodo)->nombre ?? 'N/A',
                'nivel'            => optional($curso->nivel)->nombre ?? 'N/A',
                'paralelo'         => $curso->paralelo ?? 'A',
                'especialidad'     => optional($curso->especialidad)->nombre ?? 'Educación General',
                'tutor'            => $curso && $curso->docentetutor 
                                        ? "{$curso->docentetutor->nombres} {$curso->docentetutor->apellidos}" 
                                        : 'Por Asignar',
                'representante'    => $m->representante 
                                        ? "{$m->representante->nombres} {$m->representante->apellidos}" 
                                        : 'Inscrito por la Institución',
                'asignaturas'      => $asignaturas
            ];
        });

        return response()->json([
            'error' => false,
            'data' => $data
        ], 200);
    }
}
