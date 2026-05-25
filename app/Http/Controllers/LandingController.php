<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Matriculas;
use App\Models\Periodos_lectivos;
use App\Models\Niveles_academicos;
use App\Models\Cronograma_matriculas;

class LandingController extends Controller
{
    public function getInformacionInicio()
    {
        // 1. Obtener el periodo lectivo activo
        $periodoActivo = Periodos_lectivos::where('estado_activo', 1)->first();

        if (!$periodoActivo) {
            return response()->json([
                'error' => 'No hay un periodo lectivo activo configurado.'
            ], 404);
        }

        // 2. Total de Docentes Activos (estado = 1)
        $totalDocentes = User::where('estado', 1)
            ->whereHas('rol', function ($query) {
                $query->where('nombre', 'Docente');
            })->count();

        // 3. Total de Estudiantes Matriculados ACTIVOS en el periodo activo
        $totalEstudiantes = Matriculas::where('estado', 'Activa') // <-- FILTRO: Solo matrículas activas
            ->whereHas('curso', function ($query) use ($periodoActivo) {
                $query->where('id_periodo', $periodoActivo->id_periodo);
            })->count();

        // 4. Información de Matrículas (Si están abiertas en el periodo)
        $matriculasAbiertas = $periodoActivo->matriculas_abiertas == 1;
        $cronogramas = [];
        if ($matriculasAbiertas) {
            $cronogramas = Cronograma_matriculas::with(['nivel', 'especialidad'])
                ->where('id_periodo', $periodoActivo->id_periodo)
                ->get()
                ->map(function ($cronograma) {
                    return [
                        'nivel' => $cronograma->nivel->nombre,
                        'especialidad' => $cronograma->especialidad ? $cronograma->especialidad->nombre : 'General',
                        'fecha_inicio' => $cronograma->fecha_inicio,
                        'fecha_fin' => $cronograma->fecha_fin,
                    ];
                });
        }

        // 4.5 Cronograma de Subida de Calificaciones (Fases)
        $cronogramaNotas = \App\Models\Control_subida_notas::where('id_periodo', $periodoActivo->id_periodo)
            ->orderBy('fecha_inicio', 'asc')
            ->get()
            ->map(function ($fase) {
                return [
                    'fase' => $fase->fase_evaluacion,
                    'fecha_inicio' => $fase->fecha_inicio,
                    'fecha_fin' => $fase->fecha_fin,
                    'habilitado' => (bool)$fase->habilitado,
                ];
            });

        // 5. Cuadro de Honor con Especialidad y Paralelo (Solo estudiantes con matrícula Activa)
        $niveles = Niveles_academicos::where('estado', 1)->get();
        $cuadroHonor = [];

        foreach ($niveles as $nivel) {
            $mejorMatricula = Matriculas::with(['estudiante', 'curso.especialidad'])
                ->where('estado', 'Activa') // <-- FILTRO: Excluye alumnos anulados del cuadro de honor
                ->whereHas('curso', function ($query) use ($periodoActivo, $nivel) {
                    $query->where('id_periodo', $periodoActivo->id_periodo)
                        ->where('id_nivel', $nivel->id_nivel);
                })
                ->whereHas('calificaciones', function ($query) {
                    $query->whereNotNull('nota_final_definitiva');
                })
                ->withAvg('calificaciones as promedio', 'nota_final_definitiva')
                ->orderByDesc('promedio')
                ->first();

            if ($mejorMatricula && $mejorMatricula->estudiante && $mejorMatricula->promedio > 0) {
                $cuadroHonor[] = [
                    'nivel' => $nivel->nombre,
                    'especialidad' => optional(optional($mejorMatricula->curso)->especialidad)->nombre ?? 'Tronco Común',
                    'paralelo' => optional($mejorMatricula->curso)->paralelo ?? 'A',
                    'estudiante' => $mejorMatricula->estudiante->nombres . ' ' . $mejorMatricula->estudiante->apellidos,
                    'promedio' => round($mejorMatricula->promedio, 2),
                    'foto' => $mejorMatricula->estudiante->foto ? base64_encode($mejorMatricula->estudiante->foto) : null,
                ];
            }
        }

        // Retornar toda la información consolidada incluyendo las notas
        return response()->json([
            'docentes_activos' => $totalDocentes,
            'estudiantes_matriculados' => $totalEstudiantes,
            'matriculas_abiertas' => $matriculasAbiertas,
            'cronogramas' => $cronogramas,
            'cronograma_notas' => $cronogramaNotas,
            'cuadro_honor' => $cuadroHonor
        ], 200);
    }
}
