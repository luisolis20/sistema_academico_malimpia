<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Curso_Asignaturas;
use App\Models\Cursos;
use App\Models\Matriculas;
use App\Models\Periodos_lectivos;
use App\Models\Control_subida_notas;
use App\Models\Conducta;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {}

    public function checkAsistenciaHoy($id_curso_asignatura)
    {
        $existe = Asistencia::where('id_curso_asignatura', $id_curso_asignatura)
            ->where('fecha', date('Y-m-d'))
            ->exists();

        return response()->json(['registrada' => $existe]);
    }

    public function storeMasivo(Request $request)
    {
        $asistencias = $request->input('asistencias'); // Array de objetos
        $fechaHoy = date('Y-m-d');
        $id_curso_asignatura = $request->input('id_curso_asignatura');

        // Validación de seguridad: No duplicar si ya se tomó hoy
        $yaExiste = Asistencia::where('id_curso_asignatura', $id_curso_asignatura)
            ->where('fecha', $fechaHoy)
            ->exists();

        if ($yaExiste) {
            return response()->json(['error' => 'La asistencia para esta asignatura ya fue registrada hoy.'], 422);
        }

        foreach ($asistencias as $asig) {
            Asistencia::create([
                'id_matricula' => $asig['id_matricula'],
                'id_curso_asignatura' => $id_curso_asignatura,
                'fecha' => $fechaHoy,
                'estado' => $asig['estado'],
            ]);
        }

        return response()->json(['msj' => 'Asistencia registrada correctamente']);
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {}

    public function habilitar(string $id) {}

    public function getHistorialAsistencia(string $id_docente)
    {
        // Obtenemos todas las asignaturas del docente con su historial completo
        $historial = Curso_Asignaturas::where('id_docente', $id_docente)
            ->with([
                'asignatura',
                'curso.nivel',
                'asistencias' => function ($q) {
                    $q->orderBy('fecha', 'desc'); // Historial desde lo más reciente
                },
                'asistencias.matricula.estudiante' => function ($q) {
                    $q->orderBy('apellidos', 'asc');
                },
            ])
            ->get();

        $data = $historial->map(function ($asig) {
            return [
                'asignatura' => $asig->asignatura->nombre,
                'curso' => $asig->curso->nivel->nombre . ' "' . $asig->curso->paralelo . '"',
                // Agrupamos las asistencias por fecha
                'registros' => $asig->asistencias->groupBy('fecha')->map(function ($items, $fecha) {
                    return [
                        'fecha' => $fecha,
                        'detalle' => $items->map(function ($asist) {
                            return [
                                'estudiante' => $asist->matricula->estudiante->apellidos . ' ' . $asist->matricula->estudiante->nombres,
                                'estado' => $asist->estado,
                            ];
                        }),
                    ];
                })->values(),
            ];
        });

        return response()->json($data);
    }

    public function getDatosTutor(string $id_persona)
    {
        // 1. Buscamos el periodo lectivo activo
        $periodoActivo = Periodos_lectivos::where('estado_activo', 1)->first();

        if (!$periodoActivo) {
            return response()->json(['error' => 'No existe un periodo lectivo activo actualmente.'], 404);
        }

        // 2. Buscamos el curso donde el docente es tutor estrictamente en el periodo activo
        $curso = Cursos::where('id_docente_tutor', $id_persona)
            ->where('estado', 1)
            ->where('id_periodo', $periodoActivo->id_periodo) // <-- Filtro clave de periodo
            ->with(['nivel', 'especialidad', 'periodo'])
            ->first();

        if (! $curso) {
            return response()->json(['error' => 'No tienes un curso asignado como tutor para el periodo actual'], 404);
        }

        // 3. Obtenemos los estudiantes matriculados en ese curso (Tu lógica original intacta)
        $alumnos = Matriculas::where('id_curso', $curso->id_curso)
            ->where('estado', '=', 'Activa')
            ->with([
                'estudiante',
                'representante',
                'asistencias.curso_asignatura.asignatura',
            ])
            ->get()
            ->map(function ($m) {
                return [
                    'id_matricula' => $m->id_matricula,
                    'estudiante' => [
                        'cedula' => $m->estudiante->cedula,
                        'nombres' => $m->estudiante->nombres,
                        'apellidos' => $m->estudiante->apellidos,
                        'sexo' => $m->estudiante->sexo,
                        'fecha_nacimiento' => $m->estudiante->fecha_nacimiento,
                        'foto' => $m->estudiante->foto ? base64_encode($m->estudiante->foto) : null,
                    ],
                    'representante' => [
                        'cedula' => $m->representante->cedula,
                        'nombres' => $m->representante->nombres,
                        'apellidos' => $m->representante->apellidos,
                        'sexo' => $m->representante->sexo,
                        'telefono' => $m->representante->telefono,
                    ],
                    'historial_asistencia' => $m->asistencias->map(function ($a) {
                        return [
                            'fecha' => $a->fecha,
                            'estado' => $a->estado,
                            'asignatura' => $a->curso_asignatura->asignatura->nombre,
                        ];
                    }),
                ];
            });

        return response()->json([
            'curso' => $curso->nivel->nombre . ' ' . $curso->paralelo,
            'especialidad' => $curso->especialidad->nombre,
            'periodo' => $curso->periodo->nombre,
            'alumnos' => $alumnos,
        ]);
    }
    public function getDatosConduta(string $id_persona)
    {
        // 1. Buscamos el periodo lectivo activo
        $periodoActivo = Periodos_lectivos::where('estado_activo', 1)->first();

        if (!$periodoActivo) {
            return response()->json(['error' => 'No existe un periodo lectivo activo actualmente.'], 404);
        }

        // 2. Buscamos el curso asignado al tutor en el periodo activo
        $curso = Cursos::where('id_docente_tutor', $id_persona)
            ->where('estado', 1)
            ->where('id_periodo', $periodoActivo->id_periodo)
            ->with(['nivel', 'especialidad', 'periodo'])
            ->first();

        if (!$curso) {
            return response()->json(['error' => 'No tienes un curso asignado como tutor para el periodo actual.'], 404);
        }

        // 3. Verificar qué Quimestres están habilitados para subida de notas
        $fasesHabilitadas = Control_subida_notas::where('id_periodo', $periodoActivo->id_periodo)
            ->where('habilitado', 1)
            ->pluck('fase_evaluacion')
            ->toArray();

        // Validamos de forma amplia si alguna fase de Q1 o Q2 está abierta
        $q1_habilitado = false;
        $q2_habilitado = false;

        foreach ($fasesHabilitadas as $fase) {
            if (str_contains($fase, 'Q1')) {
                $q1_habilitado = true;
            }
            if (str_contains($fase, 'Q2')) {
                $q2_habilitado = true;
            }
        }

        // 4. Obtener estudiantes con matrículas Activas junto con sus conductas ya registradas
        $alumnos = Matriculas::where('id_curso', $curso->id_curso)
            ->where('estado', 'Activa')
            ->with(['estudiante'])
            ->get()
            ->map(function ($m) {
                // Buscamos las conductas registradas de esta matrícula
                $conductas = Conducta::where('id_matricula', $m->id_matricula)->get();

                return [
                    'id_matricula' => $m->id_matricula,
                    'estudiante' => [
                        'cedula' => $m->estudiante->cedula,
                        'nombres' => $m->estudiante->nombres,
                        'apellidos' => $m->estudiante->apellidos,
                        'sexo' => $m->estudiante->sexo,
                        'foto' => $m->estudiante->foto ? base64_encode($m->estudiante->foto) : null,
                    ],
                    'conductas' => $conductas->map(function ($c) {
                        return [
                            'id_conducta' => $c->id_conducta,
                            'quimestre' => $c->quimestre,
                            'calificacion_letra' => $c->calificacion_letra,
                            'observacion' => $c->observacion
                        ];
                    })
                ];
            });

        return response()->json([
            'curso' => $curso->nivel->nombre . ' "' . $curso->paralelo . '"',
            'especialidad' => $curso->especialidad->nombre,
            'periodo' => $curso->periodo->nombre,
            'fases_conducta' => [
                'Q1' => $q1_habilitado,
                'Q2' => $q2_habilitado
            ],
            'alumnos' => $alumnos,
        ]);
    }
    public function guardarConducta(Request $request)
    {
        $request->validate([
            'id_matricula'       => 'required|integer',
            'quimestre'          => 'required|string', // 'Quimestre 1' o 'Quimestre 2'
            'calificacion_letra' => 'required|string|in:A,B,C,D,E',
            'observacion'        => 'nullable|string|max:500'
        ]);

        // 1. Validar la existencia de la matrícula
        $matricula = Matriculas::with('curso')->find($request->id_matricula);
        if (!$matricula) {
            return response()->json(['success' => false, 'message' => 'No se encontró la matrícula.'], 404);
        }

        // 2. Control de seguridad perimetral de fechas/fases habilitadas
        $prefix = ($request->quimestre === 'Quimestre 1') ? 'Q1' : 'Q2';
        $faseHabilitada = Control_subida_notas::where('id_periodo', $matricula->curso->id_periodo)
            ->where('fase_evaluacion', 'like', $prefix . '%')
            ->where('habilitado', 1)
            ->exists();

        if (!$faseHabilitada) {
            return response()->json([
                'success' => false,
                'message' => 'La subida de notas/conducta para este ' . $request->quimestre . ' no está habilitada en el cronograma actual.'
            ], 400);
        }

        // 3. Guardar o actualizar de forma síncrona
        $conducta = Conducta::updateOrCreate(
            [
                'id_matricula' => $request->id_matricula,
                'quimestre'    => $request->quimestre
            ],
            [
                'calificacion_letra' => $request->calificacion_letra,
                'observacion'        => $request->observacion
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Calificación de conducta guardada correctamente.',
            'conducta' => $conducta
        ]);
    }
    public function getDatosNotasAlumnoTutor(string $id_persona)
    {
        // 1. Buscamos el periodo lectivo activo
        $periodoActivo = Periodos_lectivos::where('estado_activo', 1)->first();

        if (!$periodoActivo) {
            return response()->json(['error' => 'No existe un periodo lectivo activo actualmente.'], 404);
        }

        // 2. Buscamos el curso donde el docente es tutor estrictamente en el periodo activo
        $curso = Cursos::where('id_docente_tutor', $id_persona)
            ->where('estado', 1)
            ->where('id_periodo', $periodoActivo->id_periodo)
            ->with(['nivel', 'especialidad', 'periodo'])
            ->first();

        if (! $curso) {
            return response()->json(['error' => 'No tienes un curso asignado como tutor para el periodo actual'], 404);
        }

        // 3. Obtenemos los estudiantes matriculados
        $alumnos = Matriculas::where('id_curso', $curso->id_curso)
            ->where('estado', '=', 'Activa')
            ->with([
                'estudiante',
                'representante',
                'asistencias.curso_asignatura.asignatura',
                'calificaciones.curso_asignatura.asignatura',
                'conductas' // Asegúrate de que en el modelo Matriculas esté como "public function conductas()"
            ])
            ->get()
            ->map(function ($m) {

                // --- CÁLCULO DE NOTAS ---
                $detallesNotas = $m->calificaciones->map(function ($c) {
                    $notaAsignatura = (float) ($c->nota_final_definitiva ?? $c->promedio_anual ?? 0);

                    return [
                        'asignatura' => $c->curso_asignatura->asignatura->nombre ?? 'Asignatura sin nombre',
                        'nota_final' => $notaAsignatura,
                        'estado' => $notaAsignatura >= 7 ? 'APROBADO' : 'REPROBADO',
                    ];
                });

                $promedioGeneral = $detallesNotas->count() > 0 ? round($detallesNotas->avg('nota_final'), 2) : 0.00;
                $estadoCurso = $promedioGeneral >= 7.00 ? 'APROBADO' : 'REPROBADO';

                // --- CÁLCULO DE ASISTENCIA ---
                $totalAsistencias = $m->asistencias->count();
                $asistenciasValidas = $m->asistencias->filter(function ($a) {
                    return in_array(strtolower($a->estado), ['presente', 'justificado']);
                })->count();

                $porcentajeAsistencia = $totalAsistencias > 0
                    ? round(($asistenciasValidas / $totalAsistencias) * 100, 2)
                    : 100.00;

                // --- EXTRACCIÓN Y CÁLCULO DE CONDUCTA ---
                $conductaQ1Model = $m->conductas->firstWhere('quimestre', 'Quimestre 1');
                $conductaQ2Model = $m->conductas->firstWhere('quimestre', 'Quimestre 2');

                // Extraemos la letra de forma segura
                $conductaQ1 = $conductaQ1Model ? strtoupper($conductaQ1Model->calificacion_letra) : '-';
                $conductaQ2 = $conductaQ2Model ? strtoupper($conductaQ2Model->calificacion_letra) : '-';

                // Criterio analítico de despliegue
                if ($conductaQ2 !== '-') {
                    $conductaFinal = $conductaQ2;
                } elseif ($conductaQ1 !== '-') {
                    $conductaFinal = $conductaQ1;
                } else {
                    $conductaFinal = '-';
                }

                return [
                    'id_matricula' => $m->id_matricula,
                    'nota_final' => $promedioGeneral,
                    'estado' => $estadoCurso,
                    'porcentaje_asistencia' => $porcentajeAsistencia,
                    'calificaciones' => $detallesNotas,

                    'conducta' => [
                        'q1' => $conductaQ1,
                        'q2' => $conductaQ2,
                        'final' => $conductaFinal,
                        // CORREGIDO: Añadido el operador ?-> para evitar el Server Error si el modelo es null
                        'observacion' => $conductaQ2Model?->observacion ?? $conductaQ1Model?->observacion ?? null
                    ],

                    'estudiante' => [
                        'cedula' => $m->estudiante->cedula,
                        'nombres' => $m->estudiante->nombres,
                        'apellidos' => $m->estudiante->apellidos,
                        'sexo' => $m->estudiante->sexo,
                        'fecha_nacimiento' => $m->estudiante->fecha_nacimiento,
                        'foto' => $m->estudiante->foto ? base64_encode($m->estudiante->foto) : null,
                    ],
                    'representante' => [
                        'cedula' => $m->representante->cedula,
                        'nombres' => $m->representante->nombres,
                        'apellidos' => $m->representante->apellidos,
                        'sexo' => $m->representante->sexo,
                        'telefono' => $m->representante->telefono,
                    ],
                    'historial_asistencia' => $m->asistencias->map(function ($a) {
                        return [
                            'fecha' => $a->fecha,
                            'estado' => $a->estado,
                            'asignatura' => $a->curso_asignatura->asignatura->nombre ?? 'Asignatura sin nombre',
                        ];
                    }),
                ];
            });

        return response()->json([
            'curso' => $curso->nivel->nombre . ' ' . $curso->paralelo,
            'especialidad' => $curso->especialidad->nombre,
            'periodo' => $curso->periodo->nombre,
            'alumnos' => $alumnos,
        ]);
    }
}
