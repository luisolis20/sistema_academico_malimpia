<?php

namespace App\Http\Controllers;

//Importación de clases necesarias para el controlador LandingController
use App\Models\User;//Importación de la clase User
use App\Models\Matriculas;//Importación de la clase Matriculas
use App\Models\Periodos_lectivos;//Importación de la clase Periodos_lectivos
use App\Models\Niveles_academicos;//Importación de la clase Niveles_academicos
use App\Models\Cronograma_matriculas;//Importación de la clase Cronograma_matriculas
use App\Models\Control_subida_notas;//Importación de la clase Control_subida_notas

//Clase LandingController que representa un controlador en la aplicación para manejar las operaciones relacionadas con la página de inicio
class LandingController extends Controller
{
    /**
     * Función que muestra la página de inicio, la cual muestra información general sobre el sistema, 
     * incluyendo el periodo lectivo activo, el número de docentes activos, 
     * el número de estudiantes matriculados y el estado de la matriculación de los estudiantes.
     */
    public function getInformacionInicio()
    {
        // 1. Obtener el periodo lectivo activo
        $periodoActivo = Periodos_lectivos::where('estado_activo', 1)->first();//Obtener el periodo lectivo activo
        //Si no hay periodo activo, devolver un mensaje de error indicando que no se encontró un periodo activo
        if (!$periodoActivo) {
            return response()->json([
                'error' => 'No hay un periodo lectivo activo configurado.'
            ], 404);
        }

        // 2. Total de Docentes Activos (estado = 1)
        $totalDocentes = User::where('estado', 1)//Filtrar solo los usuarios con el estado 1
            ->whereHas('rol', function ($query) {//Integrar la relación de rol con el usuario
                $query->where('nombre', 'Docente');//Filtrar solo los usuarios con el rol 'Docente'
            })->count();//Obtener el número de usuarios con el rol 'Docente'

        // 3. Total de Estudiantes Matriculados ACTIVOS en el periodo activo
        $totalEstudiantes = Matriculas::where('estado', 'Activa') // Filtro: Solo matrículas activas
            ->whereHas('curso', function ($query) use ($periodoActivo) {//Integrar la relación de curso con el periodo activo
                $query->where('id_periodo', $periodoActivo->id_periodo);//Filtrar solo los cursos del periodo activo
            })->count();//Obtener el número de estudiantes matriculados activos

        // 4. Información de Matrículas (Si están abiertas en el periodo)
        $matriculasAbiertas = $periodoActivo->matriculas_abiertas == 1;//Si el campo 'matriculas_abiertas' del periodo activo es 1, significa que las matrículas están abiertas
        $cronogramas = [];//Inicializar el array de cronogramas
        if ($matriculasAbiertas) {//Si las matrículas están abiertas
            $cronogramas = Cronograma_matriculas::with(['nivel', 'especialidad'])//Incluir todas las relaciones necesarias para mostrar los datos de los cronogramas de matrículas(nivel, especialidad)
                ->where('id_periodo', $periodoActivo->id_periodo)//Filtrar solo los cronogramas del periodo activo
                ->get()//Obtener los datos de la consulta
                ->map(function ($cronograma) {//Iterar sobre cada cronograma
                    return [//Devolver los datos de la nivel y especialidad junto con el cronograma
                        'nivel' => $cronograma->nivel->nombre,
                        'especialidad' => $cronograma->especialidad ? $cronograma->especialidad->nombre : 'General',
                        'fecha_inicio' => $cronograma->fecha_inicio,
                        'fecha_fin' => $cronograma->fecha_fin,
                    ];
                });
        }

        // 4.5 Cronograma de Subida de Calificaciones (Fases)
        $cronogramaNotas = Control_subida_notas::where('id_periodo', $periodoActivo->id_periodo)//Filtrar solo los cronogramas de subida de notas del periodo activo
            ->orderBy('fecha_inicio', 'asc')//Ordenar los cronogramas de subida de notas por fecha de inicio
            ->get()//Obtener los datos de la consulta
            ->map(function ($fase) {//Iterar sobre cada fase
                return [//Devolver los datos de la fase junto con el cronograma
                    'fase' => $fase->fase_evaluacion,
                    'fecha_inicio' => $fase->fecha_inicio,
                    'fecha_fin' => $fase->fecha_fin,
                    'habilitado' => (bool)$fase->habilitado,
                ];
            });

        // 5. Cuadro de Honor con Especialidad y Paralelo (Solo estudiantes con matrícula Activa)
        $niveles = Niveles_academicos::where('estado', 1)->get();//Obtener los niveles académicos activos
        $cuadroHonor = [];//Inicializar el array de cuadro de honor

        foreach ($niveles as $nivel) {//Iterar sobre cada nivel académico
            $mejorMatricula = Matriculas::with(['estudiante', 'curso.especialidad'])//Incluir todas las relaciones necesarias para mostrar los datos de los estudiantes(estudiante, curso, especialidad)
                ->where('estado', 'Activa') // <-- FILTRO: Excluye alumnos anulados del cuadro de honor
                ->whereHas('curso', function ($query) use ($periodoActivo, $nivel) {//Integrar la relación de curso con el periodo activo y el nivel
                    $query->where('id_periodo', $periodoActivo->id_periodo)//Filtrar solo los cursos del periodo activo
                        ->where('id_nivel', $nivel->id_nivel);//Filtrar solo los cursos del nivel especificado
                })
                ->whereHas('calificaciones', function ($query) {//Integrar la relación de calificaciones con el estudiante
                    $query->whereNotNull('nota_final_definitiva');//Filtrar solo las calificaciones con nota final definitiva
                })//Incluir todas las relaciones necesarias para mostrar los datos de los estudiantes(estudiante, curso, especialidad, calificaciones)
                ->withAvg('calificaciones as promedio', 'nota_final_definitiva')//Obtener el promedio de las calificaciones
                ->orderByDesc('promedio')//Ordenar los estudiantes por promedio de calificaciones
                ->first();//Obtener el primer estudiante con el promedio de calificaciones más alto
            //Si existe un estudiante con el promedio de calificaciones más alto, agregarlo al array de cuadro de honor
            if ($mejorMatricula && $mejorMatricula->estudiante && $mejorMatricula->promedio > 0) {
                $cuadroHonor[] = [//Agregar el estudiante al array de cuadro de honor
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
