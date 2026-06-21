<?php

namespace App\Http\Controllers;
//Importación de clases necesarias para el controlador AsistenciaController
use App\Models\Asistencia;//Importación de la clase Asistencia
use App\Models\Curso_Asignaturas;//Importación de la clase Curso_Asignaturas
use App\Models\Cursos;//Importación de la clase Cursos
use App\Models\Matriculas;//Importación de la clase Matriculas
use App\Models\Periodos_lectivos;//Importación de la clase Periodos_lectivos
use App\Models\Control_subida_notas;//Importación de la clase Control_subida_notas
use App\Models\Conducta;//Importación de la clase Conducta
use Illuminate\Http\Request;//Importación de la clase Request para manejar las solicitudes HTTP

//Clase AsistenciaController que representa un controlador en la aplicación para manejar las operaciones relacionadas con las asistencias
class AsistenciaController extends Controller
{
    /**
     * Función que devuelve un registro de asistencia para un curso de asignatura específico, recibiendo como parámetros el id de la asignatura.
    */

    public function checkAsistenciaHoy(int $id_curso_asignatura)
    {
        // 1. Verificar si la asistencia para la asignatura ya se registró hoy
        $existe = Asistencia::where('id_curso_asignatura', $id_curso_asignatura)//Filtrar solo las asignaturas que se registró hoy
            ->where('fecha', date('Y-m-d'))//Filtrar solo las asignaturas que se registró hoy
            ->exists();//Verificar si la asignatura existe

        // 2. Devolver el resultado de la verificación  
        return response()->json(['registrada' => $existe]);//Devolver el resultado de la verificación
    }
    /**
     * Función para registrar asistencias masivas para un curso de asignatura específico, recibiendo como parámetros Request que contiene los datos enviados por el formulario.
     */
    public function storeMasivo(Request $request)
    {
        $asistencias = $request->input('asistencias'); // Array de objetos con los datos de las asistencias
        $fechaHoy = date('Y-m-d');//Obtener la fecha actual en formato 'YYYY-MM-DD'
        $id_curso_asignatura = $request->input('id_curso_asignatura');//Obtener el id de la asignatura

        // Validación de seguridad: No duplicar si ya se tomó hoy
        $yaExiste = Asistencia::where('id_curso_asignatura', $id_curso_asignatura)//Filtrar solo las asignaturas que se registró hoy
            ->where('fecha', $fechaHoy)//Filtrar solo las asignaturas que se registró hoy
            ->exists();//Verificar si la asignatura existe
        //Si ya existe, devolver un error 422 indicando que la asistencia para esta asignatura ya fue registrada hoy
        if ($yaExiste) {
            return response()->json(['error' => 'La asistencia para esta asignatura ya fue registrada hoy.'], 422);
        }
        //Si no, crear las asistencias utilizando un bucle foreach para iterar sobre el array de asistencias y crear un nuevo registro en la base de datos para cada una.
        foreach ($asistencias as $asig) {//Iterar sobre el array de asistencias
            Asistencia::create([//Crear un nuevo registro en la base de datos
                'id_matricula' => $asig['id_matricula'],//Asignar el id de la matrícula
                'id_curso_asignatura' => $id_curso_asignatura,//Asignar el id del curso de asignatura
                'fecha' => $fechaHoy,//Asignar la fecha de hoy
                'estado' => $asig['estado'],//Asignar el estado de la asistencia (presente, ausente, justificado)
            ]);
        }
        //Devolver un mensaje de éxito indicando que la asistencia se registró correctamente
        return response()->json(['msj' => 'Asistencia registrada correctamente']);
    }
    /**
     * Función que devuelve el historial de asistencias de un docente específico, recibiendo como parámetros el id del docente.
     */
    public function getHistorialAsistencia(string $id_docente)
    {
        // Obtenemos todas las asignaturas del docente con su historial completo
        $historial = Curso_Asignaturas::where('id_docente', $id_docente)//Filtrar solo las asignaturas del docente especificado
            ->with([//Incluir todas las relaciones necesarias para mostrar el historial completo
                'asignatura',//Asignatura
                'curso.nivel',//Nivel del curso
                'asistencias' => function ($q) {//Ordenar las asistencias por fecha descendente para mostrar el historial desde lo más reciente
                    $q->orderBy('fecha', 'desc'); // Historial desde lo más reciente
                },
                'asistencias.matricula.estudiante' => function ($q) {//Ordenar las asistencias por apellidos de la matrícula para mostrar el historial desde lo más reciente
                    $q->orderBy('apellidos', 'asc');// Ordenar por apellidos de la matrícula
                },
            ])
            ->get();//Obtener los datos de la consulta
        //Transformar los datos a un array para devolverlos en formato JSON
        $data = $historial->map(function ($asig) {//Iterar sobre cada asignatura
            return [//Devolver los datos de la asignatura
                'asignatura' => $asig->asignatura->nombre,//Asignatura
                'curso' => $asig->curso->nivel->nombre . ' "' . $asig->curso->paralelo . '"',//Curso
                // Agrupamos las asistencias por fecha
                'registros' => $asig->asistencias->groupBy('fecha')->map(function ($items, $fecha) {//Agrupar las asistencias por fecha
                    return [//Devolver los datos de la fecha
                        'fecha' => $fecha,//Fecha
                        'detalle' => $items->map(function ($asist) {//Iterar sobre cada asistencia
                            return [
                                'estudiante' => $asist->matricula->estudiante->apellidos . ' ' . $asist->matricula->estudiante->nombres,//Nombres y apellidos del estudiante
                                'estado' => $asist->estado,//Estado de la asistencia (presente, ausente, justificado)
                            ];
                        }),
                    ];
                })->values(),//Devolver los valores de la colección
            ];
        });
        //Devolver los datos en formato JSON
        return response()->json($data);
    }
    /**
     * Función que devuelve los datos de un docente específico, recibiendo como parámetros el id de la persona.
     */
    public function getDatosTutor(string $id_persona)
    {
        // 1. Buscamos el periodo lectivo activo
        $periodoActivo = Periodos_lectivos::where('estado_activo', 1)->first();//Obtener el periodo lectivo activo

        if (!$periodoActivo) {//Si no existe, devolver un mensaje de error
            return response()->json(['error' => 'No existe un periodo lectivo activo actualmente.'], 404);
        }

        // 2. Buscamos el curso donde el docente es tutor estrictamente en el periodo activo
        $curso = Cursos::where('id_docente_tutor', $id_persona)//Filtrar solo los cursos donde el docente es tutor
            ->where('estado', 1)//Filtrar solo los cursos activos
            ->where('id_periodo', $periodoActivo->id_periodo) // Filtrar solo los cursos del periodo activo
            ->with(['nivel', 'especialidad', 'periodo'])//Incluir las relaciones necesarias para mostrar los datos del curso (Nivel, Especialidad, Periodo)
            ->first();//Obtener el primer curso que cumpla con los criterios
        //Si no existe, devolver un mensaje de error
        if (! $curso) {
            return response()->json(['error' => 'No tienes un curso asignado como tutor para el periodo actual'], 404);
        }

        // 3. Obtenemos los estudiantes matriculados en ese curso
        $alumnos = Matriculas::where('id_curso', $curso->id_curso)//Filtrar solo los estudiantes matriculados en el curso
            ->where('estado', '=', 'Activa')//Filtrar solo los estudiantes con matricula activa
            ->with([
                'estudiante',
                'representante',
                'asistencias.curso_asignatura.asignatura',
            ])//Incluir todas las relaciones necesarias para mostrar los datos del estudiante(estudiante, representante, asignatura)
            ->get()//Obtener los datos de la consulta
            ->map(function ($m) {//Iterar sobre cada estudiante
                return [//Devolver los datos de la estudiante
                    'id_matricula' => $m->id_matricula,//ID de la matrícula
                    'estudiante' => [//Datos del Estudiante
                        'cedula' => $m->estudiante->cedula,
                        'nombres' => $m->estudiante->nombres,
                        'apellidos' => $m->estudiante->apellidos,
                        'sexo' => $m->estudiante->sexo,
                        'fecha_nacimiento' => $m->estudiante->fecha_nacimiento,
                        'foto' => $m->estudiante->foto ? base64_encode($m->estudiante->foto) : null,
                    ],
                    'representante' => [//Datos del Representante
                        'cedula' => $m->representante->cedula,
                        'nombres' => $m->representante->nombres,
                        'apellidos' => $m->representante->apellidos,
                        'sexo' => $m->representante->sexo,
                        'telefono' => $m->representante->telefono,
                    ],
                    'historial_asistencia' => $m->asistencias->map(function ($a) {//Iterar sobre cada asistencia
                        return [//Devolver los datos de la asistencia
                            'fecha' => $a->fecha,
                            'estado' => $a->estado,
                            'asignatura' => $a->curso_asignatura->asignatura->nombre,
                        ];
                    }),
                ];
            });
        //Devolver los datos en formato JSON
        return response()->json([
            'curso' => $curso->nivel->nombre . ' ' . $curso->paralelo,
            'especialidad' => $curso->especialidad->nombre,
            'periodo' => $curso->periodo->nombre,
            'alumnos' => $alumnos,
        ]);
    }
    /**
     * Función que devuelve los datos de un docente específico, recibiendo como parámetros el id de la persona.
     */
    public function getDatosConduta(string $id_persona)
    {
        // 1. Buscamos el periodo lectivo activo
        $periodoActivo = Periodos_lectivos::where('estado_activo', 1)->first();//Obtener el periodo lectivo activo

        if (!$periodoActivo) {//Si no existe, devolver un mensaje de error
            return response()->json(['error' => 'No existe un periodo lectivo activo actualmente.'], 404);
        }

        // 2. Buscamos el curso asignado al tutor en el periodo activo
        $curso = Cursos::where('id_docente_tutor', $id_persona)//Filtrar solo los cursos donde el docente es tutor
            ->where('estado', 1)//Filtrar solo los cursos activos
            ->where('id_periodo', $periodoActivo->id_periodo)//Filtrar solo los cursos del periodo activo
            ->with(['nivel', 'especialidad', 'periodo'])//Incluir las relaciones necesarias para mostrar los datos del curso (Nivel, Especialidad, Periodo)
            ->first();//Obtener el primer curso que cumpla con los criterios
        //Si no existe, devolver un mensaje de error
        if (!$curso) {
            return response()->json(['error' => 'No tienes un curso asignado como tutor para el periodo actual.'], 404);
        }

        // 3. Verificar qué Quimestres están habilitados para subida de notas
        $fasesHabilitadas = Control_subida_notas::where('id_periodo', $periodoActivo->id_periodo)//Filtrar solo las fases del periodo activo
            ->where('habilitado', 1)//Filtrar solo las fases habilitadas
            ->pluck('fase_evaluacion')//Obtener las fases habilitadas
            ->toArray();//Convertir la colección a un array

        // Validamos de forma amplia si alguna fase de Q1 o Q2 está abierta
        $q1_habilitado = false;
        $q2_habilitado = false;
        //Iterar sobre cada fase habilitada para determinar si corresponde a Q1 o Q2
        foreach ($fasesHabilitadas as $fase) {
            if (str_contains($fase, 'Q1')) {//Si contiene "Q1", se establece q1_habilitado a true
                $q1_habilitado = true;
            }
            if (str_contains($fase, 'Q2')) {//Si contiene "Q2", se establece q2_habilitado a true
                $q2_habilitado = true;
            }
        }

        // 4. Obtener estudiantes con matrículas Activas junto con sus conductas ya registradas
        $alumnos = Matriculas::where('id_curso', $curso->id_curso)//Filtrar solo las matrículas del curso
            ->where('estado', 'Activa')//Filtrar solo las matrículas activas
            ->with(['estudiante'])//Incluir la relación de estudiante
            ->get()//Obtener los datos de la consulta
            ->map(function ($m) {//Iterar sobre cada matrícula
                // Buscamos las conductas registradas de esta matrícula
                $conductas = Conducta::where('id_matricula', $m->id_matricula)->get();

                return [//Devolver los datos de la matrícula junto con las conductas
                    'id_matricula' => $m->id_matricula,
                    'estudiante' => [//Datos del Estudiante
                        'cedula' => $m->estudiante->cedula,
                        'nombres' => $m->estudiante->nombres,
                        'apellidos' => $m->estudiante->apellidos,
                        'sexo' => $m->estudiante->sexo,
                        'foto' => $m->estudiante->foto ? base64_encode($m->estudiante->foto) : null,
                    ],
                    'conductas' => $conductas->map(function ($c) {//Iterar sobre cada conducta
                        return [//Devolver los datos de la conducta
                            'id_conducta' => $c->id_conducta,
                            'quimestre' => $c->quimestre,
                            'calificacion_letra' => $c->calificacion_letra,
                            'observacion' => $c->observacion
                        ];
                    })
                ];
            });
        //Devolver los datos en formato JSON
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
    /** 
     * Función para guardar una conducta en la base de datos, recibiendo como parámetros Request que contiene los datos enviados por el formulario.
     */
    public function guardarConducta(Request $request)
    {
        // Validación de los datos enviados por el formulario
        $request->validate([
            'id_matricula'       => 'required|integer',
            'quimestre'          => 'required|string', // 'Quimestre 1' o 'Quimestre 2'
            'calificacion_letra' => 'required|string|in:A,B,C,D,E',
            'observacion'        => 'nullable|string|max:500'
        ]);

        // 1. Validar la existencia de la matrícula
        $matricula = Matriculas::with('curso')->find($request->id_matricula);//Obtener la matrícula con el id proporcionado
        //Si no existe, devolver un mensaje de error
        if (!$matricula) {
            return response()->json(['success' => false, 'message' => 'No se encontró la matrícula.'], 404);
        }

        // 2. Control de seguridad perimetral de fechas/fases habilitadas
        $prefix = ($request->quimestre === 'Quimestre 1') ? 'Q1' : 'Q2';//Obtener el prefijo de la fase
        $faseHabilitada = Control_subida_notas::where('id_periodo', $matricula->curso->id_periodo)//Filtrar solo las fases habilitadas del curso
            ->where('fase_evaluacion', 'like', $prefix . '%')//Filtrar solo las fases que comiencen con el prefijo
            ->where('habilitado', 1)//Filtrar solo las fases habilitadas
            ->exists();//Verificar si la fase existe
        //Si no existe, devolver un mensaje de error
        if (!$faseHabilitada) {
            //Devolver un mensaje de error indicando que la fase no está habilitada
            return response()->json([
                'success' => false,//Indicar que la operación no fue exitosa
                'message' => 'La subida de notas/conducta para este ' . $request->quimestre . ' no está habilitada en el cronograma actual.'//Mensaje específico según el quimestre
            ], 400);
        }

        // 3. Guardar o actualizar de forma síncrona
        $conducta = Conducta::updateOrCreate(//Actualizar o crear un nuevo registro de conducta
            [
                'id_matricula' => $request->id_matricula,//Asignar el id de la matrícula
                'quimestre'    => $request->quimestre//Asignar el quimestre
            ],
            [
                'calificacion_letra' => $request->calificacion_letra,//Asignar la calificación
                'observacion'        => $request->observacion//Asignar la observación
            ]
        );
        //Devolver los datos en formato JSON
        return response()->json([
            'success' => true,//Indicar que la operación fue exitosa
            'message' => 'Calificación de conducta guardada correctamente.',//Mensaje de éxito
            'conducta' => $conducta//Devolver los datos de la conducta
        ]);
    }
    /**
     * Función que devuelve los datos de un docente específico, recibiendo como parámetros el id de la persona.
     */
    public function getDatosNotasAlumnoTutor(string $id_persona)
    {
        // 1. Buscamos el periodo lectivo activo
        $periodoActivo = Periodos_lectivos::where('estado_activo', 1)->first();//Obtener el periodo lectivo activo
        //Si no existe, devolver un mensaje de error
        if (!$periodoActivo) {
            return response()->json(['error' => 'No existe un periodo lectivo activo actualmente.'], 404);
        }

        // 2. Buscamos el curso donde el docente es tutor estrictamente en el periodo activo
        $curso = Cursos::where('id_docente_tutor', $id_persona)//Filtrar solo los cursos donde el docente es tutor
            ->where('estado', 1)//Filtrar solo los cursos activos
            ->where('id_periodo', $periodoActivo->id_periodo)//Filtrar solo los cursos del periodo activo
            ->with(['nivel', 'especialidad', 'periodo'])//Incluir las relaciones necesarias para mostrar los datos del curso (Nivel, Especialidad, Periodo)
            ->first();//Obtener el primer curso que cumpla con los criterios
        //Si no existe, devolver un mensaje de error
        if (! $curso) {
            return response()->json(['error' => 'No tienes un curso asignado como tutor para el periodo actual'], 404);
        }

        // 3. Obtenemos los estudiantes matriculados
        $alumnos = Matriculas::where('id_curso', $curso->id_curso)//Filtrar solo las matrículas del curso
            ->where('estado', '=', 'Activa')//Filtrar solo las matrículas activas
            ->with([//Incluir todas las relaciones necesarias para mostrar los datos de las matrículas(estudiante, representante, asignatura, calificaciones, conductas)
                'estudiante',
                'representante',
                'asistencias.curso_asignatura.asignatura',
                'calificaciones.curso_asignatura.asignatura',
                'conductas' 
            ])
            ->get()//Obtener los datos de la consulta
            ->map(function ($m) {//Iterar sobre cada matrícula

                // Cáculo de NOTAS
                $detallesNotas = $m->calificaciones->map(function ($c) {//Iterar sobre cada calificación
                    $notaAsignatura = (float) ($c->nota_final_definitiva ?? $c->promedio_anual ?? 0);//Obtener la nota final definida o promedio anual

                    return [//Devolver los datos de la calificación
                        'asignatura' => $c->curso_asignatura->asignatura->nombre ?? 'Asignatura sin nombre',//Asignatura
                        'nota_final' => $notaAsignatura,//Nota final
                        'estado' => $notaAsignatura >= 7 ? 'APROBADO' : 'REPROBADO',//Estado de la calificación (Aprobado o Reprobado)
                    ];
                });
                //Cálculo de promedio general
                $promedioGeneral = $detallesNotas->count() > 0 ? round($detallesNotas->avg('nota_final'), 2) : 0.00;//Promedio general de las calificaciones
                $estadoCurso = $promedioGeneral >= 7.00 ? 'APROBADO' : 'REPROBADO';//Estado general del curso (Aprobado o Reprobado) basado en el promedio general

                // Cálculo de asistencias
                $totalAsistencias = $m->asistencias->count();//Total de asistencias registradas para la matrícula
                $asistenciasValidas = $m->asistencias->filter(function ($a) {//Filtrar solo las asistencias validas (Presente o Justificado)
                    return in_array(strtolower($a->estado), ['presente', 'justificado']);//Verificar si la asistencia es presente o justificada
                })->count();//Contar el número de asistencias validas
                //Cálculo de porcentaje de asistencias
                $porcentajeAsistencia = $totalAsistencias > 0 //Verificar si hay asistencias registradas
                    ? round(($asistenciasValidas / $totalAsistencias) * 100, 2)//Calcular el porcentaje de asistencias validas
                    : 100.00;//Si no hay asistencias, devolver 100.00

                // Extraer los datos de la conducta para Q1 y Q2 de forma segura
                $conductaQ1Model = $m->conductas->firstWhere('quimestre', 'Quimestre 1');//Obtener la conducta de Q1
                $conductaQ2Model = $m->conductas->firstWhere('quimestre', 'Quimestre 2');//Obtener la conducta de Q2

                // Extraemos la letra de forma segura
                $conductaQ1 = $conductaQ1Model ? strtoupper($conductaQ1Model->calificacion_letra) : '-';//Obtener la calificación de conducta de Q1 o '-' si no existe
                $conductaQ2 = $conductaQ2Model ? strtoupper($conductaQ2Model->calificacion_letra) : '-';//Obtener la calificación de conducta de Q2 o '-' si no existe

                // Criterio analítico de despliegue
                if ($conductaQ2 !== '-') {//Si la conducta de Q2 no es '-'
                    $conductaFinal = $conductaQ2;//Asignar la conducta de Q2
                } elseif ($conductaQ1 !== '-') {//Si la conducta de Q1 no es '-'
                    $conductaFinal = $conductaQ1;//Asignar la conducta de Q1
                } else {//Si ambas son '-'
                    $conductaFinal = '-';//Asignar '-' indicando que no hay calificación de conducta disponible
                }

                return [//Devolver los datos de la matrícula junto con las calificaciones y conductas
                    'id_matricula' => $m->id_matricula,
                    'nota_final' => $promedioGeneral,
                    'estado' => $estadoCurso,
                    'porcentaje_asistencia' => $porcentajeAsistencia,
                    'calificaciones' => $detallesNotas,

                    'conducta' => [//Devolver los datos de la conducta
                        'q1' => $conductaQ1,
                        'q2' => $conductaQ2,
                        'final' => $conductaFinal,
                        'observacion' => $conductaQ2Model?->observacion ?? $conductaQ1Model?->observacion ?? null
                    ],

                    'estudiante' => [//Datos del Estudiante
                        'cedula' => $m->estudiante->cedula,
                        'nombres' => $m->estudiante->nombres,
                        'apellidos' => $m->estudiante->apellidos,
                        'sexo' => $m->estudiante->sexo,
                        'fecha_nacimiento' => $m->estudiante->fecha_nacimiento,
                        'foto' => $m->estudiante->foto ? base64_encode($m->estudiante->foto) : null,
                    ],
                    'representante' => [//Datos del Representante
                        'cedula' => $m->representante->cedula,
                        'nombres' => $m->representante->nombres,
                        'apellidos' => $m->representante->apellidos,
                        'sexo' => $m->representante->sexo,
                        'telefono' => $m->representante->telefono,
                    ],
                    'historial_asistencia' => $m->asistencias->map(function ($a) {//Iterar sobre cada asistencia
                        return [//Devolver los datos de la asistencia
                            'fecha' => $a->fecha,
                            'estado' => $a->estado,
                            'asignatura' => $a->curso_asignatura->asignatura->nombre ?? 'Asignatura sin nombre',
                        ];
                    }),
                ];
            });
        //Devolver los datos en formato JSON
        return response()->json([
            'curso' => $curso->nivel->nombre . ' ' . $curso->paralelo,
            'especialidad' => $curso->especialidad->nombre,
            'periodo' => $curso->periodo->nombre,
            'alumnos' => $alumnos,
        ]);
    }
}
