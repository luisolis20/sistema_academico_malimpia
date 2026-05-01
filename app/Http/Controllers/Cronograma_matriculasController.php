<?php

namespace App\Http\Controllers;

use App\Models\Cronograma_matriculas;
use App\Models\Matriculas;
use App\Models\Cursos;
use App\Models\Curso_Asignaturas;
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
    public function getCronogramaActivo()
    {
        $hoy = now();
        $data = Cronograma_matriculas::with(['periodo', 'nivel', 'especialidad'])
            ->where('fecha_inicio', '<=', $hoy)
            ->where('fecha_fin', '>=', $hoy)
            ->get();
        return response()->json($data);
    }
    public function getCursosPorCronograma($id_cronograma)
    {
        $cronograma = Cronograma_matriculas::find($id_cronograma);
        // Buscamos cursos que coincidan con el nivel y especialidad del cronograma
        $cursos = Cursos::where('id_nivel', $cronograma->id_nivel)
            ->where('id_especialidad', $cronograma->id_especialidad)
            ->where('id_periodo', $cronograma->id_periodo)
            ->where('estado', 1)
            ->get();
        return response()->json($cursos);
    }

    public function crearmatricula(Request $request)
    {
        // Verificar si ya está matriculado en ese periodo
        $existe = Matriculas::where('id_estudiante', $request->id_estudiante)
            ->whereHas('curso', function ($q) use ($request) {
                $q->where('id_periodo', $request->id_periodo);
            })->exists();

        if ($existe) return response()->json(['error' => true, 'mensaje' => 'El estudiante ya está matriculado.'], 422);

        $matricula = new Matriculas();
        $matricula->id_estudiante = $request->id_estudiante;
        $matricula->id_curso = $request->id_curso;
        $matricula->id_representante = $request->id_representante;
        $matricula->fecha_matricula = now();
        $matricula->es_nuevo = $request->es_nuevo ?? 0;
        $matricula->estado = 1;
        $matricula->save();

        return response()->json(['mensaje' => 'Matrícula generada con éxito', 'data' => $matricula]);
    }
    public function getHistorial($id_representante)
    {
        $historial = Matriculas::where('id_representante', $id_representante)
            ->with([
                'estudiante:id_persona,nombres,apellidos,cedula',
                'curso.nivel:id_nivel,nombre',
                'curso.especialidad:id_especialidad,nombre'
            ])
            ->orderBy('fecha_matricula', 'desc')
            ->get();

        $data = $historial->map(function ($m) {
            return [
                'id_matricula' => $m->id_matricula,
                'id_estudiante' => $m->id_estudiante,
                'estudiante_nombre' => $m->estudiante->nombres . ' ' . $m->estudiante->apellidos,
                'estudiante_cedula' => $m->estudiante->cedula,
                'nivel_nombre' => $m->curso->nivel->nombre,
                'especialidad' => $m->curso->especialidad->nombre,
                'paralelo' => $m->curso->paralelo,
                'fecha' => date('d/m/Y H:i', strtotime($m->fecha_matricula)),
            ];
        });

        return response()->json($data);
    }
    public function getCursosMatriculados($id_representante)
    {
        // Obtenemos las matrículas del representante con toda la información necesaria
        $matriculas = Matriculas::where('id_representante', $id_representante)
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

        // Transformamos la colección para limpiar binarios
        $matriculasLimpias = $matriculas->map(function ($matricula) {
            // 1. Limpiar foto del Estudiante
            if ($matricula->estudiante) {
                $matricula->estudiante->foto = $matricula->estudiante->foto ? base64_encode($matricula->estudiante->foto) : null;
            }

            // 2. Limpiar foto del Docente Tutor
            if ($matricula->curso && $matricula->curso->docentetutor) {
                $matricula->curso->docentetutor->foto = $matricula->curso->docentetutor->foto ? base64_encode($matricula->curso->docentetutor->foto) : null;
            }

            // 3. Limpiar fotos de los Docentes de cada Asignatura
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
    public function getEstudiantesPorAsignatura($id_docente)
    {
        $hoy = date('Y-m-d');

        $asignaturas = Curso_Asignaturas::where('id_docente', $id_docente)
            ->where('estado', 1)
            ->with([
                'asignatura',
                'curso.nivel',
                'curso.especialidad',
                'curso.matriculas.estudiante',
                'curso.matriculas.asistencias' // Cargamos todas las del día
            ])
            ->get();

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
}
