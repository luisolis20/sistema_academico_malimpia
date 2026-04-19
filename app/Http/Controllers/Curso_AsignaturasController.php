<?php

namespace App\Http\Controllers;

use App\Models\Curso_Asignaturas;
use App\Models\Personas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Curso_AsignaturasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $perPage = min($perPage, 20);
            $searchQuery = $request->input('search_query');

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

                // =========================================================
                // VALIDACIÓN DE MATERIAS Y PERIODO
                // =========================================================
                // 1. ¿Tiene materias asignadas? (Devuelve 1 o 0)
                DB::raw('(CASE WHEN EXISTS (SELECT 1 FROM curso_asignatura WHERE curso_asignatura.id_docente = personas.id_persona) THEN 1 ELSE 0 END) as tiene_asignaturas'),

                // 2. Obtener el nombre del periodo de esas materias asignadas
                DB::raw('(SELECT periodos_lectivos.nombre FROM curso_asignatura INNER JOIN cursos ON cursos.id_curso = curso_asignatura.id_curso INNER JOIN periodos_lectivos ON periodos_lectivos.id_periodo = cursos.id_periodo WHERE curso_asignatura.id_docente = personas.id_persona LIMIT 1) as nombre_periodo'),

                // =========================================================
                // DATOS DEL CURSO DONDE ES TUTOR
                // =========================================================
                DB::raw('IF(cursos_tutor.id_curso IS NOT NULL, 1, 0) as es_tutor_general'),
                'cursos_tutor.id_curso as tutor_curso_id',
                'niveles_tutor.nombre as tutor_nivel',
                'especialidades_tutor.nombre as tutor_especialidad',
                'cursos_tutor.paralelo as tutor_paralelo'
            )
                ->join('usuarios', 'usuarios.id_persona', '=', 'personas.id_persona')
                ->join('roles', 'roles.id_rol', '=', 'usuarios.id_rol')

                // Joins exclusivos para obtener la información del curso que tutoriza
                ->leftJoin('cursos as cursos_tutor', function ($join) {
                    $join->on('cursos_tutor.id_docente_tutor', '=', 'personas.id_persona')
                        ->where('cursos_tutor.estado', '=', 1);
                })
                ->leftJoin('niveles_academicos as niveles_tutor', 'niveles_tutor.id_nivel', '=', 'cursos_tutor.id_nivel')
                ->leftJoin('especialidades as especialidades_tutor', 'especialidades_tutor.id_especialidad', '=', 'cursos_tutor.id_especialidad')

                ->where('roles.nombre', 'LIKE', '%docente%');

            if (! empty($searchQuery)) {
                $query->where(function ($q) use ($searchQuery) {
                    $q->where('personas.cedula', 'LIKE', "%{$searchQuery}%");
                });
            }

            // Agrupamos por id_persona por si alguna otra relación intenta duplicar
            $query->groupBy('personas.id_persona', 'roles.id_rol', 'cursos_tutor.id_curso', 'niveles_tutor.nombre', 'especialidades_tutor.nombre', 'cursos_tutor.paralelo');

            $data = $query->paginate($perPage);

            if ($data->isEmpty()) {
                return response()->json(['data' => [], 'message' => 'No se encontraron datos'], 200);
            }

            $data->getCollection()->transform(function ($item) {
                $attributes = $item->getAttributes();
                foreach ($attributes as $key => $value) {
                    if ($key === 'foto' && ! empty($value)) {
                        $attributes[$key] = base64_encode($value);
                    } elseif (is_string($value) && $key !== 'foto') {
                        $attributes[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                    }
                }
                return $attributes;
            });

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
    public function procesarAsignaciones(Request $request)
    {
        $request->validate([
            'id_docente' => 'required|integer',
            'asignaciones' => 'required|array'
        ]);

        $id_docente = $request->id_docente;

        // --- 1. VALIDACIÓN DE CONFLICTOS ---
        $conflictos = [];

        foreach ($request->asignaciones as $asignacion) {
            $curso_id = $asignacion['curso_id'] ?? $asignacion['id_curso'];

            foreach ($asignacion['asignaturas'] as $asig_data) {
                $id_asignatura = $asig_data['id_asignatura'];

                // Buscamos si ya existe la combinación curso-asignatura con OTRO docente
                $existe = DB::table('curso_asignatura')
                    ->where('id_curso', $curso_id)
                    ->where('id_asignatura', $id_asignatura)
                    ->where('id_docente', '!=', $id_docente)
                    ->exists();

                if ($existe) {
                    // Si existe, guardamos los IDs conflictivos
                    $conflictos[] = [
                        'id_curso' => $curso_id,
                        'id_asignatura' => $id_asignatura
                    ];
                }
            }
        }

        // Si encontramos al menos un conflicto, abortamos y avisamos al frontend
        if (!empty($conflictos)) {
            return response()->json([
                'status' => false,
                'conflictos' => $conflictos,
                'mensaje' => 'Conflicto de asignación detectado.'
            ], 409); // Código HTTP 409: Conflict
        }
        // --- FIN DE VALIDACIÓN ---


        // 2. PROCESO NORMAL DE GUARDADO
        DB::beginTransaction();
        try {
            DB::table('curso_asignatura')
                ->where('id_docente', $id_docente)
                ->delete();

            $nuevasAsignaciones = [];

            foreach ($request->asignaciones as $asignacion) {
                $curso_id = $asignacion['curso_id'] ?? $asignacion['id_curso'];

                foreach ($asignacion['asignaturas'] as $asig_data) {
                    $nuevasAsignaciones[] = [
                        'id_curso'        => $curso_id,
                        'id_asignatura'   => $asig_data['id_asignatura'],
                        'id_docente'      => $id_docente,
                        'horas_semanales' => $asig_data['horas_semanales'],
                        'estado'          => 1,
                        'created_at'      => now(),
                        'updated_at'      => now()
                    ];
                }
            }

            if (!empty($nuevasAsignaciones)) {
                DB::table('curso_asignatura')->insert($nuevasAsignaciones);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'mensaje' => 'Asignaturas actualizadas exitosamente.'
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'mensaje' => 'Error SQL: ' . $e->getMessage()
            ], 500);
        }
    }

    // Endpoint para cargar las asignaturas actuales del docente (para el botón Ver/Editar)
    public function getPorDocente($id_docente)
    {
        try {
            // Nota: Asegúrate de usar DB::table('curso_asignatura') o el modelo correcto si te da error el nombre.
            $asignacionesDB = DB::table('curso_asignatura')->select(
                'curso_asignatura.id_curso',
                'curso_asignatura.id_asignatura',
                'curso_asignatura.horas_semanales', // La base de datos sí lo trae
                'cursos.paralelo',
                'niveles_academicos.nombre as nombre_nivel',
                'especialidades.nombre as nombre_especialidad',
                'asignaturas.nombre as nombre_asignatura'
            )
                ->join('cursos', 'cursos.id_curso', '=', 'curso_asignatura.id_curso')
                ->join('niveles_academicos', 'niveles_academicos.id_nivel', '=', 'cursos.id_nivel')
                ->join('especialidades', 'especialidades.id_especialidad', '=', 'cursos.id_especialidad')
                ->join('asignaturas', 'asignaturas.id_asignatura', '=', 'curso_asignatura.id_asignatura')
                ->where('curso_asignatura.id_docente', $id_docente)
                ->get();

            // Transformamos los datos para que coincidan con la estructura `asignacionesResumen` del Frontend
            $resultado = [];
            foreach ($asignacionesDB->groupBy('id_curso') as $curso_id => $materias) {
                $primera = $materias->first();
                $nombre_curso = $primera->nombre_nivel . ' ' . $primera->nombre_especialidad . ' "' . $primera->paralelo . '"';

                $asignaturasArray = $materias->map(function ($m) {
                    return [
                        'id_asignatura' => $m->id_asignatura,
                        'nombre' => $m->nombre_asignatura,
                        'horas_semanales' => $m->horas_semanales // <-- ¡AQUÍ ESTABA EL DETALLE!
                    ];
                })->values();

                $resultado[] = [
                    'curso_id' => $curso_id,
                    'curso_nombre' => $nombre_curso,
                    'asignaturas' => $asignaturasArray
                ];
            }

            return response()->json(['status' => true, 'data' => $resultado]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener datos: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Obtener el objeto Curso_Asignaturas con el id proporcionado
        $res = Curso_Asignaturas::find($id);
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Obtener el objeto Curso_Asignaturas con el id proporcionado
        $res = Curso_Asignaturas::find($id);
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
                    'mensaje' => 'El Curso_Asignaturas no existe (puede que ya lo haya eliminado)',
                ]);
            }
        } else {
            // Si el objeto no existe, devolver un mensaje de error en formato JSON
            return response()->json([
                'error' => true,
                'mensaje' => "El Curso_Asignaturas con id: $id no Existe",
            ]);
        }
    }

    public function habilitar(string $id)
    {
        // Obtener el objeto Curso_Asignaturas con el id proporcionado
        $res = Curso_Asignaturas::find($id);
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
                    'mensaje' => 'El Curso_Asignaturas no existe (puede que ya lo haya eliminado)',
                ]);
            }
        } else {
            // Si el objeto no existe, devolver un mensaje de error en formato JSON
            return response()->json([
                'error' => true,
                'mensaje' => "El Curso_Asignaturas con id: $id no Existe",
            ]);
        }
    }
    public function desasignarDocente(string $id)
    {
        // Buscar el curso por su ID
        $curso = Curso_Asignaturas::find($id);

        if (isset($curso)) {
            // Establecer el tutor como null (desasignar)
            $curso->id_docente = null;
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
                    'mensaje' => 'El Curso_Asignaturas no existe (puede que ya lo haya eliminado)',
                ]);
            }
        } else {
            return response()->json([
                'error' => true,
                'mensaje' => "El Curso_Asignaturas con id: $id no existe.",
            ], 404);
        }
    }
}
