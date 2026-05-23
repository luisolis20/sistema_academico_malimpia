<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matriculas;
use App\Models\Asistencia;
use Illuminate\Support\Facades\DB;

class EstudianteNotasController extends Controller
{
    public function getNotasHistorial(Request $request, string $id_persona)
    {
        // 1. Obtener todas las matrículas históricas del estudiante
        $matriculas = Matriculas::with([
            'curso.periodo',
            'curso.nivel',
            'curso.especialidad',
            'calificaciones.curso_asignatura.asignatura'
        ])
        ->where('id_estudiante', $id_persona)
        ->get();

        if ($matriculas->isEmpty()) {
            return response()->json([
                'periodos' => [],
                'periodo_activo_id' => null
            ], 200);
        }

        $datosHistorial = [];
        $periodoActivoId = null;

        foreach ($matriculas as $matricula) {
            $curso = $matricula->curso;
            if (!$curso || !$curso->periodo) {
                continue;
            }

            $periodo = $curso->periodo;

            // Guardamos el id del periodo marcado como activo en la BD
            if ($periodo->estado_activo == 1) {
                $periodoActivoId = $periodo->id_periodo;
            }

            // 2. Calcular asistencia agrupada por Asignatura del Curso en esta matrícula
            $asistenciasData = Asistencia::where('id_matricula', $matricula->id_matricula)
                ->select('id_curso_asignatura',
                    DB::raw('COUNT(*) as total_clases'),
                    DB::raw('SUM(CASE WHEN estado IN ("Presente", "Justificado") THEN 1 ELSE 0 END) as asistidas')
                )
                ->groupBy('id_curso_asignatura')
                ->get()
                ->keyBy('id_curso_asignatura');

            // 3. Procesar asignaturas y calificaciones estructuradas
            $calificacionesProcesadas = [];
            foreach ($matricula->calificaciones as $calif) {
                $idCA = $calif->id_curso_asignatura;
                $asistencia = $asistenciasData->get($idCA);

                // Cálculo de porcentaje de asistencia por asignatura
                $porcentajeAsistencia = 100;
                if ($asistencia && $asistencia->total_clases > 0) {
                    $porcentajeAsistencia = round(($asistencia->asistidas / $asistencia->total_clases) * 100, 2);
                }

                $calificacionesProcesadas[] = [
                    'id_calificacion'   => $calif->id_calificacion,
                    'asignatura'        => optional(optional($calif->curso_asignatura)->asignatura)->nombre ?? 'Asignatura Desconocida',
                    'q1_p1'             => $calif->q1_p1 ?? 0,
                    'q1_p2'             => $calif->q1_p2 ?? 0,
                    'q1_p3'             => $calif->q1_p3 ?? 0,
                    'q1_examen'         => $calif->q1_examen ?? 0,
                    'q1_promedio'       => $calif->q1_promedio ?? 0,
                    'q2_p1'             => $calif->q2_p1 ?? 0,
                    'q2_p2'             => $calif->q2_p2 ?? 0,
                    'q2_p3'             => $calif->q2_p3 ?? 0,
                    'q2_examen'         => $calif->q2_examen ?? 0,
                    'q2_promedio'       => $calif->q2_promedio ?? 0,
                    'promedio_anual'    => $calif->promedio_anual ?? 0,
                    'nota_supletorio'   => $calif->nota_supletorio ?? 0,
                    'nota_remedial'     => $calif->nota_remedial ?? 0,
                    'nota_gracia'       => $calif->nota_gracia ?? 0,
                    'nota_final_definitiva' => $calif->nota_final_definitiva ?? 0,
                    'estado_asignatura' => $calif->estado_asignatura ?? 'En Proceso',
                    'asistencia'        => $porcentajeAsistencia
                ];
            }

            $datosHistorial[] = [
                'id_periodo'     => $periodo->id_periodo,
                'nombre_periodo' => $periodo->nombre,
                'detalles_curso' => [
                    'nivel'        => optional($curso->nivel)->nombre ?? 'N/A',
                    'especialidad' => optional($curso->especialidad)->nombre ?? 'General',
                    'paralelo'     => $curso->paralelo ?? 'Sin Especificar',
                ],
                'notas' => $calificacionesProcesadas
            ];
        }

        // Si por alguna razón ningún periodo tiene estado_activo = 1, asignamos por prioridad el último
        if (!$periodoActivoId && count($datosHistorial) > 0) {
            $periodoActivoId = $datosHistorial[count($datosHistorial) - 1]['id_periodo'];
        }

        return response()->json([
            'periodos'          => $datosHistorial,
            'periodo_activo_id' => $periodoActivoId
        ], 200);
    }
    public function getHistorialCompleto(string $id_persona)
    {
        // 1. Extraer todas las matrículas del estudiante
        $matriculas = Matriculas::with([    
            'curso.periodo',
            'curso.nivel',
            'curso.especialidad',
            'calificaciones.curso_asignatura.asignatura'
        ])->where('id_estudiante', $id_persona)->get();

        $historial = [];
        $sumaNotasGlobal = 0;
        $totalAsignaturasGlobal = 0;

        foreach ($matriculas as $matricula) {
            $curso = $matricula->curso;
            if (!$curso) continue;

            // 2. Calcular Asistencia General del Curso
            $asistencias = Asistencia::where('id_matricula', $matricula->id_matricula)->get();
            $totalClases = $asistencias->count();
            $asistidas = $asistencias->whereIn('estado', ['Presente', 'Justificado'])->count();
            $asistenciaCurso = $totalClases > 0 ? round(($asistidas / $totalClases) * 100, 2) : 100;

            // 3. Procesar Asignaturas y Calcular Promedio del Curso
            $asignaturasData = [];
            $sumaNotasCurso = 0;
            $materiasReprobadas = 0;

            foreach ($matricula->calificaciones as $calif) {
                $notaFinal = $calif->nota_final_definitiva ?? 0;
                $estadoAsig = $calif->estado_asignatura ?? 'En Proceso';

                $asignaturasData[] = [
                    'nombre' => optional(optional($calif->curso_asignatura)->asignatura)->nombre ?? 'Asignatura Desconocida',
                    'nota_final' => round($notaFinal, 2),
                    'estado' => $estadoAsig
                ];

                $sumaNotasCurso += $notaFinal;
                $sumaNotasGlobal += $notaFinal;
                $totalAsignaturasGlobal++;

                if (strtolower($estadoAsig) === 'reprobado') {
                    $materiasReprobadas++;
                }
            }

            $cantidadAsignaturas = count($asignaturasData);
            if ($cantidadAsignaturas > 0) {
                $promedioCurso = round($sumaNotasCurso / $cantidadAsignaturas, 2);
                
                // Lógica simple de estado del curso: Si reprobó alguna materia, reprueba el curso.
                $estadoCurso = $materiasReprobadas > 0 ? 'Reprobado' : ($promedioCurso >= 7 ? 'Aprobado' : 'En Proceso');

                $historial[] = [
                    'periodo' => optional($curso->periodo)->nombre ?? 'S/N',
                    'nivel' => optional($curso->nivel)->nombre ?? 'S/N',
                    'especialidad' => optional($curso->especialidad)->nombre ?? 'General',
                    'paralelo' => $curso->paralelo ?? 'A',
                    'promedio_curso' => $promedioCurso,
                    'asistencia_curso' => $asistenciaCurso,
                    'estado_curso' => $estadoCurso,
                    'asignaturas' => $asignaturasData
                ];
            }
        }

        // 4. Calcular el Promedio General Histórico (Todas las materias de todos los años)
        $promedioGlobal = $totalAsignaturasGlobal > 0 ? round($sumaNotasGlobal / $totalAsignaturasGlobal, 2) : 0;

        return response()->json([
            'historial' => $historial,
            'promedio_general' => $promedioGlobal
        ], 200);
    }
}
