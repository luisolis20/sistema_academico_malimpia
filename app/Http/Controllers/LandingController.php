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

        // 3. Total de Estudiantes Matriculados en el periodo activo
        $totalEstudiantes = Matriculas::whereHas('curso', function ($query) use ($periodoActivo) {
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

        // 5. Cuadro de Honor con Especialidad y Paralelo
        $niveles = Niveles_academicos::where('estado', 1)->get();
        $cuadroHonor = [];

        foreach ($niveles as $nivel) {
            // Cargamos la relación estudiante y curso.especialidad
            $mejorMatricula = Matriculas::with(['estudiante', 'curso.especialidad'])
                ->whereHas('curso', function ($query) use ($periodoActivo, $nivel) {
                    $query->where('id_periodo', $periodoActivo->id_periodo)
                          ->where('id_nivel', $nivel->id_nivel);
                })
                ->withAvg('calificaciones as promedio', 'nota_final_definitiva') 
                ->orderByDesc('promedio')
                ->first();

            if ($mejorMatricula && $mejorMatricula->estudiante) {
                $cuadroHonor[] = [
                    'nivel' => $nivel->nombre,
                    // Si el curso tiene especialidad la mostramos, sino 'Tronco Común' o 'General'
                    'especialidad' => optional(optional($mejorMatricula->curso)->especialidad)->nombre ?? 'Tronco Común',
                    // Ajusta 'paralelo' si el nombre de la columna en tu tabla Cursos es diferente
                    'paralelo' => optional($mejorMatricula->curso)->paralelo ?? 'A', 
                    'estudiante' => $mejorMatricula->estudiante->nombres . ' ' . $mejorMatricula->estudiante->apellidos,
                    'promedio' => round($mejorMatricula->promedio, 2),
                    'foto' => $mejorMatricula->estudiante->foto ? base64_encode($mejorMatricula->estudiante->foto) : null,
                ];
            }
        }

        // Retornar toda la información consolidada
        return response()->json([
            'docentes_activos' => $totalDocentes,
            'estudiantes_matriculados' => $totalEstudiantes,
            'matriculas_abiertas' => $matriculasAbiertas,
            'cronogramas' => $cronogramas,
            'cuadro_honor' => $cuadroHonor
        ], 200);
    }
}
