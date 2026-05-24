<?php

namespace App\Http\Controllers;

use App\Models\Cronograma_matriculas;
use App\Models\Matriculas;
use App\Models\Cursos;
use App\Models\Curso_Asignaturas;
use App\Models\Niveles_academicos;
use App\Models\Periodos_lectivos;
use App\Models\Personas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class Cronograma_matriculasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perPage = min($request->input('per_page', 10), 1000);
            $searchQuery = $request->input('search_query');

            $query = DB::table('cursos')
                ->select(
                    'cursos.id_nivel',
                    'cursos.id_especialidad',
                    'cursos.id_periodo',
                    'niveles_academicos.nombre as nivel_academico',
                    'especialidades.nombre as especialidad',
                    'periodos_lectivos.nombre as periodo_lectivo',
                    'periodos_lectivos.matriculas_abiertas',
                    'periodos_lectivos.estado_activo',
                    'cronograma_matriculas.id_cronograma',
                    'cronograma_matriculas.fecha_inicio',
                    'cronograma_matriculas.fecha_fin'
                )
                ->join('niveles_academicos', 'cursos.id_nivel', '=', 'niveles_academicos.id_nivel')
                ->join('especialidades', 'cursos.id_especialidad', '=', 'especialidades.id_especialidad')
                ->join('periodos_lectivos', 'cursos.id_periodo', '=', 'periodos_lectivos.id_periodo')
                ->leftJoin('cronograma_matriculas', function ($join) {
                    $join->on('cursos.id_nivel', '=', 'cronograma_matriculas.id_nivel')
                        ->on('cursos.id_especialidad', '=', 'cronograma_matriculas.id_especialidad')
                        ->on('cursos.id_periodo', '=', 'cronograma_matriculas.id_periodo');
                })
                ->where('periodos_lectivos.estado_activo', 1)
                ->where('cursos.estado', 1);

            if (!empty($searchQuery)) {
                $query->where(function ($q) use ($searchQuery) {
                    $q->where('niveles_academicos.nombre', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('periodos_lectivos.nombre', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('especialidades.nombre', 'LIKE', "%{$searchQuery}%");
                });
            }

            // Agrupamos para evitar duplicados en la paginación
            $query->groupBy(
                'cursos.id_nivel',
                'cursos.id_especialidad',
                'cursos.id_periodo',
                'niveles_academicos.nombre',
                'especialidades.nombre',
                'periodos_lectivos.nombre',
                'periodos_lectivos.matriculas_abiertas',
                'periodos_lectivos.estado_activo',
                'cronograma_matriculas.id_cronograma',
                'cronograma_matriculas.fecha_inicio',
                'cronograma_matriculas.fecha_fin'
            );

            // Ordenamiento Lógico
            $query->orderByRaw("
                CASE 
                    -- Grupo 1 CORREGIDO: Exactamente '0', o que empiece con '0 ', o tenga 'Inicial'
                    WHEN niveles_academicos.nombre = '0' 
                         OR niveles_academicos.nombre LIKE '0 %' 
                         OR niveles_academicos.nombre LIKE '%Inicial%' 
                         OR especialidades.nombre LIKE '%Inicial%' THEN 1
                    
                    -- Grupo 2: Si la tabla especialidades contiene la palabra 'Básica'
                    WHEN especialidades.nombre LIKE '%Básica%' THEN 2
                    
                    -- Grupo 3: Si el nivel académico contiene la palabra 'Bachillerato'
                    WHEN niveles_academicos.nombre LIKE '%Bachillerato%' THEN 3
                    
                    -- Otros casos
                    ELSE 4
                END ASC
            ")
                // Ordenamos numéricamente dentro de cada grupo
                ->orderByRaw("CAST(niveles_academicos.nombre AS UNSIGNED) ASC");

            $data = $query->paginate($perPage);

            if ($data->isEmpty()) {
                return response()->json(['data' => [], 'message' => 'No se encontraron datos'], 200);
            }

            $transformedItems = collect($data->items())->map(function ($item) {
                $attributes = (array) $item;
                foreach ($attributes as $key => $value) {
                    if (is_string($value)) {
                        $attributes[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                    }
                }
                return $attributes;
            });

            return response()->json([
                'data' => $transformedItems,
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'per_page'     => $data->perPage(),
                    'total'        => $data->total(),
                    'last_page'    => $data->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener los datos: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validación de los datos que llegan del frontend
        $request->validate([
            'fecha_inicio'              => 'required|date',
            'fecha_fin'                 => 'required|date|after_or_equal:fecha_inicio',
            'niveles'                   => 'required|array|min:1',
            'niveles.*.id_nivel'        => 'required|integer',
            'niveles.*.id_especialidad' => 'required|integer',
            'niveles.*.id_periodo'      => 'required|integer',
        ], [
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
            'niveles.required'         => 'Debe seleccionar al menos un nivel académico.',
        ]);

        try {
            // 2. Iniciamos una transacción de base de datos
            // Esto asegura que si hay un error en el nivel 5, los primeros 4 no se guarden (evita datos corruptos)
            DB::beginTransaction();

            // 3. Iteramos sobre los niveles seleccionados para guardarlos
            foreach ($request->niveles as $nivel) {

                // Usamos updateOrCreate para ser precavidos. 
                // Busca si ya existe un registro con ese nivel, especialidad y periodo.
                // Si existe, le actualiza las fechas. Si no existe, lo crea nuevo.
                Cronograma_matriculas::updateOrCreate(
                    [
                        // Condiciones de búsqueda (Lo que hace único al registro)
                        'id_nivel'        => $nivel['id_nivel'],
                        'id_especialidad' => $nivel['id_especialidad'],
                        'id_periodo'      => $nivel['id_periodo'],
                    ],
                    [
                        // Datos a actualizar o insertar
                        'fecha_inicio'    => $request->fecha_inicio,
                        'fecha_fin'       => $request->fecha_fin,
                    ]
                );
            }

            // 4. Si el bucle termina sin errores, confirmamos los cambios en la DB
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cronogramas creados correctamente para ' . count($request->niveles) . ' nivel(es).'
            ], 200);
        } catch (\Exception $e) {
            // Si algo falla, revertimos todos los cambios
            DB::rollBack();

            return response()->json([
                'success' => false,
                'error'   => 'Error al procesar los cronogramas: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //Obtener el objeto Cronograma_matriculas con el id proporcionado
        $res = Cronograma_matriculas::find($id);
        //Si el objeto existe, devolver los datos en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {
            return response()->json([
                'data' => $res,
                'mensaje' => "Encontrado con Éxito!!",
            ]);
        } else {
            //Si el objeto no existe, devolver un mensaje de error en formato JSON
            return response()->json([
                'error' => true,
                'mensaje' => "La Cronograma_matriculas con id: $id no Existe",
            ]);
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //Obtener el objeto Cronograma_matriculas con el id proporcionado
        $res = Cronograma_matriculas::find($id);
        //Si el objeto existe, actualizar los datos enviados por el formulario y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {
            $res->id_periodo = $request->id_periodo;
            $res->id_nivel = $request->id_nivel;
            $res->id_especialidad = $request->id_especialidad;
            $res->fecha_inicio = $request->fecha_inicio;
            $res->fecha_fin = $request->fecha_fin;
            //Guardar los cambios en la base de datos
            if ($res->save()) {
                //Devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
                return response()->json([
                    'data' => $res,
                    'mensaje' => "Actualizado con Éxito!!",
                ]);
            } else {
                //Si ocurre algún error, devolver un mensaje de error en formato JSON
                return response()->json([
                    'error' => true,
                    'mensaje' => "Error al Actualizar",
                ]);
            }
        } else {
            //Si el objeto no existe, devolver un mensaje de error en formato JSON
            return response()->json([
                'error' => true,
                'mensaje' => "El Cronograma_matriculas con id: $id no Existe",
            ]);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function getCronogramaActivo(Request $request, string $id_estudiante)
    {
        $hoy = now();

        // 1. Obtener el periodo activo
        $periodoActivo = Periodos_lectivos::where('estado_activo', 1)->first();

        if (!$periodoActivo) {
            return response()->json(['tiene_historial' => true, 'cronogramas' => []]);
        }

        // 2. Consulta base de cronogramas vigentes por fecha
        $querySchedules = Cronograma_matriculas::with(['periodo', 'nivel', 'especialidad'])
            ->where('fecha_inicio', '<=', $hoy)
            ->where('fecha_fin', '>=', $hoy);

        // CONTROL EXPLICITO DESDE FRONTEND: Si el alumno va a Inicial por primera vez
        if ($request->query('tipo') === '0') {
            $querySchedules->whereHas('nivel', function ($q) {
                $q->where('nombre', 'LIKE', '0'); // Filtra únicamente cursos de nivel Inicial
            });

            return response()->json([
                'tiene_historial' => false,
                'cronogramas' => $querySchedules->get()
            ]);
        }

        // 3. Buscar la última matrícula histórica interna
        $ultimaMatricula = Matriculas::with(['curso.nivel', 'calificaciones'])
            ->where('id_estudiante', $id_estudiante)
            ->whereHas('curso', function ($q) use ($periodoActivo) {
                $q->where('id_periodo', '!=', $periodoActivo->id_periodo);
            })
            ->orderBy('fecha_matricula', 'desc')
            ->first();

        $nivelAnterior = null;
        $reprobo = false;

        if ($ultimaMatricula) {
            $nivelAnterior = $ultimaMatricula->curso->nivel;
            $reprobo = $ultimaMatricula->calificaciones->contains(function ($calificacion) {
                $estado = strtolower($calificacion->estado_asignatura);
                return $estado === 'reprobado' || $estado === 'pierde' || $calificacion->nota_final_definitiva < 7;
            });
        } else {
            // 4. Si no tiene historial interno, validamos si ya tiene registrado un historial externo
            $historialExterno = DB::table('historial_externo')
                ->where('id_estudiante', $id_estudiante)
                ->first();

            if ($historialExterno) {
                $nivelAnterior = Niveles_academicos::find($historialExterno->ultimo_nivel_aprobado);
                $reprobo = false; // Al ser un registro externo aprobado, se asume promoción directa
            }
        }

        // 5. Si encontramos un punto de partida previo (Interno o Externo), calculamos el siguiente nivel
        if ($nivelAnterior) {
            $jerarquiaAnterior = $nivelAnterior->orden_jerarquia;
            $jerarquiasDB = Niveles_academicos::pluck('orden_jerarquia')->unique()->toArray();

            usort($jerarquiasDB, function ($a, $b) {
                $getPeso = function ($str) {
                    $strLower = strtolower($str);
                    if (strpos($strLower, 'graduado') !== false) return 999;
                    preg_match('/\d+/', $str, $matches);
                    $num = isset($matches[0]) ? (int)$matches[0] : 0;
                    if (strpos($strLower, 'bachillerato') !== false) return $num + 10;
                    return $num;
                };
                return $getPeso($a) <=> $getPeso($b);
            });

            $ordenProgreso = array_values($jerarquiasDB);
            $indiceActual = array_search($jerarquiaAnterior, $ordenProgreso);

            if ($indiceActual !== false) {
                $indiceEsperado = $reprobo ? $indiceActual : ($indiceActual + 1);
                $jerarquiaEsperada = $ordenProgreso[$indiceEsperado] ?? 'Graduado';

                $querySchedules->whereHas('nivel', function ($q) use ($jerarquiaEsperada) {
                    $q->where('orden_jerarquia', $jerarquiaEsperada);
                });
            }

            return response()->json([
                'tiene_historial' => true,
                'cronogramas' => $querySchedules->get()
            ]);
        }

        // 6. Si llegó aquí, es un caso virgen (No tiene matrículas previas ni historial externo)
        return response()->json([
            'tiene_historial' => false,
            'cronogramas' => []
        ]);
    }
    // NUEVO ENDPOINT: Para registrar el historial externo desde el modal/formulario
    public function storeHistorialExterno(Request $request)
    {
        try {
            $request->validate([
                'id_estudiante'          => 'required|integer',
                'institucion_origen'     => 'required|string|max:200',
                'ultimo_nivel_aprobado'  => 'required|integer',
                'promedio_final'         => 'required|numeric',
                'archivo_notas'          => 'nullable|file|mimes:pdf,jpg,png|max:2048'
            ]);

            $urlArchivo = null;
            if ($request->hasFile('archivo_notas')) {
                $path = $request->file('archivo_notas')->store('historiales_externos', 'public');
                $urlArchivo = asset('storage/' . $path);
            }

            DB::table('historial_externo')->insert([
                'id_estudiante'          => $request->id_estudiante,
                'institucion_origen'     => $request->institucion_origen,
                'ultimo_nivel_aprobado'  => $request->ultimo_nivel_aprobado,
                'promedio_final'         => $request->promedio_final,
                'archivo_notas_url'      => $urlArchivo,
            ]);

            return response()->json(['status' => true, 'mensaje' => 'Historial académico externo registrado correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()], 500);
        }
    }
    public function listadoNivelesExteriores()
    {
        $niveles = DB::table('niveles_academicos')->select('id_nivel', 'nombre')->get();
        return response()->json($niveles);
    }
    public function getCursosPorCronograma(Request $request, string $id_cronograma)
    {
        try {
            // 1. Obtener el periodo lectivo activo
            $periodoActivo = Periodos_lectivos::where('estado_activo', 1)->first();

            if (!$periodoActivo) {
                return response()->json([
                    'status' => false,
                    'error' => 'No hay un periodo lectivo activo configurado.'
                ], 404);
            }

            // 2. Buscar el cronograma solicitado
            $cronograma = Cronograma_matriculas::find($id_cronograma);

            // CONTROL CLAVE: Validar que exista y que pertenezca ÚNICAMENTE al periodo activo
            if (!$cronograma || $cronograma->id_periodo != $periodoActivo->id_periodo) {
                return response()->json([
                    'status' => false,
                    'error' => 'El cronograma no existe o no corresponde al periodo lectivo activo.'
                ], 404);
            }

            // 3. Buscar cursos que coincidan con los parámetros del cronograma en el periodo activo
            $cursos = Cursos::where('id_nivel', $cronograma->id_nivel)
                ->where('id_especialidad', $cronograma->id_especialidad)
                ->where('id_periodo', $periodoActivo->id_periodo) // Forzamos el ID del periodo activo verificado
                ->where('estado', 1)
                ->get();

            return response()->json($cursos);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }

    public function crearmatricula(Request $request)
    {
        $cursoDestino = Cursos::with('nivel')->find($request->id_curso);

        if (!$cursoDestino) {
            return response()->json(['error' => true, 'mensaje' => 'El curso seleccionado no existe.'], 404);
        }

        $nivelDestino = $cursoDestino->nivel;

        // Verificar si ya está matriculado en ese periodo
        $existe = Matriculas::where('id_estudiante', $request->id_estudiante)
            ->whereHas('curso', function ($q) use ($cursoDestino) {
                $q->where('id_periodo', $cursoDestino->id_periodo);
            })->exists();

        if ($existe) {
            return response()->json(['error' => true, 'mensaje' => 'El estudiante ya está matriculado en este periodo lectivo.'], 422);
        }

        $ultimaMatricula = Matriculas::with(['curso.nivel', 'calificaciones'])
            ->where('id_estudiante', $request->id_estudiante)
            ->whereHas('curso', function ($q) use ($cursoDestino) {
                $q->where('id_periodo', '!=', $cursoDestino->id_periodo);
            })
            ->orderBy('fecha_matricula', 'desc')
            ->first();

        if ($ultimaMatricula) {
            $nivelAnterior = $ultimaMatricula->curso->nivel;
            $jerarquiaAnterior = $nivelAnterior->orden_jerarquia;
            $jerarquiaDestino = $nivelDestino->orden_jerarquia;

            $reprobo = $ultimaMatricula->calificaciones->contains(function ($calificacion) {
                $estado = strtolower($calificacion->estado_asignatura);
                return $estado === 'reprobado' || $estado === 'pierde' || $calificacion->nota_final_definitiva < 7;
            });

            // =========================================================
            // ORDENAMIENTO DINÁMICO DESDE LA BASE DE DATOS
            // =========================================================
            $jerarquiasDB = Niveles_academicos::pluck('orden_jerarquia')->unique()->toArray();

            usort($jerarquiasDB, function ($a, $b) {
                $getPeso = function ($str) {
                    $strLower = strtolower($str);
                    if (strpos($strLower, 'graduado') !== false) return 999;

                    preg_match('/\d+/', $str, $matches);
                    $num = isset($matches[0]) ? (int)$matches[0] : 0;

                    if (strpos($strLower, 'bachillerato') !== false) return $num + 10;

                    return $num;
                };
                return $getPeso($a) <=> $getPeso($b);
            });

            $ordenProgreso = array_values($jerarquiasDB);
            // =========================================================

            $indiceAnterior = array_search($jerarquiaAnterior, $ordenProgreso);
            $indiceDestino = array_search($jerarquiaDestino, $ordenProgreso);

            if ($indiceAnterior !== false && $indiceDestino !== false) {
                if ($reprobo) {
                    if ($indiceDestino !== $indiceAnterior) {
                        return response()->json([
                            'error' => true,
                            'mensaje' => "El estudiante reprobó el periodo anterior. Debe matricularse nuevamente en: {$nivelAnterior->nombre}."
                        ], 422);
                    }
                } else {
                    if ($indiceDestino !== ($indiceAnterior + 1)) {
                        $jerarquiaEsperada = $ordenProgreso[$indiceAnterior + 1] ?? 'Graduado';
                        $nivelEsperado = \App\Models\Niveles_academicos::where('orden_jerarquia', $jerarquiaEsperada)->first();
                        $nombreEsperado = $nivelEsperado ? $nivelEsperado->nombre : 'el siguiente nivel correspondiente';

                        return response()->json([
                            'error' => true,
                            'mensaje' => "El estudiante aprobó el periodo anterior. Le corresponde matricularse en: {$nombreEsperado}."
                        ], 422);
                    }
                }
            }
        }

        $matricula = new Matriculas();
        $matricula->id_estudiante = $request->id_estudiante;
        $matricula->id_curso = $request->id_curso;
        $matricula->id_representante = $request->id_representante;
        $matricula->fecha_matricula = now();
        $matricula->es_nuevo = $request->es_nuevo ?? (is_null($ultimaMatricula) ? 1 : 0);
        $matricula->estado = "Activa";
        $matricula->save();

        return response()->json(['mensaje' => 'Matrícula generada con éxito', 'data' => $matricula], 200);
    }
    public function getHistorial($id_representante)
    {
        // 1. Buscamos cuál es el periodo lectivo que está activo actualmente
        $periodoActivo = \App\Models\Periodos_lectivos::where('estado_activo', 1)->first();

        // Si por alguna razón no hay periodo activo, devolvemos un arreglo vacío
        if (!$periodoActivo) {
            return response()->json([]);
        }

        // 2. Filtramos el historial del representante estrictamente para el periodo activo
        $historial = Matriculas::where('id_representante', $id_representante)
            ->whereHas('curso', function ($query) use ($periodoActivo) {
                // Este es el filtro clave: solo cursos de este año/periodo
                $query->where('id_periodo', $periodoActivo->id_periodo);
            })
            ->with([
                'estudiante:id_persona,nombres,apellidos,cedula',
                'curso.nivel:id_nivel,nombre',
                'curso.especialidad:id_especialidad,nombre'
            ])
            ->orderBy('fecha_matricula', 'desc')
            ->get();

        // 3. Formateamos la data para el frontend
        $data = $historial->map(function ($m) {
            return [
                'id_matricula'      => $m->id_matricula,
                'id_estudiante'     => $m->id_estudiante,
                'estudiante_nombre' => $m->estudiante->nombres . ' ' . $m->estudiante->apellidos,
                'estudiante_cedula' => $m->estudiante->cedula,
                'nivel_nombre'      => $m->curso->nivel->nombre,
                'especialidad'      => $m->curso->especialidad->nombre,
                'paralelo'          => $m->curso->paralelo,
                'fecha'             => date('d/m/Y H:i', strtotime($m->fecha_matricula)),
            ];
        });

        return response()->json($data);
    }
    public function getCursosMatriculados(string $id_representante)
    {
        // 1. Buscamos el periodo lectivo activo
        $periodoActivo = Periodos_lectivos::where('estado_activo', 1)->first();

        // Si no hay periodo activo, retornamos un arreglo vacío
        if (!$periodoActivo) {
            return response()->json([]);
        }

        // 2. Obtenemos las matrículas filtrando estrictamente por los cursos del periodo activo
        $matriculas = Matriculas::where('id_representante', $id_representante)
            ->whereHas('curso', function ($query) use ($periodoActivo) {
                // Filtro clave: Solo traer matrículas asociadas a cursos de este periodo
                $query->where('id_periodo', $periodoActivo->id_periodo);
            })
            ->with([
                'estudiante',
                'curso.nivel',
                'curso.especialidad',
                'curso.docentetutor',
                'curso.curso_asignaturas.asignatura',
                'curso.curso_asignaturas.docente',
                'curso.curso_asignaturas.horarios_clases'
            ])
            ->get();

        // 3. Transformamos la colección para limpiar binarios (Tu código original)
        $matriculasLimpias = $matriculas->map(function ($matricula) {
            // Limpiar foto del Estudiante
            if ($matricula->estudiante) {
                $matricula->estudiante->foto = $matricula->estudiante->foto ? base64_encode($matricula->estudiante->foto) : null;
            }

            // Limpiar foto del Docente Tutor
            if ($matricula->curso && $matricula->curso->docentetutor) {
                $matricula->curso->docentetutor->foto = $matricula->curso->docentetutor->foto ? base64_encode($matricula->curso->docentetutor->foto) : null;
            }

            // Limpiar fotos de los Docentes de cada Asignatura
            if ($matricula->curso && $matricula->curso->curso_asignaturas) {
                $matricula->curso->curso_asignaturas->each(function ($item) {
                    if ($item->docente) {
                        $item->docente->foto = $item->docente->foto ? base64_encode($item->docente->foto) : null;
                    }
                });
            }

            return $matricula;
        });

        return response()->json($matriculasLimpias);
    }
    public function getEstudiantesPorAsignatura(string $id_docente)
    {
        $hoy = date('Y-m-d');

        // 1. Buscamos el periodo lectivo activo
        $periodoActivo = Periodos_lectivos::where('estado_activo', 1)->first();

        // Si no hay periodo activo, retornamos un arreglo vacío
        if (!$periodoActivo) {
            return response()->json([]);
        }

        // 2. Obtener las asignaturas del docente filtradas estrictamente por el periodo activo
        $asignaturas = Curso_Asignaturas::where('id_docente', $id_docente)
            ->where('estado', 1)
            ->whereHas('curso', function ($query) use ($periodoActivo) {
                // Filtro clave: Asegura que el curso al que pertenece la asignatura sea del periodo actual
                $query->where('id_periodo', $periodoActivo->id_periodo);
            })
            ->with([
                'asignatura',
                'curso.nivel',
                'curso.especialidad',
                'curso.matriculas.estudiante',
                'curso.matriculas.asistencias' // Cargamos todas las del día
            ])
            ->get();

        // 3. Transformación de datos
        $data = $asignaturas->map(function ($item) use ($hoy) {
            // Guardamos el ID de la asignatura actual para filtrar dentro del map
            $id_actual = $item->id_curso_asignatura;

            return [
                'id_curso_asignatura' => $id_actual,
                'nombre_asignatura' => $item->asignatura->nombre,
                'curso_info' => $item->curso->nivel->nombre . ' "' . $item->curso->paralelo . '"',
                'especialidad' => $item->curso->especialidad->nombre,
                'total_estudiantes' => $item->curso->matriculas->count(),
                'estudiantes' => $item->curso->matriculas->map(function ($m) use ($hoy, $id_actual) {

                    // CRÍTICO: Filtramos la asistencia que coincida con la FECHA Y la ASIGNATURA actual
                    $asistenciaHoy = $m->asistencias->where('fecha', $hoy)
                        ->where('id_curso_asignatura', $id_actual)
                        ->first();

                    return [
                        'id_persona' => $m->estudiante->id_persona,
                        'cedula' => $m->estudiante->cedula,
                        'nombres' => $m->estudiante->nombres,
                        'apellidos' => $m->estudiante->apellidos,
                        'foto' => $m->estudiante->foto ? base64_encode($m->estudiante->foto) : null,
                        'id_matricula' => $m->id_matricula,
                        'asistencia_guardada' => $asistenciaHoy ? $asistenciaHoy->estado : null
                    ];
                })->sortBy('apellidos')->values()->all() // Re-aseguramos el orden alfabético aquí
            ];
        });

        return response()->json($data);
    }
    public function buscarHistorialMatriculas(Request $request)
    {
        $cedula = $request->cedula;

        // Buscamos a la persona (estudiante) por su cédula
        $estudiante = Personas::where('cedula', $cedula)
            ->with([
                'matriculasestudiantes' => function ($query) {
                    // Unimos con cursos y periodos para poder ordenar
                    $query->join('cursos', 'matriculas.id_curso', '=', 'cursos.id_curso')
                        ->join('periodos_lectivos', 'cursos.id_periodo', '=', 'periodos_lectivos.id_periodo')
                        ->orderBy('periodos_lectivos.estado_activo', 'desc')
                        ->orderBy('periodos_lectivos.fecha_inicio', 'desc')
                        ->select('matriculas.*');
                },
                'matriculasestudiantes.curso.periodo',
                'matriculasestudiantes.curso.nivel',
                'matriculasestudiantes.curso.especialidad',
                'matriculasestudiantes.curso.docentetutor',
                'matriculasestudiantes.curso.curso_asignaturas.asignatura',
                'matriculasestudiantes.curso.curso_asignaturas.docente'
            ])->first();

        if (!$estudiante) {
            return response()->json(['success' => false, 'message' => 'No se encontró un estudiante con esa cédula.'], 404);
        }

        if ($estudiante->matriculasestudiantes->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'El estudiante no posee historial de matrículas.'], 404);
        }

        // 1. Convertir la foto del estudiante a Base64
        if ($estudiante->foto) {
            $estudiante->foto = base64_encode($estudiante->foto);
        }

        // 2. PREVENCIÓN: Ocultar el campo 'foto' de los docentes. 
        // Como también son de la tabla 'Personas', si tienen datos BLOB romperán el JSON igual que el estudiante.
        foreach ($estudiante->matriculasestudiantes as $matricula) {
            if ($matricula->curso && $matricula->curso->docentetutor) {
                $matricula->curso->docentetutor->makeHidden('foto');
            }
            if ($matricula->curso && $matricula->curso->curso_asignaturas) {
                foreach ($matricula->curso->curso_asignaturas as $curso_asignatura) {
                    if ($curso_asignatura->docente) {
                        $curso_asignatura->docente->makeHidden('foto');
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'estudiante' => $estudiante
        ]);
    }
}
