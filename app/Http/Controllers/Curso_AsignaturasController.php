<?php

namespace App\Http\Controllers;
//Importación de clases necesarias para el controlador Curso_AsignaturasController
use App\Models\Curso_Asignaturas;//Importación de la clase Curso_Asignaturas
use App\Models\Personas;//Importación de la clase Personas
use App\Models\Periodos_lectivos;//Importación de la clase Periodos_lectivos
use Illuminate\Http\Request;//Importación de la clase Request para manejar las solicitudes HTTP
use Illuminate\Support\Facades\DB;//Importación de la clase DB para acceder a las tablas de la base de datos

//Clase Curso_AsignaturasController que representa un controlador en la aplicación para manejar las operaciones relacionadas con los cursos de asignaturas
class Curso_AsignaturasController extends Controller
{
    /**
     * Función que muestra una lista de cursos de asignaturas, las cuales pueden ser filtradas por página y por búsqueda.
     * La función index es la encargada de mostrar una lista de cursos de asignaturas, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);//Obtener el número de elementos por página, limitando el número de elementos por página a 10
            $perPage = min($perPage, 20);//Limitar el número de elementos por página a 20
            $searchQuery = $request->input('search_query');//Obtener la consulta de búsqueda

            $periodoActivo = DB::table('periodos_lectivos')->where('estado_activo', 1)->first();//Obtener el periodo lectivo activo
            $idPeriodoActivo = $periodoActivo ? $periodoActivo->id_periodo : 0;//Obtener el id del periodo lectivo activo
            //Consulta avanzada para obtener las asignaturas que pertenecen a cada docente y en qué curso las da
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
                // VALIDACIÓN DE MATERIAS Y PERIODO (SOLO PERIODO ACTIVO)
                // =========================================================
                // 1. ¿Tiene materias asignadas en el periodo activo?
                DB::raw("(CASE WHEN EXISTS (
                SELECT 1 FROM curso_asignatura 
                INNER JOIN cursos ON cursos.id_curso = curso_asignatura.id_curso 
                WHERE curso_asignatura.id_docente = personas.id_persona 
                AND cursos.id_periodo = {$idPeriodoActivo}
            ) THEN 1 ELSE 0 END) as tiene_asignaturas"),

                // 2. Obtener el nombre del periodo de esas materias asignadas
                DB::raw("(SELECT periodos_lectivos.nombre FROM curso_asignatura 
                INNER JOIN cursos ON cursos.id_curso = curso_asignatura.id_curso 
                INNER JOIN periodos_lectivos ON periodos_lectivos.id_periodo = cursos.id_periodo 
                WHERE curso_asignatura.id_docente = personas.id_persona 
                AND cursos.id_periodo = {$idPeriodoActivo} LIMIT 1) as nombre_periodo"),

                // =========================================================
                // DATOS DEL CURSO DONDE ES TUTOR
                // =========================================================
                DB::raw('IF(cursos_tutor.id_curso IS NOT NULL, 1, 0) as es_tutor_general'),
                'cursos_tutor.id_curso as tutor_curso_id',
                'niveles_tutor.nombre as tutor_nivel',
                'especialidades_tutor.nombre as tutor_especialidad',
                'cursos_tutor.paralelo as tutor_paralelo'
            )//Seleccionar todas las columnas de la tabla 'personas', de la tabla 'roles' y de la tabla 'usuarios'
                ->join('usuarios', 'usuarios.id_persona', '=', 'personas.id_persona')//Unir la tabla 'usuarios' con la tabla 'personas' en base a la columna 'id_persona'
                ->join('roles', 'roles.id_rol', '=', 'usuarios.id_rol')//Unir la tabla 'roles' con la tabla 'usuarios' en base a la columna 'id_rol'

                // Joins exclusivos para obtener la información del curso que tutoriza en el PERIODO ACTIVO
                ->leftJoin('cursos as cursos_tutor', function ($join) use ($idPeriodoActivo) {
                    $join->on('cursos_tutor.id_docente_tutor', '=', 'personas.id_persona')
                        ->where('cursos_tutor.estado', '=', 1)
                        ->where('cursos_tutor.id_periodo', '=', $idPeriodoActivo); // Filtro clave aquí
                })
                ->leftJoin('niveles_academicos as niveles_tutor', 'niveles_tutor.id_nivel', '=', 'cursos_tutor.id_nivel')//Unir la tabla 'niveles_academicos' con la tabla 'cursos' en base a la columna 'id_nivel'
                ->leftJoin('especialidades as especialidades_tutor', 'especialidades_tutor.id_especialidad', '=', 'cursos_tutor.id_especialidad')//Unir la tabla 'especialidades' con la tabla 'cursos' en base a la columna 'id_especialidad'

                ->where('roles.nombre', 'LIKE', '%docente%');//Filtrar solo las personas con el rol 'docente'

            if (! empty($searchQuery)) {//Si hay una consulta de búsqueda, aplicarla a los campos relevantes
                $query->where(function ($q) use ($searchQuery) {
                    $q->where('personas.cedula', 'LIKE', "%{$searchQuery}%");
                });
            }

            // Agrupamos por id_persona por si alguna otra relación intenta duplicar
            $query->groupBy(
                'personas.id_persona',
                'roles.id_rol',
                'cursos_tutor.id_curso',
                'niveles_tutor.nombre',
                'especialidades_tutor.nombre',
                'cursos_tutor.paralelo'
            );

            $data = $query->paginate($perPage);//Obtener los datos paginados
            //Si no hay datos, devolver un mensaje de error indicando que no se encontraron datos
            if ($data->isEmpty()) {
                return response()->json(['data' => [], 'message' => 'No se encontraron datos'], 200);
            }
            //Transformar los datos a UTF-8 para evitar problemas de codificación al convertir a JSON
            $data->getCollection()->transform(function ($item) {//Iterar sobre cada elemento de la colección
                $attributes = $item->getAttributes();//Convertir el elemento a un array
                foreach ($attributes as $key => $value) {//Iterar sobre cada clave-valor del array
                    if ($key === 'foto' && ! empty($value)) {//Si el valor es una cadena de caracteres
                        $attributes[$key] = base64_encode($value);//Convertir la cadena de caracteres a base64
                    } elseif (is_string($value) && $key !== 'foto') {//Si el valor es una cadena de caracteres
                        $attributes[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');//Convertir la cadena de caracteres a UTF-8
                    }
                }
                return $attributes;//Devolver los atributos del elemento transformados
            });
            //Devolver los datos paginados en formato JSON, incluyendo la información de paginación
            return response()->json([
                'data' => $data->items(),
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'last_page' => $data->lastPage(),
                ],
            ], 200);
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al procesar los datos: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Función para procesar las asignaciones de un docente específico, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     * La función procesarAsignaciones es la encargada de procesar las asignaciones de un docente específico, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario. 
     * Antes de procesar las asignaciones, se realiza una validación para asegurar que se encuentra con el periodo lectivo activo y que no se encuentre con conflictos entre las asignaturas. Si se intenta procesar asignaciones con un periodo lectivo activo que ya no es el periodo lectivo activo, se devuelve un mensaje de error indicando el conflicto. Si se intenta procesar asignaciones con conflictos, se devuelve un mensaje de error indicando el conflicto. Si la validación pasa sin problemas, se procede a procesar las asignaciones y se devuelve un mensaje de éxito junto con los datos del nuevo registro.
     */
    public function procesarAsignaciones(Request $request)
    {
        $request->validate([
            'id_docente' => 'required|integer',
            'asignaciones' => 'required|array'
        ]);//Validar los datos enviados por el formulario

        $id_docente = $request->id_docente;//Obtener el id del docente

        // --- 0. OBTENER EL PERIODO ACTIVO ---
        $periodoActivo = DB::table('periodos_lectivos')->where('estado_activo', 1)->first();
        //Si no existe un periodo activo, devolver un mensaje de error indicando que no se encontró un periodo activo
        if (!$periodoActivo) {
            return response()->json([
                'status' => false,
                'mensaje' => 'No se encontró un periodo lectivo activo en el sistema.'
            ], 400);
        }

        $idPeriodoActivo = $periodoActivo->id_periodo;//Obtener el id del periodo lectivo activo

        // Validación de CONFLICTOS
        $conflictos = [];
        //Recorrer cada asignación
        foreach ($request->asignaciones as $asignacion) {//Iterar sobre cada asignación
            $curso_id = $asignacion['curso_id'] ?? $asignacion['id_curso'];//Obtener el id del curso

            foreach ($asignacion['asignaturas'] as $asig_data) {//Iterar sobre cada asignatura
                $id_asignatura = $asig_data['id_asignatura'];//Obtener el id de la asignatura

                // Buscamos si ya existe la combinación curso-asignatura con OTRO docente
                $existe = DB::table('curso_asignatura')//Filtrar solo las asignaturas que pertenecen al docente actual
                    ->where('id_curso', $curso_id)//Filtrar solo las asignaturas del curso especificado
                    ->where('id_asignatura', $id_asignatura)//Filtrar solo las asignaturas que coincidan con la asignatura especificada
                    ->where('id_docente', '!=', $id_docente)//Filtrar solo las asignaturas que no coincidan con el docente actual
                    ->exists();//Verificar si la asignatura existe
                //Si la asignatura existe, se agrega al array de conflictos
                if ($existe) {//Si existe
                    $conflictos[] = [//Agregar el conflicto
                        'id_curso' => $curso_id,
                        'id_asignatura' => $id_asignatura
                    ];
                }
            }
        }
        //Si hay conflictos, devolver un mensaje de error indicando el conflicto
        if (!empty($conflictos)) {
            return response()->json([
                'status' => false,
                'conflictos' => $conflictos,
                'mensaje' => 'Conflicto de asignación detectado.'
            ], 409);
        }
        // --- FIN DE VALIDACIÓN ---


        // --- 2. PROCESO NORMAL DE GUARDADO ---
        DB::beginTransaction();//Iniciar una transacción de base de datos
        try {
            // Obtenemos SOLO los IDs de los cursos que pertenecen al periodo activo
            $cursosActivosIds = DB::table('cursos')//Seleccionar todas las columnas de la tabla 'cursos'
                ->where('id_periodo', $idPeriodoActivo)//Filtrar solo los cursos del periodo activo
                ->pluck('id_curso');//Obtener los IDs de los cursos

            // Borramos SOLO las asignaciones de este docente que correspondan al periodo activo
            if ($cursosActivosIds->isNotEmpty()) {//Si hay cursos activos
                DB::table('curso_asignatura')//Seleccionar todas las columnas de la tabla 'curso_asignatura'
                    ->where('id_docente', $id_docente)//Filtrar solo las asignaturas del docente especificado
                    ->whereIn('id_curso', $cursosActivosIds)//Filtrar solo las asignaturas que pertenecen a los cursos activos
                    ->delete();//Borrar las asignaturas
            }

            $nuevasAsignaciones = [];//Inicializar el array de asignaciones nuevas

            foreach ($request->asignaciones as $asignacion) {//Iterar sobre cada asignación
                $curso_id = $asignacion['curso_id'] ?? $asignacion['id_curso'];//Obtener el id del curso

                foreach ($asignacion['asignaturas'] as $asig_data) {//Iterar sobre cada asignatura
                    $nuevasAsignaciones[] = [//Agregar la asignatura nueva
                        'id_curso'        => $curso_id,
                        'id_asignatura'   => $asig_data['id_asignatura'],//Asignar el id de la asignatura
                        'id_docente'      => $id_docente,
                        'horas_semanales' => $asig_data['horas_semanales'],//Asignar las horas semanales
                        'estado'          => 1,
                        'created_at'      => now(),//Asignar la fecha de creación actual
                        'updated_at'      => now()//Asignar la fecha de actualización actual
                    ];
                }
            }
            //Si hay asignaciones nuevas, insertarlas en la base de datos
            if (!empty($nuevasAsignaciones)) {//Si hay asignaciones nuevas
                DB::table('curso_asignatura')->insert($nuevasAsignaciones);//Insertar las asignaciones nuevas en la base de datos
            }

            DB::commit();//Confirmar la transacción de base de datos
            //Devolver un mensaje de éxito indicando que las asignaciones se actualizaron exitosamente
            return response()->json([
                'status' => true,
                'mensaje' => 'Asignaturas actualizadas exitosamente.'
            ], 200);
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
        } catch (\Throwable $e) {
            DB::rollBack();//Cancelar la transacción de base de datos
            return response()->json([
                'status' => false,
                'mensaje' => 'Error SQL: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Función para obtener las asignaturas actuales del docente, recibiendo como parámetro el id del docente.
     * La función getPorDocente es la encargada de obtener las asignaturas actuales del docente, recibiendo como parámetro el id del docente. 
     * La función busca las asignaturas actuales del docente con el id proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. Si no encuentra las asignaturas actuales del docente, devuelve un mensaje de error indicando que no se encontró las asignaturas actuales del docente.
     */
    public function getPorDocente(string $id_docente)
    {
        try {
            // 1. Obtener el periodo lectivo activo
            $periodoActivo = Periodos_lectivos::where('estado_activo', 1)->first();//Obtener el periodo lectivo activo

            // Si no hay un periodo activo, cortamos la ejecución de forma segura
            if (!$periodoActivo) {
                return response()->json([
                    'status' => false,
                    'error' => 'No hay un periodo lectivo activo configurado.'
                ], 404);
            }

            // 2. Consultar asignaciones cruzando con el periodo activo
            $asignacionesDB = DB::table('curso_asignatura')->select(
                'curso_asignatura.id_curso',
                'curso_asignatura.id_asignatura',
                'curso_asignatura.horas_semanales',
                'cursos.paralelo',
                'niveles_academicos.nombre as nombre_nivel',
                'especialidades.nombre as nombre_especialidad',
                'asignaturas.nombre as nombre_asignatura'
            )//Seleccionar todas las columnas de la tabla 'curso_asignatura'
                ->join('cursos', 'cursos.id_curso', '=', 'curso_asignatura.id_curso')//Unir la tabla 'cursos' con la tabla 'curso_asignatura' en base a la columna 'id_curso'
                ->join('niveles_academicos', 'niveles_academicos.id_nivel', '=', 'cursos.id_nivel')//Unir la tabla 'niveles_academicos' con la tabla 'cursos' en base a la columna 'id_nivel'
                ->join('especialidades', 'especialidades.id_especialidad', '=', 'cursos.id_especialidad')//Unir la tabla 'especialidades' con la tabla 'cursos' en base a la columna 'id_especialidad'
                ->join('asignaturas', 'asignaturas.id_asignatura', '=', 'curso_asignatura.id_asignatura')//Unir la tabla 'asignaturas' con la tabla 'curso_asignatura' en base a la columna 'id_asignatura'
                ->where('curso_asignatura.id_docente', $id_docente)//Filtrar solo las asignaturas del docente especificado
                ->where('cursos.id_periodo', $periodoActivo->id_periodo) // Filtrar solo las asignaturas del periodo activo
                ->get();//Obtener los datos de la consulta

            // 3. Transformamos los datos para la estructura que espera al Frontend
            $resultado = [];//Inicializar el array de resultado
            foreach ($asignacionesDB->groupBy('id_curso') as $curso_id => $materias) {//Iterar sobre cada curso
                $primera = $materias->first();//Obtener la primera asignatura
                $nombre_curso = $primera->nombre_nivel . ' ' . $primera->nombre_especialidad . ' "' . $primera->paralelo . '"';//Obtener el nombre del curso

                $asignaturasArray = $materias->map(function ($m) {//Iterar sobre cada asignatura
                    return [//Devolver los datos de la asignatura
                        'id_asignatura' => $m->id_asignatura,
                        'nombre' => $m->nombre_asignatura,
                        'horas_semanales' => $m->horas_semanales
                    ];
                })->values();//Devolver los valores de la colección

                $resultado[] = [//Agregar el curso a la lista de resultado
                    'curso_id' => $curso_id,
                    'curso_nombre' => $nombre_curso,
                    'asignaturas' => $asignaturasArray
                ];
            }
            //Devolver los datos en formato JSON
            return response()->json(['status' => true, 'data' => $resultado]);
        } catch (\Exception $e) {
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
            return response()->json(['status' => false, 'error' => 'Error al obtener datos: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Función para mostrar un curso de asignatura específico, recibiendo como parámetro el id del registro.
     * La función show es la encargada de mostrar un curso de asignatura específico, recibiendo como parámetro el id del registro. 
     * La función busca el curso de asignatura con el id proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. Si no encuentra el curso de asignatura, devuelve un mensaje de error indicando que no se encontró el curso de asignatura.
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
     * Función para eliminar un curso de asignatura específico, recibiendo como parámetro el id del registro.
     * La función destroy es la encargada de eliminar un curso de asignatura específico, recibiendo como parámetro el id del registro. 
     * La función busca el curso de asignatura con el id proporcionado y, si lo encuentra, inhabilita el nivel académico y guarda los cambios en la base de datos. 
     * Luego, devuelve los datos actualizados en formato JSON, incluyendo un mensaje de éxito. 
     * Si no encuentra el curso de asignatura, devuelve un mensaje de error indicando que no se encontró el curso de asignatura.
     */
    public function destroy(string $id)
    {
        // Obtener el objeto Curso_Asignaturas con el id proporcionado
        $res = Curso_Asignaturas::find($id);
        // Si el objeto existe, inhabilitar el Curso_Asignaturas y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {
            $res->estado = 0;//Inhabilitar el Curso_Asignaturas
            $res->save();//Guardar los cambios en la base de datos
            $data = $res->toArray();//Convertir el objeto a un array para devolverlo en formato JSON
            if ($data) {//Si el objeto existe
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
    /**
     * Función para habilitar un curso de asignatura específico, recibiendo como parámetro el id del registro.
     * La función habilitar es la encargada de habilitar un curso de asignatura específico, recibiendo como parámetro el id del registro. 
     * La función busca el curso de asignatura con el id proporcionado y, si lo encuentra, habilita el nivel académico y guarda los cambios en la base de datos. 
     * Luego, devuelve los datos actualizados en formato JSON, incluyendo un mensaje de éxito. 
     * Si no encuentra el curso de asignatura, devuelve un mensaje de error indicando que no se encontró el curso de asignatura.
     */
    public function habilitar(string $id)
    {
        // Obtener el objeto Curso_Asignaturas con el id proporcionado
        $res = Curso_Asignaturas::find($id);
        // Si el objeto existe, habilitar el Curso Asignatura y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {
            $res->estado = 1;//Habilitar el Curso_Asignaturas
            $res->save();//Guardar los cambios en la base de datos
            $data = $res->toArray();//Convertir el objeto a un array para devolverlo en formato JSON
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
    /**
     * Función para desasignar un docente específico, recibiendo como parámetro el id del registro.
     * La función desasignarDocente es la encargada de desasignar un docente específico, recibiendo como parámetro el id del registro. 
     * La función busca el curso de asignatura con el id proporcionado y, si lo encuentra, establece el docente como null (desasignar) y guarda los cambios en la base de datos. 
     * Luego, devuelve los datos actualizados en formato JSON, incluyendo un mensaje de éxito. 
     * Si no encuentra el curso de asignatura, devuelve un mensaje de error indicando que no se encontró el curso de asignatura.
     */
    public function desasignarDocente(string $id)
    {
        // Buscar el curso por su ID
        $curso = Curso_Asignaturas::find($id);//Obtener el curso de asignatura con el id proporcionado

        if (isset($curso)) {
            // Establecer el tutor como null (desasignar)
            $curso->id_docente = null;//Establecer el docente como null (desasignar)
            $curso->save();//Guardar los cambios en la base de datos
            $data = $curso->toArray();//Convertir el objeto a un array para devolverlo en formato JSON
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
            //Si no encuentra el curso de asignatura, devolver un mensaje de error indicando que no se encontró el curso de asignatura
            return response()->json([
                'error' => true,
                'mensaje' => "El Curso_Asignaturas con id: $id no existe.",
            ], 404);
        }
    }
}
