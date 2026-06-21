<?php

namespace App\Http\Controllers;
//Importación de clases necesarias para el controlador CursosController
use App\Models\Cursos;//Importación de la clase Cursos
use App\Models\Personas;//Importación de la clase Personas
use App\Models\Familia;//Importación de la clase Familia
use App\Models\Curso_Asignaturas;//Importación de la clase Curso_Asignaturas
use App\Models\Periodos_lectivos;//Importación de la clase Periodos_lectivos
use Illuminate\Http\Request;//Importación de la clase Request para manejar las solicitudes HTTP
use Illuminate\Support\Facades\DB;//Importación de la clase DB para acceder a las tablas de la base de datos
use Carbon\Carbon;//Importación de la clase Carbon para manejar fechas y horas

//Clase CursosController que representa un controlador en la aplicación para manejar las operaciones relacionadas con los cursos
class CursosController extends Controller
{
    /**
     * Función que muestra una lista de cursos, las cuales pueden ser filtradas por página y por búsqueda.
     * La función index es la encargada de mostrar una lista de cursos, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
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
            $periodoActivo = DB::table('periodos_lectivos')->where('estado_activo', 1)->first();//Obtener el periodo lectivo activo
            $idPeriodoActivo = $periodoActivo ? $periodoActivo->id_periodo : null;//Obtener el id del periodo lectivo activo
            $nombrePeriodoActivo = $periodoActivo ? $periodoActivo->nombre : 'Desconocido';//Obtener el nombre del periodo lectivo activo

            // 2. Crear una subconsulta para obtener SOLO el último curso de cada docente
            // Esto evita que el docente salga duplicado si tiene cursos en periodos anteriores
            $ultimosCursos = DB::table('cursos')//Seleccionar todas las columnas de la tabla 'cursos'
                ->select('id_docente_tutor', DB::raw('MAX(id_curso) as ultimo_curso_id'))//Seleccionar solo la columna 'id_docente_tutor' y la columna 'ultimo_curso_id'
                ->groupBy('id_docente_tutor');//Agrupar por 'id_docente_tutor'

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
            )//Seleccionar todas las columnas de la tabla 'personas', de la tabla 'usuarios', de la tabla 'roles', de la tabla 'cursos', de la tabla 'periodos_lectivos', de la tabla 'niveles_academicos' y de la tabla 'especialidades'
                ->join('usuarios', 'usuarios.id_persona', '=', 'personas.id_persona')//Unir la tabla 'usuarios' con la tabla 'personas' en base a la columna 'id_persona'
                ->join('roles', 'roles.id_rol', '=', 'usuarios.id_rol')//Unir la tabla 'roles' con la tabla 'usuarios' en base a la columna 'id_rol'
                // Unimos con la subconsulta para aislar solo el último curso asignado al docente
                ->leftJoinSub($ultimosCursos, 'ultimos_cursos', function ($join) {//Unir la tabla 'cursos' con la tabla 'ultimos_cursos' en base a la columna 'id_docente_tutor'
                    $join->on('personas.id_persona', '=', 'ultimos_cursos.id_docente_tutor');//Unir la tabla 'personas' con la tabla 'ultimos_cursos' en base a la columna 'id_persona'
                })
                // Hacemos el join real con la tabla cursos usando el ID obtenido en la subconsulta
                ->leftJoin('cursos', 'cursos.id_curso', '=', 'ultimos_cursos.ultimo_curso_id')//Unir la tabla 'cursos' con la tabla 'cursos' en base a la columna 'id_curso'
                ->leftJoin('periodos_lectivos', 'periodos_lectivos.id_periodo', '=', 'cursos.id_periodo')//Unir la tabla 'periodos_lectivos' con la tabla 'cursos' en base a la columna 'id_periodo'
                ->leftJoin('niveles_academicos', 'niveles_academicos.id_nivel', '=', 'cursos.id_nivel')//Unir la tabla 'niveles_academicos' con la tabla 'cursos' en base a la columna 'id_nivel'
                ->leftJoin('especialidades', 'especialidades.id_especialidad', '=', 'cursos.id_especialidad')//Unir la tabla 'especialidades' con la tabla 'cursos' en base a la columna 'id_especialidad'
                ->where('roles.nombre', 'LIKE', '%docente%');//Filtrar los resultados donde el campo 'nombre' de la tabla 'roles' comience con el texto 'docente'

            // Si hay una consulta de búsqueda, aplicarla a los campos relevantes
            if (! empty($searchQuery)) {
                $query->where(function ($q) use ($searchQuery) {//Aplicar la consulta de búsqueda a cada campo relevante
                    $q->where('personas.cedula', 'LIKE', "%{$searchQuery}%");//Filtrar los resultados donde el campo 'cedula' de la tabla 'personas' comience con el texto proporcionado en la consulta de búsqueda
                });
            }

            // Obtener los datos paginados
            $data = $query->paginate($perPage);

            // Si no hay datos, devolver un mensaje de error
            if ($data->isEmpty()) {
                return response()->json(['data' => [], 'message' => 'No se encontraron datos'], 200);
            }

            // Transformar los datos a UTF-8 y evaluar lógica de los periodos
            $data->getCollection()->transform(function ($item) use ($idPeriodoActivo, $nombrePeriodoActivo) {//Iterar sobre cada elemento de la colección
                $attributes = $item->getAttributes();//Obtener los atributos del elemento

                // Inicializar banderas para el Frontend
                $attributes['requiere_actualizacion'] = false;//Inicializar la bandera de actualización
                $attributes['mensaje_periodo'] = '';//Inicializar el mensaje de periodo
                $attributes['nuevo_periodo_id'] = $idPeriodoActivo;//Inicializar el nuevo ID del periodo

                // Si tiene un curso asignado y su periodo es diferente al periodo activo
                if (!empty($attributes['PeriodoID']) && $idPeriodoActivo && $attributes['PeriodoID'] != $idPeriodoActivo) {//Si tiene un curso asignado y su periodo es diferente al periodo activo
                    $attributes['requiere_actualizacion'] = true;//Indicar que la asignatura requiere actualización
                    $attributes['mensaje_periodo'] = "El periodo <b>{$attributes['nombre_periodo']}</b> ya no está activo. Actualmente estamos en el periodo <b>{$nombrePeriodoActivo}</b>.<br><br>¿Desea reasignar este mismo docente al curso del nuevo periodo?";//Indicar el mensaje de periodo
                }
                //Transformar los datos a UTF-8 y evaluar lógica de los periodos
                foreach ($attributes as $key => $value) {//Iterar sobre cada clave-valor del array
                    if ($key === 'foto' && ! empty($value)) {//Si el valor es una cadena de caracteres
                        $attributes[$key] = base64_encode($value);//Convertir la cadena de caracteres a base64
                    } elseif (is_string($value) && $key !== 'foto' && $key !== 'mensaje_periodo') {//Si el valor es una cadena de caracteres
                        $attributes[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');//Convertir la cadena de caracteres a UTF-8
                    }
                }

                return $attributes;//Devolver los atributos del elemento transformados
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
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al codificar los datos a JSON: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Función para obtener los cursos habilitados, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     * La función getActivados es la encargada de obtener los cursos habilitados, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     */
    public function getActivados()
    {
        try {
            $cursos = Cursos::select(
                'cursos.*',
                'niveles_academicos.id_nivel as NivelID',
                'niveles_academicos.nombre as nombre_nivel',
                'especialidades.id_especialidad as EspecialidadID',
                'especialidades.nombre as nombre_especialidad',
                'periodos_lectivos.nombre as nombre_periodo'
            )//Seleccionar todas las columnas de la tabla 'cursos', de la tabla 'niveles_academicos', de la tabla 'especialidades' y de la tabla 'periodos_lectivos'
                ->join('niveles_academicos', 'niveles_academicos.id_nivel', '=', 'cursos.id_nivel')//Unir la tabla 'niveles_academicos' con la tabla 'cursos' en base a la columna 'id_nivel'
                ->join('especialidades', 'especialidades.id_especialidad', '=', 'cursos.id_especialidad')//Unir la tabla 'especialidades' con la tabla 'cursos' en base a la columna 'id_especialidad'
                ->join('periodos_lectivos', 'periodos_lectivos.id_periodo', '=', 'cursos.id_periodo')//Unir la tabla 'periodos_lectivos' con la tabla 'cursos' en base a la columna 'id_periodo'
                ->where('cursos.estado', 1)//Filtrar solo los cursos activos
                ->where('periodos_lectivos.estado_activo', 1)//Filtrar solo los periodos lectivos activos
                ->get();//Obtener los datos de la consulta
            //Devolver los datos en formato JSON
            return response()->json([
                'status' => true,
                'data' => $cursos,
            ]);
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al procesar los datos: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Función para reasignar varios cursos a un docente específico, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     * La función reasignacionMasiva es la encargada de reasignar varios cursos a un docente específico, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     * Solo los reasigna si el docnete tenia un curso asignado en un periodo anterior al actual.
     */
    public function reasignacionMasiva(Request $request)
    {
        try {
            DB::beginTransaction();//Iniciar una transacción de base de datos

            // 1. Obtener el periodo activo
            $periodoActivo = DB::table('periodos_lectivos')->where('estado_activo', 1)->first();//Obtener el periodo activo
            //Si no hay periodo activo, devolver un mensaje de error indicando que no se encontró un periodo activo
            if (!$periodoActivo) {
                return response()->json(['mensaje' => 'No hay un periodo activo configurado.'], 404);
            }

            $idPeriodoActivo = $periodoActivo->id_periodo;//Obtener el id del periodo activo

            // 2. Subconsulta para aislar el último curso asignado a cada docente
            $ultimosCursos = DB::table('cursos')//Seleccionar todas las columnas de la tabla 'cursos'
                ->select('id_docente_tutor', DB::raw('MAX(id_curso) as ultimo_curso_id'))//Seleccionar solo la columna 'id_docente_tutor' y la columna 'ultimo_curso_id'
                ->groupBy('id_docente_tutor');//Agrupar por 'id_docente_tutor'

            // 3. Buscar a los docentes cuyo último curso NO pertenece al periodo activo
            $cursosPendientes = DB::table('cursos')//Seleccionar todas las columnas de la tabla 'cursos'
                ->joinSub($ultimosCursos, 'uc', function ($join) {//Unir la tabla 'cursos' con la tabla 'ultimos_cursos' en base a la columna 'id_docente_tutor'
                    $join->on('cursos.id_curso', '=', 'uc.ultimo_curso_id');//Unir la tabla 'cursos' con la tabla 'cursos' en base a la columna 'id_curso'
                })//Unir la tabla 'cursos' con la tabla 'cursos' en base a la columna 'id_curso'
                ->where('cursos.id_periodo', '!=', $idPeriodoActivo)//Filtrar solo los cursos que no pertenecen al periodo activo
                ->get();//Obtener los datos de la consulta
            //Si no hay cursos pendientes, devolver un mensaje de error indicando que no se encontraron cursos pendientes
            if ($cursosPendientes->isEmpty()) {
                return response()->json(['mensaje' => 'Todos los docentes ya están al día. No hay nada que reasignar.'], 404);
            }

            $nuevosRegistros = [];//Inicializar el array de nuevos registros
            $ahora = Carbon::now();//Obtener la fecha actual
            //Iterar sobre cada curso pendiente
            foreach ($cursosPendientes as $curso) {//Iterar sobre cada curso
                // Validación de seguridad: Comprobar que no se haya reasignado ya accidentalmente
                $existe = Cursos::where('id_periodo', $idPeriodoActivo)//Filtrar solo los cursos que pertenecen al periodo activo
                    ->where('id_docente_tutor', $curso->id_docente_tutor)//Filtrar solo los cursos que pertenecen al docente
                    ->exists();//Verificar si la asignatura existe
                //Si ya existe, se agrega al array de nuevos registros
                if (!$existe) {
                    $nuevosRegistros[] = [//Agregar el nuevo registro a la lista de nuevos registros
                        'id_periodo'       => $idPeriodoActivo,
                        'id_nivel'         => $curso->id_nivel,
                        'id_especialidad'  => $curso->id_especialidad,
                        'paralelo'         => $curso->paralelo,
                        'id_docente_tutor' => $curso->id_docente_tutor,
                        'estado'           => 1, // Lo dejamos activo por defecto
                        'created_at'       => $ahora,//Asignar la fecha de creación actual
                        'updated_at'       => $ahora,//Asignar la fecha de actualización actual
                    ];
                }
            }

            // Si hay registros válidos, los insertamos en bloque (Mass Insert)
            if (count($nuevosRegistros) > 0) {
                Cursos::insert($nuevosRegistros);//Insertar los registros en la base de datos
            } else {
                //Si no hay registros válidos, devolver un mensaje de error indicando que no se encontraron registros válidos
                return response()->json(['mensaje' => 'Los docentes ya contaban con cursos en este periodo.'], 404);
            }

            DB::commit();//Confirmar la transacción de base de datos
            //Devolver un mensaje de éxito indicando que la reasignación se realizó correctamente
            return response()->json([
                'mensaje' => 'Reasignación masiva completada con éxito. Se reasignaron ' . count($nuevosRegistros) . ' docentes.',
                'cantidad' => count($nuevosRegistros)//Devolver la cantidad de registros reasignados
            ], 200);
        } catch (\Exception $e) {
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
            DB::rollBack();//Cancelar la transacción de base de datos
            return response()->json(['error' => 'Error en la reasignación masiva: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Función para insertar nuevos cursos en la base de datos, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     * La función store es la encargada de insertar nuevos cursos en la base de datos, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario. 
     * Antes de insertar los nuevos cursos, se realiza una validación para asegurar que no existan conflictos entre niveles académicos, especialidades y periodos lectivos. Si se intenta insertar un nivel académico que ya existe, se devuelve un mensaje de error indicando el conflicto. Si se intenta insertar una especialidad que ya existe, se devuelve un mensaje de error indicando el conflicto. Si se intenta insertar un periodo lectivo que ya existe, se devuelve un mensaje de error indicando el conflicto. Si la validación pasa sin problemas, se procede a insertar los nuevos cursos y se devuelve un mensaje de éxito junto con los datos del nuevo registro.
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
            if (!is_null($cursoExistente->id_docente_tutor)) {//Si no es null, significa que ya hay alguien asignado
                // Si NO es null, significa que ya hay alguien asignado
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Ya existe un docente tutor asignado a este curso',
                ], 422);
            } else {
                // Si ES null, significa que lo desasignaron antes. 
                // Reutilizamos el registro actualizándole el nuevo docente.
                $cursoExistente->id_docente_tutor = $inputs['id_docente_tutor'];//Asignar el nuevo docente
                $cursoExistente->save();//Guardar los cambios en la base de datos
                //Devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
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
     * Función para mostrar un curso específico, recibiendo como parámetro el id del registro.
     * La función show es la encargada de mostrar un curso específico, recibiendo como parámetro el id del registro. 
     * La función busca el curso con el id proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. Si no encuentra el curso, devuelve un mensaje de error indicando que no se encontró el curso.
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
    /**
     * Función para obtener los cursos asignados a un docente específico, recibiendo como parámetro el id del docente.
     * La función getCursosDocente es la encargada de obtener los cursos asignados a un docente específico, recibiendo como parámetro el id del docente. 
     * La función busca los cursos asignados a el docente con el id proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. Si no encuentra los cursos asignados al docente, devuelve un mensaje de error indicando que no se encontró ningún curso asignado al docente.
     */
    public function getCursosDocente(int $id_docente_tutor)
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
            )//Seleccionar todas las columnas de la tabla 'cursos', de la tabla 'niveles_academicos', de la tabla 'especialidades' y de la tabla 'periodos_lectivos'
                ->leftJoin('periodos_lectivos', 'periodos_lectivos.id_periodo', '=', 'cursos.id_periodo')//Unir la tabla 'periodos_lectivos' con la tabla 'cursos' en base a la columna 'id_periodo'
                ->leftJoin('niveles_academicos', 'niveles_academicos.id_nivel', '=', 'cursos.id_nivel')//Unir la tabla 'niveles_academicos' con la tabla 'cursos' en base a la columna 'id_nivel'
                ->leftJoin('especialidades', 'especialidades.id_especialidad', '=', 'cursos.id_especialidad')//Unir la tabla 'especialidades' con la tabla 'cursos' en base a la columna 'id_especialidad'
                ->where('cursos.id_docente_tutor', $id_docente_tutor)//Filtrar solo los cursos que pertenecen al docente especificado
                ->get();//Obtener los datos de la consulta
            //Devolver los datos en formato JSON
            return response()->json([
                'status' => true,
                'data' => $cursos,
            ]);
        } catch (\Exception $e) {
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
            return response()->json(['error' => 'Error al codificar los datos a JSON: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Función para actualizar los datos de un curso específico, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario y el id del registro.
     * La función update es la encargada de actualizar los datos de un curso específico, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario y el id del registro. 
     * La función busca el curso con el id proporcionado y, si lo encuentra, actualiza los datos enviados por el formulario y guarda los cambios en la base de datos. Si no encuentra el curso, devuelve un mensaje de error indicando que no se encontró el curso.
     */
    public function update(Request $request, string $id)
    {
        // Obtener el objeto Cursos con el id proporcionado
        $res = Cursos::find($id);

        // Si el objeto existe, procedemos a validar y actualizar
        if (isset($res)) {

            // 1. Validar si ya existe OTRA asignación con los mismos datos
            $existeAsignacion = Cursos::where('id_periodo', $request->id_periodo)//Filtrar solo los cursos que pertenecen al periodo solicitado
                ->where('id_nivel', $request->id_nivel)//Filtrar solo los cursos que pertenecen al nivel solicitado
                ->where('id_especialidad', $request->id_especialidad)//Filtrar solo los cursos que pertenecen a la especialidad solicitada
                ->where('paralelo', $request->paralelo)//Filtrar solo los cursos que pertenecen al paralelo solicitado
                ->where('estado', 1)//Filtrar solo los cursos activos
                ->where('id_curso', '!=', $id)//Filtrar solo los cursos que no sean el curso actual
                ->whereNotNull('id_docente_tutor') // IMPORTANTE: Solo choca si el otro curso YA TIENE docente
                ->exists();

            // 2. Si ya existe otro, y estamos intentando dejar este como activo, arrojamos error
            if ($existeAsignacion && $request->estado == 1) {//Si ya existe otro curso y estamos intentando dejar este como activo, arrojamos error
                //Devolver un mensaje de error indicando que ya existe otro docente asignado y activo para este nivel, especialidad y paralelo en este periodo
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Ya existe otro docente asignado y activo para este nivel, especialidad y paralelo en este periodo.',
                ], 422);
            }

            // 3. Asignar los nuevos valores
            $res->id_periodo = $request->id_periodo;//Asignar el id del periodo
            $res->id_nivel = $request->id_nivel;//Asignar el id del nivel
            $res->id_especialidad = $request->id_especialidad;//Asignar el id de la especialidad
            $res->paralelo = $request->paralelo;//Asignar el paralelo
            $res->id_docente_tutor = $request->id_docente_tutor;//Asignar el id del docente
            $res->estado = $request->estado;//Asignar el estado de la asignatura

            // Guardar los cambios en la base de datos
            if ($res->save()) {
                //Devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito   
                return response()->json([
                    'data' => $res,
                    'mensaje' => 'Actualizado con Éxito!!',
                ], 200);
            } else {
                //Si ocurre algún error, devolver un mensaje de error en formato JSON
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
     * Función para eliminar un curso específico, recibiendo como parámetro el id del registro.
     * La función destroy es la encargada de eliminar un curso específico, recibiendo como parámetro el id del registro. 
     * La función busca el curso con el id proporcionado y, si lo encuentra, inhabilita el Curso y guarda los cambios en la base de datos. 
     * Luego, devuelve los datos actualizados en formato JSON, incluyendo un mensaje de éxito. 
     * Si no encuentra el curso, devuelve un mensaje de error indicando que no se encontró el curso.
     */
    public function destroy(string $id)
    {
        // Obtener el objeto Cursos con el id proporcionado
        $res = Cursos::find($id);
        // Si el objeto existe, inhabilitar el Curso y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {
            $res->estado = 0;//Inhabilitar el Curso
            $res->save();//Guardar los cambios en la base de datos
            $data = $res->toArray();//Convertir el objeto a un array para devolverlo en formato JSON
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
    /**
     * Función para habilitar un curso específico, recibiendo como parámetro el id del registro.
     * La función habilitar es la encargada de habilitar un curso específico, recibiendo como parámetro el id del registro. 
     * La función busca el curso con el id proporcionado y, si lo encuentra, habilita el Curso y guarda los cambios en la base de datos. 
     * Luego, devuelve los datos actualizados en formato JSON, incluyendo un mensaje de éxito. 
     * Si no encuentra el curso, devuelve un mensaje de error indicando que no se encontró el curso.
     */
    public function habilitar(string $id)
    {
        // Obtener el objeto Cursos con el id proporcionado
        $res = Cursos::find($id);
        // Si el objeto existe, habilitar el Curso y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {
            $res->estado = 1;//Habilitar el Curso
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
    /**
     * Función para desasignar un docente específico, recibiendo como parámetro el id del registro.
     * La función desasignarDocente es la encargada de desasignar un docente específico, recibiendo como parámetro el id del registro. 
     * La función busca el curso con el id proporcionado y, si lo encuentra, establece el docente como null (desasignar) y guarda los cambios en la base de datos. 
     * Luego, devuelve los datos actualizados en formato JSON, incluyendo un mensaje de éxito. 
     * Si no encuentra el curso, devuelve un mensaje de error indicando que no se encontró el curso.
     */
    public function desasignarDocente(string $id)
    {
        // Buscar el curso por su ID
        $curso = Cursos::find($id);

        if (isset($curso)) {//Si existe
            // Establecer el tutor como null (desasignar)
            $curso->id_docente_tutor = null;//Establecer el docente como null (desasignar)
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
                    'mensaje' => 'El Curso no existe (puede que ya lo haya eliminado)',
                ]);
            }
            //Si no encuentra el curso, devolver un mensaje de error indicando que no se encontró el curso
        } else {
            return response()->json([
                'error' => true,
                'mensaje' => "El Curso con id: $id no existe.",
            ], 404);
        }
    }
    /**
     * Función para obtener la carga académica de un docente específico, recibiendo como parámetro el id del docente.
     * La función getCargaAcademica es la encargada de obtener la carga académica de un docente específico, recibiendo como parámetro el id del docente. 
     * La función busca la carga académica de un docente con el id proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. Si no encuentra la carga académica del docente, devuelve un mensaje de error indicando que no se encontró la carga académica del docente.
     */
    public function getCargaAcademica(string $id_persona)
    {
        // 1. Buscamos el periodo lectivo activo
        $periodoActivo = Periodos_lectivos::where('estado_activo', 1)->first();//Obtener el periodo lectivo activo

        // Si no hay periodo activo, devolvemos las estructuras vacías para que el frontend no falle
        if (!$periodoActivo) {
            //Devolver las estructuras vacías para que el frontend no falle
            return response()->json([
                'es_tutor' => false,
                'tiene_asignaturas' => false,
                'tutorias' => [],
                'asignaturas' => []
            ]);
        }

        // 2. Obtener cursos donde es Tutor estrictamente en este periodo
        $tutorias = Cursos::with(['nivel', 'especialidad', 'periodo'])//Incluir todas las relaciones necesarias para mostrar los datos de los cursos(nivel, especialidad, periodo)
            ->where('id_docente_tutor', $id_persona)//Filtrar solo los cursos donde el docente es tutor
            ->where('estado', 1)//Filtrar solo los cursos activos
            ->where('id_periodo', $periodoActivo->id_periodo) // Filtrar solo los cursos del periodo activo
            ->get();//Obtener los datos de la consulta

        // 3. Obtener asignaturas que dicta, asegurando que el curso atado sea de este periodo
        $asignaturas = Curso_Asignaturas::with(['curso.nivel', 'curso.especialidad', 'asignatura'])//Incluir todas las relaciones necesarias para mostrar los datos de las asignaturas(curso, nivel, especialidad, asignatura)
            ->where('id_docente', $id_persona)//Filtrar solo las asignaturas del docente especificado
            ->where('estado', 1)//Filtrar solo las asignaturas activas
            ->whereHas('curso', function ($query) use ($periodoActivo) {//Integrar la relación de curso con el periodo activo
                // <-- Filtro de periodo aplicado a la relación del curso
                $query->where('id_periodo', $periodoActivo->id_periodo);//Filtrar solo los cursos del periodo activo
            })
            ->get();//Obtener los datos de la consulta
        //Devolver los datos en formato JSON
        return response()->json([
            'es_tutor' => $tutorias->isNotEmpty(),
            'tiene_asignaturas' => $asignaturas->isNotEmpty(),
            'tutorias' => $tutorias,
            'asignaturas' => $asignaturas
        ]);
    }
    /**
     * Función para verificar si un usuario es tutor de un curso específico, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     * La función verificarTutor es la encargada de verificar si un usuario es tutor de un curso específico, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario. 
     * La función busca si el usuario autenticado es tutor de un curso con el id proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. Si no encuentra el usuario, devuelve un mensaje de error indicando que no se encontró el usuario.  
     */
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
        $esTutor = Cursos::where('id_docente_tutor', $usuario->id_persona)//Filtrar solo los cursos donde el docente es tutor
            ->whereHas('periodo', function ($query) {//Integridad referencial: El periodo debe ser el periodo del curso
                $query->where('estado_activo', 1); // o true, dependiendo de tu base de datos
            })
            ->exists();//Verificar si la asignatura existe
        //Devolver los datos en formato JSON
        return response()->json([
            'es_tutor' => $esTutor
        ], 200);
    }
    /**
     * Función para verificar si un usuario tiene familia, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     * La función TieneFamilia es la encargada de verificar si un usuario tiene familia, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario. 
     * La función busca si el usuario autenticado tiene familia y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. Si no encuentra el usuario, devuelve un mensaje de error indicando que no se encontró el usuario.
     */
    public function TieneFamilia(Request $request)
    {
        // Obtenemos el usuario autenticado (ajusta según cómo uses Sanctum o JWT)
        $usuario = auth()->user();

        // Si no hay usuario o no tiene una persona asociada, devolvemos false
        if (!$usuario || !$usuario->id_persona) {
            return response()->json(['tiene_familia' => false], 200);
        }

        // Buscamos si existe al menos un familiar asociado a esta persona
        $tieneFamilia = Familia::where('id_representante', $usuario->id_persona)//Filtrar solo los familiares asociados a la persona
            ->exists();//Verificar si la asignatura existe
        //Devolver los datos en formato JSON
        return response()->json([
            'tiene_familia' => $tieneFamilia
        ], 200);
    }
}
