<?php

namespace App\Http\Controllers;

use App\Models\Cursos;
use App\Models\Personas;
use App\Models\Familia;
use App\Models\Curso_Asignaturas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CursosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Definir el número de elementos por página, con un máximo de 50
            $perPage = $request->input('per_page', 10);
            // Limitar el número de elementos por página a 20
            $perPage = min($perPage, 20);
            // Obtener la consulta de búsqueda
            $searchQuery = $request->input('search_query');

            // 1. Obtener el PERIODO ACTIVO
            $periodoActivo = DB::table('periodos_lectivos')->where('estado_activo', 1)->first();
            $idPeriodoActivo = $periodoActivo ? $periodoActivo->id_periodo : null;
            $nombrePeriodoActivo = $periodoActivo ? $periodoActivo->nombre : 'Desconocido';

            // 2. Crear una subconsulta para obtener SOLO el último curso de cada docente
            // Esto evita que el docente salga duplicado si tiene cursos en periodos anteriores
            $ultimosCursos = DB::table('cursos')
                ->select('id_docente_tutor', DB::raw('MAX(id_curso) as ultimo_curso_id'))
                ->groupBy('id_docente_tutor');

            // 3. Crear la consulta base usando la subconsulta
            $query = Personas::select(
                'personas.id_persona as personID',
                'personas.cedula',
                'personas.nombres',
                'personas.apellidos',
                'personas.foto',
                'personas.sexo',
                'personas.fecha_nacimiento',
                'roles.id_rol as RoleID',
                'roles.nombre as nombre_rol',
                'cursos.id_curso as CursoID',
                'cursos.paralelo',
                'cursos.estado as estado_curso',
                'cursos.created_at',
                'cursos.updated_at',
                'periodos_lectivos.id_periodo as PeriodoID',
                'periodos_lectivos.nombre as nombre_periodo',
                'niveles_academicos.id_nivel as NivelID',
                'niveles_academicos.nombre as nombre_nivel',
                'especialidades.id_especialidad as EspecialidadID',
                'especialidades.nombre as nombre_especialidad'
            )
                ->join('usuarios', 'usuarios.id_persona', '=', 'personas.id_persona')
                ->join('roles', 'roles.id_rol', '=', 'usuarios.id_rol')
                // Unimos con la subconsulta para aislar solo el último curso asignado al docente
                ->leftJoinSub($ultimosCursos, 'ultimos_cursos', function ($join) {
                    $join->on('personas.id_persona', '=', 'ultimos_cursos.id_docente_tutor');
                })
                // Hacemos el join real con la tabla cursos usando el ID obtenido en la subconsulta
                ->leftJoin('cursos', 'cursos.id_curso', '=', 'ultimos_cursos.ultimo_curso_id')
                ->leftJoin('periodos_lectivos', 'periodos_lectivos.id_periodo', '=', 'cursos.id_periodo')
                ->leftJoin('niveles_academicos', 'niveles_academicos.id_nivel', '=', 'cursos.id_nivel')
                ->leftJoin('especialidades', 'especialidades.id_especialidad', '=', 'cursos.id_especialidad')
                ->where('roles.nombre', 'LIKE', '%docente%');

            // Si hay una consulta de búsqueda, aplicarla a los campos relevantes
            if (! empty($searchQuery)) {
                $query->where(function ($q) use ($searchQuery) {
                    $q->where('personas.cedula', 'LIKE', "%{$searchQuery}%");
                });
            }

            // Obtener los datos paginados
            $data = $query->paginate($perPage);

            // Si no hay datos, devolver un mensaje de error
            if ($data->isEmpty()) {
                return response()->json(['data' => [], 'message' => 'No se encontraron datos'], 200);
            }

            // Transformar los datos a UTF-8 y evaluar lógica de los periodos
            $data->getCollection()->transform(function ($item) use ($idPeriodoActivo, $nombrePeriodoActivo) {
                $attributes = $item->getAttributes();

                // Inicializar banderas para el Frontend
                $attributes['requiere_actualizacion'] = false;
                $attributes['mensaje_periodo'] = '';
                $attributes['nuevo_periodo_id'] = $idPeriodoActivo;

                // Si tiene un curso asignado y su periodo es diferente al periodo activo
                if (!empty($attributes['PeriodoID']) && $idPeriodoActivo && $attributes['PeriodoID'] != $idPeriodoActivo) {
                    $attributes['requiere_actualizacion'] = true;
                    $attributes['mensaje_periodo'] = "El periodo <b>{$attributes['nombre_periodo']}</b> ya no está activo. Actualmente estamos en el periodo <b>{$nombrePeriodoActivo}</b>.<br><br>¿Desea reasignar este mismo docente al curso del nuevo periodo?";
                }

                foreach ($attributes as $key => $value) {
                    if ($key === 'foto' && ! empty($value)) {
                        $attributes[$key] = base64_encode($value);
                    } elseif (is_string($value) && $key !== 'foto' && $key !== 'mensaje_periodo') {
                        $attributes[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                    }
                }

                return $attributes;
            });

            // Devolver los datos paginados en formato JSON
            return response()->json([
                'data' => $data->items(),
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'last_page' => $data->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al codificar los datos a JSON: ' . $e->getMessage()], 500);
        }
    }
    //Traer cursos habilitados
    public function getActivados()
    {
        try {
            $cursos = Cursos::select(
                'cursos.*',
                'niveles_academicos.id_nivel as NivelID',
                'niveles_academicos.nombre as nombre_nivel',
                'especialidades.id_especialidad as EspecialidadID',
                'especialidades.nombre as nombre_especialidad',
            )
                ->join('niveles_academicos', 'niveles_academicos.id_nivel', '=', 'cursos.id_nivel')
                ->join('especialidades', 'especialidades.id_especialidad', '=', 'cursos.id_especialidad')
                ->where('cursos.estado', 1)
                ->get();
            return response()->json([
                'status' => true,
                'data' => $cursos,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al codificar los datos a JSON: ' . $e->getMessage()], 500);
        }
    }
    public function reasignacionMasiva(Request $request)
    {
        try {
            DB::beginTransaction();

            // 1. Obtener el periodo activo
            $periodoActivo = DB::table('periodos_lectivos')->where('estado_activo', 1)->first();

            if (!$periodoActivo) {
                return response()->json(['mensaje' => 'No hay un periodo activo configurado.'], 404);
            }

            $idPeriodoActivo = $periodoActivo->id_periodo;

            // 2. Subconsulta para aislar el último curso asignado a cada docente
            $ultimosCursos = DB::table('cursos')
                ->select('id_docente_tutor', DB::raw('MAX(id_curso) as ultimo_curso_id'))
                ->groupBy('id_docente_tutor');

            // 3. Buscar a los docentes cuyo último curso NO pertenece al periodo activo
            $cursosPendientes = DB::table('cursos')
                ->joinSub($ultimosCursos, 'uc', function ($join) {
                    $join->on('cursos.id_curso', '=', 'uc.ultimo_curso_id');
                })
                ->where('cursos.id_periodo', '!=', $idPeriodoActivo)
                ->get();

            if ($cursosPendientes->isEmpty()) {
                return response()->json(['mensaje' => 'Todos los docentes ya están al día. No hay nada que reasignar.'], 404);
            }

            $nuevosRegistros = [];
            $ahora = Carbon::now();

            foreach ($cursosPendientes as $curso) {
                // Validación de seguridad: Comprobar que no se haya reasignado ya accidentalmente
                $existe = Cursos::where('id_periodo', $idPeriodoActivo)
                    ->where('id_docente_tutor', $curso->id_docente_tutor)
                    ->exists();

                if (!$existe) {
                    $nuevosRegistros[] = [
                        'id_periodo'       => $idPeriodoActivo,
                        'id_nivel'         => $curso->id_nivel,
                        'id_especialidad'  => $curso->id_especialidad,
                        'paralelo'         => $curso->paralelo,
                        'id_docente_tutor' => $curso->id_docente_tutor,
                        'estado'           => 1, // Lo dejamos activo por defecto
                        'created_at'       => $ahora,
                        'updated_at'       => $ahora,
                    ];
                }
            }

            // Si hay registros válidos, los insertamos en bloque (Mass Insert)
            if (count($nuevosRegistros) > 0) {
                Cursos::insert($nuevosRegistros);
            } else {
                return response()->json(['mensaje' => 'Los docentes ya contaban con cursos en este periodo.'], 404);
            }

            DB::commit();

            return response()->json([
                'mensaje' => 'Reasignación masiva completada con éxito. Se reasignaron ' . count($nuevosRegistros) . ' docentes.',
                'cantidad' => count($nuevosRegistros)
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error en la reasignación masiva: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Obtener los datos enviados por el formulario
        $inputs = $request->input();

        // 1. Buscar si ya existe un curso activo con esos datos
        $cursoExistente = Cursos::where('id_periodo', $inputs['id_periodo'])
            ->where('id_nivel', $inputs['id_nivel'])
            ->where('id_especialidad', $inputs['id_especialidad'])
            ->where('paralelo', $inputs['paralelo'])
            ->where('estado', 1) // Validamos solo los que estén activos
            ->first(); // Traemos el registro, no solo verificamos si existe

        // 2. Si el curso ya existe, verificamos si tiene docente
        if ($cursoExistente) {
            if (!is_null($cursoExistente->id_docente_tutor)) {
                // Si NO es null, significa que ya hay alguien asignado
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Ya existe un docente tutor asignado a este curso',
                ], 422);
            } else {
                // Si ES null, significa que lo desasignaron antes. 
                // Reutilizamos el registro actualizándole el nuevo docente.
                $cursoExistente->id_docente_tutor = $inputs['id_docente_tutor'];
                $cursoExistente->save();

                return response()->json([
                    'error' => false,
                    'data' => $cursoExistente,
                    'mensaje' => 'Agregado con Éxito!!', // Docente asignado al curso huérfano
                ], 200);
            }
        }

        // 3. Si no existe ningún registro previo, crear el objeto Cursos desde cero
        $res = Cursos::create($inputs);

        // Devolver los datos creados en formato JSON
        return response()->json([
            'error' => false,
            'data' => $res,
            'mensaje' => 'Agregado con Éxito!!',
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Obtener el objeto Cursos con el id proporcionado
        $res = Cursos::find($id);
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
                'mensaje' => "El Curso con id: $id no Existe",
            ]);
        }
    }
    //buscar cursos por id_docente_tutor
    public function getCursosDocente($id_docente_tutor)
    {
        try {

            $cursos = Cursos::select(
                'cursos.*',
                'niveles_academicos.id_nivel as NivelID',
                'niveles_academicos.nombre as nombre_nivel',
                'especialidades.id_especialidad as EspecialidadID',
                'especialidades.nombre as nombre_especialidad',
                'cursos.id_curso as CursoID',
                'cursos.paralelo',
                'cursos.estado as estado_curso',
                'periodos_lectivos.id_periodo as PeriodoID',
                'periodos_lectivos.nombre as nombre_periodo',
            )
                ->leftJoin('periodos_lectivos', 'periodos_lectivos.id_periodo', '=', 'cursos.id_periodo')
                ->leftJoin('niveles_academicos', 'niveles_academicos.id_nivel', '=', 'cursos.id_nivel')
                ->leftJoin('especialidades', 'especialidades.id_especialidad', '=', 'cursos.id_especialidad')
                ->where('cursos.id_docente_tutor', $id_docente_tutor)
                ->get();
            return response()->json([
                'status' => true,
                'data' => $cursos,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al codificar los datos a JSON: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Obtener el objeto Cursos con el id proporcionado
        $res = Cursos::find($id);

        // Si el objeto existe, procedemos a validar y actualizar
        if (isset($res)) {

            // 1. Validar si ya existe OTRA asignación con los mismos datos
            $existeAsignacion = Cursos::where('id_periodo', $request->id_periodo)
                ->where('id_nivel', $request->id_nivel)
                ->where('id_especialidad', $request->id_especialidad)
                ->where('paralelo', $request->paralelo)
                ->where('estado', 1)
                ->where('id_curso', '!=', $id)
                ->whereNotNull('id_docente_tutor') // IMPORTANTE: Solo choca si el otro curso YA TIENE docente
                ->exists();

            // 2. Si ya existe otro, y estamos intentando dejar este como activo, arrojamos error
            if ($existeAsignacion && $request->estado == 1) {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Ya existe otro docente asignado y activo para este nivel, especialidad y paralelo en este periodo.',
                ], 422);
            }

            // 3. Asignar los nuevos valores
            $res->id_periodo = $request->id_periodo;
            $res->id_nivel = $request->id_nivel;
            $res->id_especialidad = $request->id_especialidad;
            $res->paralelo = $request->paralelo;
            $res->id_docente_tutor = $request->id_docente_tutor;
            $res->estado = $request->estado;

            // Guardar los cambios en la base de datos
            if ($res->save()) {
                return response()->json([
                    'data' => $res,
                    'mensaje' => 'Actualizado con Éxito!!',
                ], 200);
            } else {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error al Actualizar',
                ], 500);
            }
        } else {
            // Si el objeto no existe
            return response()->json([
                'error' => true,
                'mensaje' => "El Curso con id: $id no Existe",
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Obtener el objeto Cursos con el id proporcionado
        $res = Cursos::find($id);
        // Si el objeto existe, inhabilitar el nivel académico y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {
            $res->estado = 0;
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
                    'mensaje' => 'El Curso no existe (puede que ya lo haya eliminado)',
                ]);
            }
        } else {
            // Si el objeto no existe, devolver un mensaje de error en formato JSON
            return response()->json([
                'error' => true,
                'mensaje' => "El Curso con id: $id no Existe",
            ]);
        }
    }

    public function habilitar(string $id)
    {
        // Obtener el objeto Cursos con el id proporcionado
        $res = Cursos::find($id);
        // Si el objeto existe, habilitar el nivel académico y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {
            $res->estado = 1;
            $res->save();
            $data = $res->toArray();
            if ($data) {
                // Devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
                return response()->json([
                    'data' => $data,
                    'mensaje' => 'Habilitado con Éxito!!',
                ]);
            } else {
                // Si ocurre algún error, devolver un mensaje de error en formato JSON
                return response()->json([
                    'data' => $data,
                    'mensaje' => 'El Curso no existe (puede que ya lo haya eliminado)',
                ]);
            }
        } else {
            // Si el objeto no existe, devolver un mensaje de error en formato JSON
            return response()->json([
                'error' => true,
                'mensaje' => "El Curso con id: $id no Existe",
            ]);
        }
    }
    public function desasignarDocente(string $id)
    {
        // Buscar el curso por su ID
        $curso = Cursos::find($id);

        if (isset($curso)) {
            // Establecer el tutor como null (desasignar)
            $curso->id_docente_tutor = null;
            $curso->save();
            $data = $curso->toArray();
            if ($data) {
                // Devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
                return response()->json([
                    'data' => $data,
                    'mensaje' => 'Desasignado con Éxito!!',
                ]);
            } else {
                // Si ocurre algún error, devolver un mensaje de error en formato JSON
                return response()->json([
                    'data' => $data,
                    'mensaje' => 'El Curso no existe (puede que ya lo haya eliminado)',
                ]);
            }
        } else {
            return response()->json([
                'error' => true,
                'mensaje' => "El Curso con id: $id no existe.",
            ], 404);
        }
    }
    public function getCargaAcademica($id_persona)
    {
        // 1. Obtener cursos donde es Tutor
        // Cargamos relaciones para mostrar nombres de nivel, especialidad, etc.
        $tutorias = Cursos::with(['nivel', 'especialidad', 'periodo'])
            ->where('id_docente_tutor', $id_persona)
            ->where('estado', 1)
            ->get();

        // 2. Obtener asignaturas que dicta (incluyendo a qué curso pertenecen)
        $asignaturas = Curso_Asignaturas::with(['curso.nivel', 'curso.especialidad', 'asignatura'])
            ->where('id_docente', $id_persona)
            ->where('estado', 1)
            ->get();

        return response()->json([
            'es_tutor' => $tutorias->isNotEmpty(),
            'tiene_asignaturas' => $asignaturas->isNotEmpty(),
            'tutorias' => $tutorias,
            'asignaturas' => $asignaturas
        ]);
    }
    public function verificarTutor(Request $request)
    {
        // Obtenemos el usuario autenticado (ajusta según cómo uses Sanctum o JWT)
        $usuario = auth()->user();

        // Si no hay usuario o no tiene una persona asociada, devolvemos false
        if (!$usuario || !$usuario->id_persona) {
            return response()->json(['es_tutor' => false], 200);
        }

        // Buscamos si existe al menos un curso donde este usuario sea el tutor
        // Y donde el periodo lectivo asociado esté activo
        $esTutor = Cursos::where('id_docente_tutor', $usuario->id_persona)
            ->whereHas('periodo', function ($query) {
                $query->where('estado_activo', 1); // o true, dependiendo de tu base de datos
            })
            ->exists();

        return response()->json([
            'es_tutor' => $esTutor
        ], 200);
    }
    public function TieneFamilia(Request $request)
    {
        // Obtenemos el usuario autenticado (ajusta según cómo uses Sanctum o JWT)
        $usuario = auth()->user();

        // Si no hay usuario o no tiene una persona asociada, devolvemos false
        if (!$usuario || !$usuario->id_persona) {
            return response()->json(['tiene_familia' => false], 200);
        }

        // Buscamos si existe al menos un familiar asociado a esta persona
        $tieneFamilia = Familia::where('id_representante', $usuario->id_persona)
            ->exists();

        return response()->json([
            'tiene_familia' => $tieneFamilia
        ], 200);
    }
}
