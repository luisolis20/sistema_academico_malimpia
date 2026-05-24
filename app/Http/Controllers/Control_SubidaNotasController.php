<?php

namespace App\Http\Controllers;

use App\Models\Calificaciones;
use App\Models\Control_subida_notas;
use App\Models\Curso_Asignaturas;
use App\Models\Matriculas;
use App\Models\Niveles_academicos;
use App\Models\Periodos_lectivos;
use Carbon\Carbon;
use Illuminate\Http\Request;

class Control_SubidaNotasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $periodoActivo = Periodos_lectivos::where('estado_activo', 1)->first();

            if (! $periodoActivo) {
                return response()->json(['data' => [], 'message' => 'No hay un periodo lectivo activo'], 404);
            }

            $query = Control_subida_notas::where('id_periodo', $periodoActivo->id_periodo);
            $data = $query->get();

            // MAGIA AUTOMÁTICA: Revisar si la fecha fin ya pasó para inhabilitarlos
            $now = Carbon::now();
            foreach ($data as $control) {
                if ($control->habilitado == 1 && $control->fecha_fin) {
                    $fechaFin = Carbon::parse($control->fecha_fin);
                    if ($now->greaterThanOrEqualTo($fechaFin)) {
                        $control->habilitado = 0;
                        $control->save();
                    }
                }
            }

            return response()->json([
                'data' => $data,
                'periodo_activo' => $periodoActivo,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $inputs = $request->input();

        // Validación Q1 vs Q2 si se intenta guardar como habilitado
        if (isset($inputs['habilitado']) && $inputs['habilitado'] == 1) {
            $conflicto = $this->validarConflictoFases($inputs['id_periodo'], $inputs['fase_evaluacion']);
            if ($conflicto) {
                return response()->json(['error' => true, 'mensaje' => $conflicto], 400);
            }
        }

        $res = Control_subida_notas::create($inputs);

        return response()->json([
            'data' => $res,
            'mensaje' => 'Fase configurada con Éxito!!',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Obtener el objeto Control_subida_notas con el id proporcionado
        $res = Control_subida_notas::find($id);
        // Si el objeto existe, devolver los datos en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {
            return response()->json([
                'data' => $res,
                'mensaje' => 'Encontrado con Éxito!!',
            ]);
        } else {
            // Si el objeto no existe, devolver un mensaje de error en formato JSON
            return response()->json([
                'error' => true,
                'mensaje' => "El Control de Subida de Notas con id: $id no Existe",
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $res = Control_subida_notas::find($id);

        if (isset($res)) {
            $habilitado = $request->habilitado;
            $fecha_fin = $request->fecha_fin;

            // 1. Validación Q1 vs Q2 si se intenta guardar como habilitado
            if ($habilitado == 1) {
                $conflicto = $this->validarConflictoFases($request->id_periodo, $request->fase_evaluacion);
                if ($conflicto) {
                    return response()->json(['error' => true, 'mensaje' => $conflicto], 400);
                }
            }

            // 2. Validación de Inhabilitación automática si la fecha_fin ya se superó
            if ($fecha_fin) {
                $fechaFinParseada = Carbon::parse($fecha_fin);
                if (Carbon::now()->greaterThanOrEqualTo($fechaFinParseada)) {
                    $habilitado = 0; // Forzamos a que se inhabilite
                }
            }

            $res->id_periodo = $request->id_periodo;
            $res->fase_evaluacion = $request->fase_evaluacion;
            $res->fecha_inicio = $request->fecha_inicio;
            $res->fecha_fin = $fecha_fin;
            $res->habilitado = $habilitado;

            if ($res->save()) {
                return response()->json([
                    'data' => $res,
                    'mensaje' => 'Actualizado con Éxito!! ' . ($habilitado == 0 && $request->habilitado == 1 ? '(Se inhabilitó automáticamente porque la fecha fin es pasada o actual)' : ''),
                ]);
            } else {
                return response()->json(['error' => true, 'mensaje' => 'Error al Actualizar'], 500);
            }
        } else {
            return response()->json(['error' => true, 'mensaje' => "El Control con id: $id no Existe"], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Obtener el objeto Control_subida_notas con el id proporcionado
        $res = Control_subida_notas::find($id);
        // Si el objeto existe, inhabilitar el nivel académico y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {
            $res->habilitado = 0;
            $res->save();
            $data = $res->toArray();
            if ($data) {
                // Devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
                return response()->json([
                    'data' => $data,
                    'mensaje' => 'Inhabilitado con Éxito!!',
                ]);
            } else {
                // Si ocurre algún error, devolver un mensaje de error en formato JSON
                return response()->json([
                    'data' => $data,
                    'mensaje' => 'El Control de Subida de Notas no existe (puede que ya lo haya eliminado)',
                ]);
            }
        } else {
            // Si el objeto no existe, devolver un mensaje de error en formato JSON
            return response()->json([
                'error' => true,
                'mensaje' => "El Control de Subida de Notas con id: $id no Existe",
            ]);
        }
    }

    public function habilitar(string $id)
    {
        $res = Control_subida_notas::find($id);

        if (isset($res)) {
            // Validación Q1 vs Q2 antes de habilitar
            $conflicto = $this->validarConflictoFases($res->id_periodo, $res->fase_evaluacion);
            if ($conflicto) {
                return response()->json(['error' => true, 'mensaje' => $conflicto], 400);
            }

            $res->habilitado = 1;
            $res->save();

            return response()->json([
                'data' => $res->toArray(),
                'mensaje' => 'Fase habilitada con Éxito!!',
            ]);
        } else {
            return response()->json(['error' => true, 'mensaje' => 'El Control no Existe'], 404);
        }
    }

    private function validarConflictoFases($id_periodo, $fase_evaluacion)
    {
        $isQ1 = str_starts_with($fase_evaluacion, 'Q1');
        $isQ2 = str_starts_with($fase_evaluacion, 'Q2');

        if ($isQ1) {
            $q2Activos = Control_subida_notas::where('id_periodo', $id_periodo)
                ->where('fase_evaluacion', 'LIKE', 'Q2%')
                ->where('habilitado', 1)->count();
            if ($q2Activos > 0) {
                return 'No se puede habilitar una fase Q1 porque existen fases del Q2 habilitadas.';
            }
        }

        if ($isQ2) {
            $q1Activos = Control_subida_notas::where('id_periodo', $id_periodo)
                ->where('fase_evaluacion', 'LIKE', 'Q1%')
                ->where('habilitado', 1)->count();
            if ($q1Activos > 0) {
                return 'No se puede habilitar una fase Q2 porque existen fases del Q1 habilitadas.';
            }
        }

        return null; // Sin conflictos
    }

    // 1. Obtener las asignaturas que da un docente específico
    public function getAsignaturasDocente(string $id_docente)
    {
        // 1. Buscamos el periodo lectivo activo
        $periodoActivo = Periodos_lectivos::where('estado_activo', 1)->first();

        // Si no hay periodo activo, retornamos un arreglo vacío de inmediato
        if (!$periodoActivo) {
            return response()->json([], 200);
        }

        // 2. Obtener asignaturas asegurando que el curso sea del periodo vigente
        $asignaturas = Curso_Asignaturas::with(['curso.nivel', 'curso.especialidad', 'asignatura'])
            ->where('id_docente', $id_docente)
            ->where('estado', 1)
            ->whereHas('curso', function ($query) use ($periodoActivo) {
                // Filtro clave: Solo materias vinculadas a cursos de este periodo
                $query->where('id_periodo', $periodoActivo->id_periodo);
            })
            ->get();

        return response()->json($asignaturas, 200);
    }

    // 2. Obtener estudiantes, sus calificaciones y fases activas para una asignatura
    public function getEstudiantesAsignatura($id_curso_asignatura)
    {
        // 1. Buscamos la asignatura y usamos tus relaciones anidadas que ya sabemos que funcionan
        $asignatura = Curso_Asignaturas::with([
            'curso.matriculas.estudiante',
            'curso.matriculas.calificaciones' => function ($q) use ($id_curso_asignatura) {
                // Importante: Solo traer las calificaciones de esta materia específica
                $q->where('id_curso_asignatura', $id_curso_asignatura);
            },
        ])->find($id_curso_asignatura);

        if (! $asignatura) {
            return response()->json(['mensaje' => 'Asignatura no encontrada'], 404);
        }

        // Fases habilitadas
        $fasesActivas = Control_subida_notas::where('habilitado', 1)->pluck('fase_evaluacion');

        // 2. Mapeamos las matrículas del curso igual que en tu método de asistencia
        $estudiantes = $asignatura->curso->matriculas->map(function ($m) use ($id_curso_asignatura) {
            $calificacion = $m->calificaciones->first();

            // Si no tiene registro, armamos el esqueleto en ceros
            if (! $calificacion) {
                $calificacion = [
                    'id_matricula' => $m->id_matricula,
                    'id_curso_asignatura' => $id_curso_asignatura,
                    'q1_p1' => '0.00',
                    'q1_p2' => '0.00',
                    'q1_p3' => '0.00',
                    'q1_examen' => '0.00',
                    'q1_promedio' => '0.00',
                    'q2_p1' => '0.00',
                    'q2_p2' => '0.00',
                    'q2_p3' => '0.00',
                    'q2_examen' => '0.00',
                    'q2_promedio' => '0.00',
                    'promedio_anual' => '0.00',
                    'nota_supletorio' => null,
                    'nota_remedial' => null,
                    'nota_gracia' => null,
                    'nota_final_definitiva' => '0.00',
                    'estado_asignatura' => 'Reprobado',
                ];
            }

            return [
                'id_matricula' => $m->id_matricula,
                'estudiante' => [
                    'id_persona' => $m->estudiante->id_persona,
                    'cedula' => $m->estudiante->cedula,
                    'nombres' => $m->estudiante->nombres,
                    'apellidos' => $m->estudiante->apellidos,
                    // Codificamos la foto igual que en tu código
                    'foto' => $m->estudiante->foto ? base64_encode($m->estudiante->foto) : null,
                ],
                'calificaciones' => $calificacion,
            ];
        })->sortBy(function ($item) {
            // Ordenamos alfabéticamente por apellido
            return $item['estudiante']['apellidos'];
        })->values(); // Garantiza que sea un array puro para Vue

        return response()->json([
            'estudiantes' => $estudiantes,
            'fases_activas' => $fasesActivas,
        ], 200);
    }

    // 3. Guardar las calificaciones
    public function guardarCalificaciones(Request $request)
    {
        $estudiantes = $request->estudiantes; // Array de estudiantes con sus notas

        foreach ($estudiantes as $est) {
            $datosNota = $est['calificaciones'];

            Calificaciones::updateOrCreate(
                [
                    'id_matricula' => $datosNota['id_matricula'],
                    'id_curso_asignatura' => $datosNota['id_curso_asignatura'],
                ],
                [
                    'q1_p1' => $datosNota['q1_p1'],
                    'q1_p2' => $datosNota['q1_p2'],
                    'q1_p3' => $datosNota['q1_p3'],
                    'q1_examen' => $datosNota['q1_examen'],
                    'q1_promedio' => $datosNota['q1_promedio'],
                    'q2_p1' => $datosNota['q2_p1'],
                    'q2_p2' => $datosNota['q2_p2'],
                    'q2_p3' => $datosNota['q2_p3'],
                    'q2_examen' => $datosNota['q2_examen'],
                    'q2_promedio' => $datosNota['q2_promedio'],
                    'promedio_anual' => $datosNota['promedio_anual'],
                    'nota_supletorio' => $datosNota['nota_supletorio'],
                    'nota_remedial' => $datosNota['nota_remedial'],
                    'nota_gracia' => $datosNota['nota_gracia'],
                    'nota_final_definitiva' => $datosNota['nota_final_definitiva'],
                    'estado_asignatura' => $datosNota['estado_asignatura'],
                ]
            );
        }

        return response()->json(['mensaje' => 'Calificaciones guardadas exitosamente'], 200);
    }

    public function getCalificacionesActuales($id_estudiante)
    {
        // Buscamos la matrícula del estudiante en el periodo activo
        $matricula = Matriculas::with([
            'curso.periodo',
            'curso.nivel',
            'curso.especialidad',
            'calificaciones.curso_asignatura.asignatura',
        ])
            ->where('id_estudiante', $id_estudiante)
            ->whereHas('curso.periodo', function ($query) {
                $query->where('estado_activo', 1); // Solo el periodo activo
            })
            ->first();

        if (! $matricula) {
            return response()->json([
                'status' => 'error',
                'mensaje' => 'El estudiante no tiene una matrícula activa en el periodo actual.',
            ], 404);
        }

        // Estructuramos la información del curso y periodo
        $cursoInfo = [
            'periodo' => $matricula->curso->periodo->nombre,
            'nivel' => $matricula->curso->nivel->nombre,
            'especialidad' => $matricula->curso->especialidad ? $matricula->curso->especialidad->nombre : '',
            'paralelo' => $matricula->curso->paralelo,
        ];

        // Mapeamos las calificaciones
        $calificaciones = $matricula->calificaciones->map(function ($calificacion) {
            return [
                'asignatura' => $calificacion->curso_asignatura->asignatura->nombre,
                // Quimestre 1
                'q1_p1' => $calificacion->q1_p1,
                'q1_p2' => $calificacion->q1_p2,
                'q1_p3' => $calificacion->q1_p3,
                'q1_promedio' => $calificacion->q1_promedio,

                // Quimestre 2
                'q2_p1' => $calificacion->q2_p1,
                'q2_p2' => $calificacion->q2_p2,
                'q2_p3' => $calificacion->q2_p3,
                'q2_promedio' => $calificacion->q2_promedio,
                'promedio_anual' => $calificacion->promedio_anual,
                'nota_supletorio' => $calificacion->nota_supletorio,
                'nota_remedial' => $calificacion->nota_remedial,
                'nota_gracia' => $calificacion->nota_gracia,
                'nota_final_definitiva' => $calificacion->nota_final_definitiva,
                'estado_asignatura' => $calificacion->estado_asignatura,
            ];
        });

        return response()->json([
            'status' => 'success',
            'curso' => $cursoInfo,
            'calificaciones' => $calificaciones,
        ], 200);
    }

    public function buscarPorCedula($cedula)
    {
        try {
            // Buscamos todas las matrículas asociadas a la cédula del estudiante
            $matriculas = Matriculas::whereHas('estudiante', function ($query) use ($cedula) {
                $query->where('cedula', $cedula);
            })
                ->with([
                    'estudiante',
                    'curso.periodo',
                    'curso.nivel',
                    'curso.especialidad',
                    'calificaciones.curso_asignatura.asignatura',
                    'asistencias', // <-- Relación agregada para poder calcular el porcentaje
                ])
                ->get();

            if ($matriculas->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No se encontró ningún estudiante o historial con el número de cédula proporcionado.',
                ], 404);
            }

            // Extraemos los datos del estudiante del primer registro de matrícula
            $estudiante = $matriculas->first()->estudiante;

            // Procesamos el historial de calificaciones y asistencia agrupando por cada matrícula/periodo
            $historial = $matriculas->map(function ($matricula) {
                $subpromedios = 0;
                $materiasContadas = 0;
                $reprobadas = 0;

                // --- 1. PROCESAMIENTO DE CALIFICACIONES ---
                $calificacionesProcesadas = $matricula->calificaciones->map(function ($cal) use (&$subpromedios, &$materiasContadas, &$reprobadas) {
                    $notaFinal = floatval($cal->nota_final_definitiva ?? 0);
                    $subpromedios += $notaFinal;
                    $materiasContadas++;

                    // Determinar estado de la asignatura
                    $estado = $cal->estado_asignatura;
                    if (! $estado) {
                        $estado = $notaFinal >= 7 ? 'APROBADO' : 'REPROBADO';
                    }
                    if (strtoupper($estado) === 'REPROBADO') {
                        $reprobadas++;
                    }

                    return [
                        'asignatura' => $cal->curso_asignatura->asignatura->nombre ?? 'N/A',
                        'nota_final' => $notaFinal,
                        'estado' => $estado,
                    ];
                });

                $promedioPeriodo = $materiasContadas > 0 ? round($subpromedios / $materiasContadas, 2) : 0;
                // El curso completo se aprueba si el promedio es >= 7 y no tiene materias reprobadas
                $estadoCurso = ($promedioPeriodo >= 7 && $reprobadas === 0) ? 'APROBADO' : 'REPROBADO';

                // --- 2. CÁLCULO DE ASISTENCIA ---
                $totalAsistencias = $matricula->asistencias->count();

                // Filtramos solo las que no penalizan (Presente y Justificado)
                $asistenciasValidas = $matricula->asistencias->filter(function ($a) {
                    return in_array(strtolower($a->estado), ['presente', 'justificado']);
                })->count();

                // Calculamos el porcentaje
                $porcentajeAsistencia = $totalAsistencias > 0
                    ? round(($asistenciasValidas / $totalAsistencias) * 100, 2)
                    : 100.00;

                return [
                    'id_matricula' => $matricula->id_matricula,
                    'periodo' => $matricula->curso->periodo->nombre ?? 'N/A',
                    'nivel_id' => $matricula->curso->id_nivel,
                    'nivel_nombre' => $matricula->curso->nivel->nombre ?? 'N/A',
                    'paralelo' => $matricula->curso->paralelo ?? '',
                    'especialidad' => $matricula->curso->especialidad->nombre ?? null,
                    'promedio_general' => $promedioPeriodo,
                    'estado_curso' => $estadoCurso,
                    'porcentaje_asistencia' => $porcentajeAsistencia, // <-- Nuevo dato integrado
                    'calificaciones' => $calificacionesProcesadas,
                ];
            });

            // Traemos todos los niveles académicos registrados en el sistema ordenados por jerarquía
            $nivelesSistema = Niveles_academicos::where('estado', 1)
                ->orderBy('orden_jerarquia', 'asc')
                ->get(['id_nivel', 'nombre', 'orden_jerarquia']);

            return response()->json([
                'status' => 'success',
                'estudiante' => [
                    'nombres' => $estudiante->nombres,
                    'apellidos' => $estudiante->apellidos,
                    'cedula' => $estudiante->cedula,
                    'foto' => $estudiante->foto ? base64_encode($estudiante->foto) : null,
                    'telefono' => $estudiante->telefono,
                ],
                'historial' => $historial,
                'niveles_sistema' => $nivelesSistema,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage(),
            ], 500);
        }
    }
}
