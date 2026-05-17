<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Curso_Asignaturas;
use App\Models\Cursos;
use App\Models\Matriculas;
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

    public function getHistorialAsistencia($id_docente)
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
                'curso' => $asig->curso->nivel->nombre.' "'.$asig->curso->paralelo.'"',
                // Agrupamos las asistencias por fecha
                'registros' => $asig->asistencias->groupBy('fecha')->map(function ($items, $fecha) {
                    return [
                        'fecha' => $fecha,
                        'detalle' => $items->map(function ($asist) {
                            return [
                                'estudiante' => $asist->matricula->estudiante->apellidos.' '.$asist->matricula->estudiante->nombres,
                                'estado' => $asist->estado,
                            ];
                        }),
                    ];
                })->values(),
            ];
        });

        return response()->json($data);
    }

    public function getDatosTutor($id_persona)
    {
        // 1. Buscamos el curso donde el docente es tutor
        $curso = Cursos::where('id_docente_tutor', $id_persona)
            ->where('estado', 1)
            ->with(['nivel', 'especialidad', 'periodo'])
            ->first();

        if (! $curso) {
            return response()->json(['error' => 'No tienes un curso asignado como tutor'], 404);
        }

        // 2. Obtenemos los estudiantes matriculados en ese curso
        $alumnos = Matriculas::where('id_curso', $curso->id_curso)
            ->where('estado', 1)
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
            'curso' => $curso->nivel->nombre.' '.$curso->paralelo,
            'especialidad' => $curso->especialidad->nombre,
            'periodo' => $curso->periodo->nombre,
            'alumnos' => $alumnos,
        ]);
    }

    public function getDatosNotasAlumnoTutor($id_persona)
    {
        // 1. Buscamos el curso donde el docente es tutor
        $curso = Cursos::where('id_docente_tutor', $id_persona)
            ->where('estado', 1)
            ->with(['nivel', 'especialidad', 'periodo'])
            ->first();

        if (! $curso) {
            return response()->json(['error' => 'No tienes un curso asignado como tutor'], 404);
        }

        // 2. Obtenemos los estudiantes matriculados incluyendo sus calificaciones y asistencias
        $alumnos = Matriculas::where('id_curso', $curso->id_curso)
            ->where('estado', 1)
            ->with([
                'estudiante',
                'representante',
                'asistencias.curso_asignatura.asignatura',
                'calificaciones.curso_asignatura.asignatura', // Cargamos las calificaciones relacionales
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

                // Filtramos las asistencias que NO son penalizadas (Presentes y Justificadas)
                $asistenciasValidas = $m->asistencias->filter(function ($a) {
                    // Ajusta los strings según cómo los guardes exactamente en tu BD (ej: 'Presente', 'Justificado')
                    return in_array(strtolower($a->estado), ['presente', 'justificado']);
                })->count();

                // Si hay registros de asistencia, sacamos el porcentaje, caso contrario asumimos 100%
                $porcentajeAsistencia = $totalAsistencias > 0
                    ? round(($asistenciasValidas / $totalAsistencias) * 100, 2)
                    : 100.00;

                return [
                    'id_matricula' => $m->id_matricula,
                    'nota_final' => $promedioGeneral,
                    'estado' => $estadoCurso,
                    'porcentaje_asistencia' => $porcentajeAsistencia, // <-- Nuevo campo devuelto
                    'calificaciones' => $detallesNotas,
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
            'curso' => $curso->nivel->nombre.' '.$curso->paralelo,
            'especialidad' => $curso->especialidad->nombre,
            'periodo' => $curso->periodo->nombre,
            'alumnos' => $alumnos,
        ]);
    }
}
