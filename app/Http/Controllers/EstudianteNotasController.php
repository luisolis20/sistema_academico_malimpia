<?php

namespace App\Http\Controllers;
//Importación de clases necesarias para el controlador EstudianteNotasController
use Illuminate\Http\Request;//Importación de la clase Request para manejar las solicitudes HTTP
use App\Models\Matriculas;//Importación de la clase Matriculas
use App\Models\Asistencia;//Importación de la clase Asistencia
use Illuminate\Support\Facades\DB;//Importación de la clase DB para acceder a las tablas de la base de datos

//Clase EstudianteNotasController que representa un controlador en la aplicación para manejar las operaciones relacionadas con los estudiantes y sus notas
class EstudianteNotasController extends Controller
{
    /**
     * Función que muestra una lista de estudiantes y sus notas, las cuales pueden ser filtradas por página y por búsqueda.
     * La función index es la encargada de mostrar una lista de estudiantes y sus notas, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     */
    public function getNotasHistorial(Request $request, string $id_persona)
    {
        // 1. Obtener todas las matrículas históricas del estudiante
        $matriculas = Matriculas::with([
            'curso.periodo',
            'curso.nivel',
            'curso.especialidad',
            'calificaciones.curso_asignatura.asignatura'
        ])//Incluir todas las relaciones necesarias para mostrar los datos de las matrículas(curso, nivel, especialidad, calificaciones)
        ->where('id_estudiante', $id_persona)//Filtrar solo las matrículas del estudiante especificado
        ->get();//Obtener los datos de la consulta
        //Si no hay datos, devolver un mensaje de error indicando que no se encontraron datos
        if ($matriculas->isEmpty()) {
            return response()->json([
                'periodos' => [],
                'periodo_activo_id' => null
            ], 200);
        }

        $datosHistorial = [];//Inicializar el array de datos históricos
        $periodoActivoId = null;//Inicializar el id del periodo activo
        //Iterar sobre cada matrícula
        foreach ($matriculas as $matricula) {
            $curso = $matricula->curso;//Obtener el curso del curso actual asignado en la matrícula
            if (!$curso || !$curso->periodo) {//Si no existe un curso o no existe un periodo, saltear la iteración
                continue;
            }

            $periodo = $curso->periodo;//Obtener el periodo del curso actual asignado en la matrícula

            // Guardamos el id del periodo marcado como activo en la BD
            if ($periodo->estado_activo == 1) {//Si el periodo está activo
                $periodoActivoId = $periodo->id_periodo;//Guardar el id del periodo activo
            }

            // 2. Calcular asistencia agrupada por Asignatura del Curso en esta matrícula
            $asistenciasData = Asistencia::where('id_matricula', $matricula->id_matricula)//Filtrar solo las asistencias del curso actual
                ->select('id_curso_asignatura',
                    DB::raw('COUNT(*) as total_clases'),
                    DB::raw('SUM(CASE WHEN estado IN ("Presente", "Justificado") THEN 1 ELSE 0 END) as asistidas')
                )//Seleccionar todas las columnas de la tabla 'asistencias', de la tabla 'curso_asignaturas', de la tabla 'calificaciones'
                ->groupBy('id_curso_asignatura')//Agrupar por 'id_curso_asignatura'
                ->get()//Obtener los datos de la consulta
                ->keyBy('id_curso_asignatura');//Agrupar por 'id_curso_asignatura'

            // 3. Procesar asignaturas y calificaciones estructuradas
            $calificacionesProcesadas = [];//Inicializar el array de calificaciones procesadas
            foreach ($matricula->calificaciones as $calif) {//Iterar sobre cada calificación
                $idCA = $calif->id_curso_asignatura;//Obtener el id de la asignatura
                $asistencia = $asistenciasData->get($idCA);//Obtener la asistencia del curso actual

                // Cálculo de porcentaje de asistencia por asignatura
                $porcentajeAsistencia = 100;//Inicializar el porcentaje de asistencia
                if ($asistencia && $asistencia->total_clases > 0) {//Si existe asistencia y hay clases asistidas
                    $porcentajeAsistencia = round(($asistencia->asistidas / $asistencia->total_clases) * 100, 2);//Calcular el porcentaje de asistencia
                }
                //Procesar la calificación
                $calificacionesProcesadas[] = [//Agregar la calificación procesada al array
                    'id_calificacion'   => $calif->id_calificacion,
                    'asignatura'        => optional(optional($calif->curso_asignatura)->asignatura)->nombre ?? 'Asignatura Desconocida',//Obtener el nombre de la asignatura
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
            //Agregar los datos del curso al array de datos históricos
            $datosHistorial[] = [//Agregar el curso al array de datos históricos
                'id_periodo'     => $periodo->id_periodo,
                'nombre_periodo' => $periodo->nombre,
                'detalles_curso' => [
                    'nivel'        => optional($curso->nivel)->nombre ?? 'N/A',//Obtener el nombre del nivel del curso
                    'especialidad' => optional($curso->especialidad)->nombre ?? 'General',//Obtener el nombre de la especialidad del curso
                    'paralelo'     => $curso->paralelo ?? 'Sin Especificar',//Obtener el paralelo del curso
                ],
                'notas' => $calificacionesProcesadas
            ];
        }

        // Si por alguna razón ningún periodo tiene estado_activo = 1, asignamos por prioridad el último
        if (!$periodoActivoId && count($datosHistorial) > 0) {
            $periodoActivoId = $datosHistorial[count($datosHistorial) - 1]['id_periodo'];//Asignar el id del periodo activo
        }
        //Devolver los datos en formato JSON
        return response()->json([
            'periodos'          => $datosHistorial,
            'periodo_activo_id' => $periodoActivoId
        ], 200);
    }
    /**
     * Función para obtener los datos completos de un estudiante específico, recibiendo como parámetro el id del estudiante.
     * La función getHistorialCompleto es la encargada de obtener los datos completos de un estudiante específico, recibiendo como parámetro el id del estudiante. 
     * La función busca los datos completos de un estudiante con el id proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. Si no encuentra el estudiante, devuelve un mensaje de error indicando que no se encontró el estudiante.
     */
    public function getHistorialCompleto(string $id_persona)
    {
        // 1. Extraer todas las matrículas del estudiante
        $matriculas = Matriculas::with([    
            'curso.periodo',
            'curso.nivel',
            'curso.especialidad',
            'calificaciones.curso_asignatura.asignatura'
        ])->where('id_estudiante', $id_persona)->get();//Obtener los datos de la consulta

        $historial = [];//Inicializar el array de datos históricos
        $sumaNotasGlobal = 0;//Inicializar la suma de notas global
        $totalAsignaturasGlobal = 0;//Inicializar el total de asignaturas global
        //Iterar sobre cada matrícula
        foreach ($matriculas as $matricula) {
            $curso = $matricula->curso;//Obtener el curso del curso actual asignado en la matrícula
            if (!$curso) continue;//Si no existe un curso, saltear la iteración

            // 2. Calcular Asistencia General del Curso
            $asistencias = Asistencia::where('id_matricula', $matricula->id_matricula)->get();//Obtener las asistencias del curso actual
            $totalClases = $asistencias->count();//Obtener el número de clases asistidas
            $asistidas = $asistencias->whereIn('estado', ['Presente', 'Justificado'])->count();//Obtener el número de asistencias presentes o justificadas
            $asistenciaCurso = $totalClases > 0 ? round(($asistidas / $totalClases) * 100, 2) : 100;//Calcular el porcentaje de asistencia

            // 3. Procesar Asignaturas y Calcular Promedio del Curso
            $asignaturasData = [];//Inicializar el array de asignaturas procesadas
            $sumaNotasCurso = 0;//Inicializar la suma de notas del curso
            $materiasReprobadas = 0;//Inicializar el número de materias reprobadas
            //Iterar sobre cada calificación
            foreach ($matricula->calificaciones as $calif) {
                $notaFinal = $calif->nota_final_definitiva ?? 0;//Obtener la nota final definitiva
                $estadoAsig = $calif->estado_asignatura ?? 'En Proceso';//Obtener el estado de la asignatura
                //Agregar la asignatura al array de asignaturas procesadas
                $asignaturasData[] = [
                    'nombre' => optional(optional($calif->curso_asignatura)->asignatura)->nombre ?? 'Asignatura Desconocida',//Obtener el nombre de la asignatura
                    'nota_final' => round($notaFinal, 2),//Obtener la nota final
                    'estado' => $estadoAsig//Obtener el estado de la asignatura
                ];
                //Sumar la nota final del curso
                $sumaNotasCurso += $notaFinal;//Sumar la nota final del curso
                $sumaNotasGlobal += $notaFinal;//Sumar la nota final global
                $totalAsignaturasGlobal++;//Incrementar contador de asignaturas global

                if (strtolower($estadoAsig) === 'reprobado') {//Si la asignatura está reprobada
                    $materiasReprobadas++;//Incrementar contador de materias reprobadas
                }
            }
            //Calcular el promedio del curso
            $cantidadAsignaturas = count($asignaturasData);//Obtener el número de asignaturas procesadas
            if ($cantidadAsignaturas > 0) {//Si hay asignaturas procesadas
                $promedioCurso = round($sumaNotasCurso / $cantidadAsignaturas, 2);//Calcular el promedio del curso
                
                // Lógica simple de estado del curso: Si reprobó alguna materia, reprueba el curso.
                $estadoCurso = $materiasReprobadas > 0 ? 'Reprobado' : ($promedioCurso >= 7 ? 'Aprobado' : 'En Proceso');
                //Agregar los datos del curso al array de datos históricos
                $historial[] = [
                    //Agregar los datos del curso al array de datos históricos
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
        //Devolver los datos en formato JSON
        return response()->json([
            'historial' => $historial,
            'promedio_general' => $promedioGlobal
        ], 200);
    }
}
