<?php

namespace App\Http\Controllers;
//Importación de clases necesarias para el controlador Cronograma_matriculasController
use App\Models\Cronograma_matriculas;//Importación de la clase Cronograma_matriculas
use App\Models\Matriculas;//Importación de la clase Matriculas
use App\Models\Cursos;//Importación de la clase Cursos
use App\Models\Curso_Asignaturas;//Importación de la clase Curso_Asignaturas
use App\Models\Niveles_academicos;//Importación de la clase Niveles_academicos
use App\Models\Periodos_lectivos;//Importación de la clase Periodos_lectivos
use App\Models\Personas;//Importación de la clase Personas
use Illuminate\Http\Request;//Importación de la clase Request para manejar las solicitudes HTTP
use Illuminate\Support\Facades\DB;//Importación de la clase DB para acceder a las tablas de la base de datos

//Clase Cronograma_matriculasController que representa un controlador en la aplicación para manejar las operaciones relacionadas con el cronograma de matrículas
class Cronograma_matriculasController extends Controller
{
    /**
     * Función que muestra una lista de cronogramas de matrículas, las cuales pueden ser filtradas por página y por búsqueda.
     * La función index es la encargada de mostrar una lista de cronogramas de matrículas, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     */
    public function index(Request $request)
    {
        try {
            $perPage = min($request->input('per_page', 10), 1000);//Obtener el número de elementos por página, limitando el número de elementos por página a 1000
            $searchQuery = $request->input('search_query');//Obtener la consulta de búsqueda

            $query = DB::table('cursos')//Seleccionar todas las columnas de la tabla 'cursos'
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
                )//Seleccionar todas las columnas de la tabla 'cursos', de la tabla 'niveles_academicos', de la tabla 'especialidades', de la tabla 'periodos_lectivos' y de la tabla 'cronograma_matriculas'
                ->join('niveles_academicos', 'cursos.id_nivel', '=', 'niveles_academicos.id_nivel')//Unir la tabla 'niveles_academicos' con la tabla 'cursos' en base a la columna 'id_nivel'
                ->join('especialidades', 'cursos.id_especialidad', '=', 'especialidades.id_especialidad')//Unir la tabla 'especialidades' con la tabla 'cursos' en base a la columna 'id_especialidad'
                ->join('periodos_lectivos', 'cursos.id_periodo', '=', 'periodos_lectivos.id_periodo')//Unir la tabla 'periodos_lectivos' con la tabla 'cursos' en base a la columna 'id_periodo'
                ->leftJoin('cronograma_matriculas', function ($join) {//Unir la tabla 'cronograma_matriculas' con la tabla 'cursos' en base a la columna 'id_periodo'
                    $join->on('cursos.id_nivel', '=', 'cronograma_matriculas.id_nivel')//Unir la tabla 'cronograma_matriculas' con la tabla 'cursos' en base a la columna 'id_nivel'
                        ->on('cursos.id_especialidad', '=', 'cronograma_matriculas.id_especialidad')//Unir la tabla 'cronograma_matriculas' con la tabla 'cursos' en base a la columna 'id_especialidad'
                        ->on('cursos.id_periodo', '=', 'cronograma_matriculas.id_periodo');//Unir la tabla 'cronograma_matriculas' con la tabla 'cursos' en base a la columna 'id_periodo'
                })
                ->where('periodos_lectivos.estado_activo', 1)//Filtrar solo los periodos lectivos activos
                ->where('cursos.estado', 1);//Filtrar solo los cursos activos

            if (!empty($searchQuery)) {//Si hay una consulta de búsqueda, aplicarla a los campos relevantes
                $query->where(function ($q) use ($searchQuery) {//Aplicar la consulta de búsqueda a cada campo relevante
                    $q->where('niveles_academicos.nombre', 'LIKE', "%{$searchQuery}%")//Filtrar solo los niveles académicos que coincidan con la consulta de búsqueda
                        ->orWhere('periodos_lectivos.nombre', 'LIKE', "%{$searchQuery}%")//Filtrar solo los periodos lectivos que coincidan con la consulta de búsqueda
                        ->orWhere('especialidades.nombre', 'LIKE', "%{$searchQuery}%");//Filtrar solo las especialidades que coincidan con la consulta de búsqueda
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
                ->orderByRaw("CAST(niveles_academicos.nombre AS UNSIGNED) ASC");//Ordenar los datos de la consulta de búsqueda numéricamente dentro de cada grupo

            $data = $query->paginate($perPage);//Obtener los datos paginados
            //Si no hay datos, devolver un mensaje de error indicando que no se encontraron datos
            if ($data->isEmpty()) {
                return response()->json(['data' => [], 'message' => 'No se encontraron datos'], 200);
            }

            $transformedItems = collect($data->items())->map(function ($item) {//Iterar sobre cada elemento de la colección
                $attributes = (array) $item;//Convertir el elemento a un array
                foreach ($attributes as $key => $value) {//Iterar sobre cada clave-valor del array
                    if (is_string($value)) {//Si el valor es una cadena de caracteres
                        $attributes[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');//Convertir el valor a UTF-8
                    }
                }
                return $attributes;//Devolver los atributos del elemento transformados
            });
            //Devolver los datos paginados en formato JSON, incluyendo la información de paginación
            return response()->json([//Devolver los datos en formato JSON
                'data' => $transformedItems,
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'per_page'     => $data->perPage(),
                    'total'        => $data->total(),
                    'last_page'    => $data->lastPage(),
                ],
            ], 200);
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener los datos: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Función para insertar nuevas cronogramas de matrículas en la base de datos, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     * La función store es la encargada de insertar nuevas cronogramas de matrículas en la base de datos, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario. 
     * Antes de guardar, se realiza una validación para asegurar que no existan conflictos entre niveles académicos, especialidades y periodos lectivos. Si se intenta guardar un nivel académico que ya existe, se devuelve un mensaje de error indicando el conflicto. Si se intenta guardar una especialidad que ya existe, se devuelve un mensaje de error indicando el conflicto. Si se intenta guardar un periodo lectivo que ya existe, se devuelve un mensaje de error indicando el conflicto. Si se intenta guardar una cronograma de matrículas que ya existe, se devuelve un mensaje de error indicando el conflicto. Si la validación pasa sin problemas, se procede a crear el nuevo cronograma de matrículas y se devuelve un mensaje de éxito junto con los datos del nuevo registro.
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
        ]);//Validar los datos enviados por el formulario

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

            return response()->json([//Devolver los datos en formato JSON
                'success' => true,
                'message' => 'Cronogramas creados correctamente para ' . count($request->niveles) . ' nivel(es).'
            ], 200);
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
        } catch (\Exception $e) {
            // Si algo falla, revertimos todos los cambios
            DB::rollBack();
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
            return response()->json([
                'success' => false,
                'error'   => 'Error al procesar los cronogramas: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Función para mostrar un cronograma de matrículas específico, recibiendo como parámetro el id del registro.
     * La función show es la encargada de mostrar un cronograma de matrículas específico, recibiendo como parámetro el id del registro. 
     * La función busca el cronograma de matrículas con el id proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. Si no encuentra el cronograma de matrículas, devuelve un mensaje de error indicando que el registro no existe.
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
     * Función para actualizar los datos de un cronograma de matrículas específico, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario y el id del registro.
     * La función update es la encargada de actualizar los datos de un cronograma de matrículas específico, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario y el id del registro. 
     * La función busca el cronograma de matrículas con el id proporcionado y, si lo encuentra, actualiza los datos enviados por el formulario y guarda los cambios en la base de datos. Si no encuentra el cronograma de matrículas, devuelve un mensaje de error indicando que el registro no existe.
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
     * Función para obtener el cronograma de matrículas activo para un estudiante específico, recibiendo como parámetros el id del estudiante.
     * La función getCronogramaActivo es la encargada de obtener el cronograma de matrículas activo para un estudiante específico, recibiendo como parámetros el id del estudiante. 
     * La función busca el cronograma de matrículas activo para el estudiante con el id proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. Si no encuentra el cronograma de matrículas activo, devuelve un mensaje de error indicando que no se encontró ningún cronograma de matrículas activo.
     */
    public function getCronogramaActivo(Request $request, string $id_estudiante)
    {
        $hoy = now();//Obtener la fecha actual

        // 1. Obtener el periodo activo
        $periodoActivo = Periodos_lectivos::where('estado_activo', 1)->first();//Obtener el periodo lectivo activo
        //Si no hay periodo activo, devolver un mensaje de error indicando que no se encontró ningún cronograma de matrículas activo
        if (!$periodoActivo) {
            return response()->json(['tiene_historial' => true, 'cronogramas' => []]);
        }

        // 2. Consulta base de cronogramas vigentes por fecha
        $querySchedules = Cronograma_matriculas::with(['periodo', 'nivel', 'especialidad'])//Incluir todas las relaciones necesarias para mostrar los datos de las cronogramas de matrículas(periodo, nivel, especialidad)
            ->where('id_periodo', $periodoActivo->id_periodo)//Filtrar solo las cronogramas del periodo activo
            ->where('fecha_inicio', '<=', $hoy)//Filtrar solo las cronogramas que comiencen antes o en la fecha actual
            ->where('fecha_fin', '>=', $hoy);//Filtrar solo las cronogramas que terminen después o en la fecha actual

        // CONTROL EXPLICITO DESDE FRONTEND: Si el alumno va a Inicial por primera vez
        if ($request->query('tipo') === '0') {//Si el tipo de consulta es '0', filtrar solo cursos de nivel Inicial
            $querySchedules->whereHas('nivel', function ($q) {//Filtrar solo cursos de nivel Inicial
                $q->where('nombre', 'LIKE', '0'); // Filtra únicamente cursos de nivel Inicial
            });
            //Devolver un mensaje de error indicando que no se encontró ningún cronograma de matrículas activo
            return response()->json([
                'tiene_historial' => false,
                'cronogramas' => $querySchedules->get()
            ]);
        }

        // 3. Buscar la última matrícula histórica interna
        $ultimaMatricula = Matriculas::with(['curso.nivel', 'calificaciones'])//Incluir todas las relaciones necesarias para mostrar los datos de las matrículas(curso, nivel, calificaciones)
            ->where('id_estudiante', $id_estudiante)//Filtrar solo las matrículas del estudiante especificado
            ->whereHas('curso', function ($q) use ($periodoActivo) {//Integridad referencial: El periodo activo debe ser el periodo del curso
                $q->where('id_periodo', '!=', $periodoActivo->id_periodo);//Filtrar solo los cursos del periodo activo
            })
            ->orderBy('fecha_matricula', 'desc')//Ordenar por fecha de matrícula descendente
            ->first();//Obtener la última matrícula histórica interna

        $nivelAnterior = null;//Inicializar el nivel anterior como null
        $reprobo = false;//Inicializar el valor de reprobo como false

        if ($ultimaMatricula) {//Si existe una matrícula histórica interna
            $nivelAnterior = $ultimaMatricula->curso->nivel;//Obtener el nivel anterior
            $reprobo = $ultimaMatricula->calificaciones->contains(function ($calificacion) {//Verificar si la calificación de la matrícula histórica interna es reprobada o pierde
                $estado = strtolower($calificacion->estado_asignatura);//Obtener el estado de la calificación
                return $estado === 'reprobado' || $estado === 'pierde' || $calificacion->nota_final_definitiva < 7;//Verificar si la calificación es reprobada, pierde o es inferior a 7 puntos
            });
        } else {
            // 4. Si no tiene historial interno, validamos si ya tiene registrado un historial externo
            $historialExterno = DB::table('historial_externo')//Seleccionar todas las columnas de la tabla 'historial_externo'
                ->where('id_estudiante', $id_estudiante)//Filtrar solo los registros del estudiante especificado
                ->first();//Obtener el primer registro de la consulta

            if ($historialExterno) {//Si existe un registro externo
                $nivelAnterior = Niveles_academicos::find($historialExterno->ultimo_nivel_aprobado);//Obtener el nivel anterior externo
                $reprobo = false; // Al ser un registro externo aprobado, se asume promoción directa
            }
        }

        // 5. Si encontramos un punto de partida previo (Interno o Externo), calculamos el siguiente nivel
        if ($nivelAnterior) {//Si existe un nivel anterior
            $jerarquiaAnterior = $nivelAnterior->orden_jerarquia;//Obtener la jerarquía del nivel anterior
            $jerarquiasDB = Niveles_academicos::pluck('orden_jerarquia')->unique()->toArray();//Obtener todas las jerarquías de niveles académicos

            usort($jerarquiasDB, function ($a, $b) {//Ordenar las jerarquías de niveles académicos de manera descendente
                $getPeso = function ($str) {//Función para obtener el peso de una jerarquía de niveles académicos
                    $strLower = strtolower($str);//Convertir la cadena a minúsculas
                    if (strpos($strLower, 'graduado') !== false) return 999;//Si la jerarquía es 'Graduado', devolver un peso de 999
                    preg_match('/\d+/', $str, $matches);//Extraer el número de la jerarquía
                    $num = isset($matches[0]) ? (int)$matches[0] : 0;//Obtener el número de la jerarquía
                    if (strpos($strLower, 'bachillerato') !== false) return $num + 10;//Si la jerarquía es 'Bachillerato', devolver un peso de 10 más alto
                    return $num;//Devolver el número de la jerarquía
                };
                return $getPeso($a) <=> $getPeso($b);//Ordenar las jerarquías de niveles académicos de manera descendente
            });

            $ordenProgreso = array_values($jerarquiasDB);//Convertir el array de jerarquías de niveles académicos en un array de valores
            $indiceActual = array_search($jerarquiaAnterior, $ordenProgreso);//Buscar el índice de la jerarquía actual en el array de jerarquías de niveles académicos

            if ($indiceActual !== false) {//Si se encuentra el índice de la jerarquía actual
                $indiceEsperado = $reprobo ? $indiceActual : ($indiceActual + 1);//Si es reprobado, el índice esperado es el actual. Si no es reprobado, el índice esperado es el siguiente
                $jerarquiaEsperada = $ordenProgreso[$indiceEsperado] ?? 'Graduado';//Obtener la jerarquía esperada según el índice esperado

                $querySchedules->whereHas('nivel', function ($q) use ($jerarquiaEsperada) {//Filtrar solo los cursos de la jerarquía esperada
                    $q->where('orden_jerarquia', $jerarquiaEsperada);//Filtrar solo los cursos de la jerarquía esperada
                });
            }
            //Devolver un mensaje de error indicando que no se encontró ningún cronograma de matrículas activo
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
    /**
     * Función para registrar el historial externo desde el modal/formulario, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     * La función storeHistorialExterno es la encargada de registrar el historial externo desde el modal/formulario, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario. 
     * Antes de guardar, se realiza una validación para asegurar que no existan conflictos entre niveles académicos, especialidades y periodos lectivos. Si se intenta guardar un nivel académico que ya existe, se devuelve un mensaje de error indicando el conflicto. Si se intenta guardar una especialidad que ya existe, se devuelve un mensaje de error indicando el conflicto. Si se intenta guardar un periodo lectivo que ya existe, se devuelve un mensaje de error indicando el conflicto. Si la validación pasa sin problemas, se procede a crear el nuevo registro de historial externo y se devuelve un mensaje de éxito junto con los datos del nuevo registro.
     */
    public function storeHistorialExterno(Request $request)
    {
        try {
            $request->validate([
                'id_estudiante'          => 'required|integer',
                'institucion_origen'     => 'required|string|max:200',
                'ultimo_nivel_aprobado'  => 'required|integer',
                'promedio_final'         => 'required|numeric',
                'archivo_notas'          => 'nullable|file|mimes:pdf,jpg,png|max:2048'
            ]);//Validar los datos enviados por el formulario

            $urlArchivo = null;//Inicializar el valor de urlArchivo como null
            if ($request->hasFile('archivo_notas')) {//Si se ha enviado un archivo
                $path = $request->file('archivo_notas')->store('historiales_externos', 'public');//Almacenar el archivo en la carpeta 'historiales_externos' en la carpeta 'public'
                $urlArchivo = asset('storage/' . $path);//Obtener la URL del archivo almacenado
            }   
            //Insertar el registro de historial externo en la base de datos
            DB::table('historial_externo')->insert([
                'id_estudiante'          => $request->id_estudiante,
                'institucion_origen'     => $request->institucion_origen,
                'ultimo_nivel_aprobado'  => $request->ultimo_nivel_aprobado,
                'promedio_final'         => $request->promedio_final,
                'archivo_notas_url'      => $urlArchivo,
            ]);//Insertar el registro de historial externo en la base de datos
            //Devolver un mensaje de éxito indicando que el historial externo se registró correctamente
            return response()->json(['status' => true, 'mensaje' => 'Historial académico externo registrado correctamente.']);
        } catch (\Exception $e) {
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
            return response()->json(['status' => false, 'error' => $e->getMessage()], 500);
        }
    }
    /**
     * Función para obtener los niveles académicos externos, no recibiendo parámetros.
     * La función listadoNivelesExteriores es la encargada de obtener los niveles académicos externos, no recibiendo parámetros. 
     * La función obtiene los niveles académicos externos de la base de datos y los devuelve en formato JSON, incluyendo un mensaje de éxito. 
     * Si ocurre algún error, devuelve un mensaje de error en formato JSON.
     */
    public function listadoNivelesExteriores()
    {   
        $niveles = DB::table('niveles_academicos')->select('id_nivel', 'nombre')->get();//Obtener los niveles académicos externos de la base de datos
        return response()->json($niveles);//Devolver los datos en formato JSON
    }
    /**
     * Función para obtener los cursos por cronograma específico, recibiendo como parámetros el id del cronograma y el objeto Request que contiene los datos enviados por el formulario.
     * La función getCursosPorCronograma es la encargada de obtener los cursos por cronograma específico, recibiendo como parámetros el id del cronograma y el objeto Request que contiene los datos enviados por el formulario.
     */
    public function getCursosPorCronograma(Request $request, string $id_cronograma)
    {
        try {
            // 1. Obtener el periodo lectivo activo
            $periodoActivo = Periodos_lectivos::where('estado_activo', 1)->first();//Obtener el periodo lectivo activo
            //Si no hay periodo activo, devolver un mensaje de error indicando que no se encontró ningún cronograma de matrículas activo
            if (!$periodoActivo) {
                return response()->json([
                    'status' => false,
                    'error' => 'No hay un periodo lectivo activo configurado.'
                ], 404);
            }

            // 2. Buscar el cronograma solicitado
            $cronograma = Cronograma_matriculas::find($id_cronograma);//Obtener el cronograma solicitado

            // CONTROL CLAVE: Validar que exista y que pertenezca ÚNICAMENTE al periodo activo
            if (!$cronograma || $cronograma->id_periodo != $periodoActivo->id_periodo) {
                return response()->json([
                    'status' => false,
                    'error' => 'El cronograma no existe o no corresponde al periodo lectivo activo.'
                ], 404);
            }

            // 3. Buscar cursos que coincidan con los parámetros del cronograma en el periodo activo
            $cursos = Cursos::where('id_nivel', $cronograma->id_nivel)//Filtrar solo los cursos del nivel correspondiente
                ->where('id_especialidad', $cronograma->id_especialidad)//Filtrar solo los cursos de la especialidad correspondiente
                ->where('id_periodo', $periodoActivo->id_periodo) // Forzamos el ID del periodo activo verificado
                ->where('estado', 1)//Filtrar solo los cursos activos
                ->get();//Obtener los cursos que coincidan con los parámetros del cronograma en el periodo activo

            return response()->json($cursos);//Devolver los cursos en formato JSON
        } catch (\Exception $e) {
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
            return response()->json([
                'status' => false,
                'error' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Función para crear una matrícula específica, recibiendo como parámetros el id del curso y el objeto Request que contiene los datos enviados por el formulario.
     * La función crearmatricula es la encargada de crear una matrícula específica, recibiendo como parámetros el id del curso y el objeto Request que contiene los datos enviados por el formulario. 
     * Antes de crear la matrícula, se realiza una validación para asegurar que no existan conflictos entre niveles académicos, especialidades y periodos lectivos. Si se intenta crear un nivel académico que ya existe, se devuelve un mensaje de error indicando el conflicto. Si se intenta crear una especialidad que ya existe, se devuelve un mensaje de error indicando el conflicto. Si se intenta crear un periodo lectivo que ya existe, se devuelve un mensaje de error indicando el conflicto. Si la validación pasa sin problemas, se procede a crear la matrícula y se devuelve un mensaje de éxito junto con los datos de la matrícula.
     */
    public function crearmatricula(Request $request)
    {
        $cursoDestino = Cursos::with('nivel')->find($request->id_curso);//Obtener el curso destino
        //Si no existe, devolver un mensaje de error indicando que el curso no existe
        if (!$cursoDestino) {
            return response()->json(['error' => true, 'mensaje' => 'El curso seleccionado no existe.'], 404);
        }

        $nivelDestino = $cursoDestino->nivel;//Obtener el nivel destino

        // Verificar si ya está matriculado en ese periodo
        $existe = Matriculas::where('id_estudiante', $request->id_estudiante)//Filtrar solo las matrículas del estudiante especificado
            ->whereHas('curso', function ($q) use ($cursoDestino) {//Integridad referencial: El curso debe ser el curso destino
                $q->where('id_periodo', $cursoDestino->id_periodo);//Filtrar solo las matrículas del curso destino
            })->exists();//Verificar si la matrícula existe
        //Si ya existe, devolver un mensaje de error indicando que el estudiante ya está matriculado en ese periodo lectivo
        if ($existe) {
            return response()->json(['error' => true, 'mensaje' => 'El estudiante ya está matriculado en este periodo lectivo.'], 422);
        }
        //Si no existe, crear la matrícula utilizando un bucle foreach para iterar sobre el array de cursos y crear una nueva matrícula en la base de datos para cada uno.
        $ultimaMatricula = Matriculas::with(['curso.nivel', 'calificaciones'])//Incluir todas las relaciones necesarias para mostrar los datos de las matrículas(curso, nivel, calificaciones)
            ->where('id_estudiante', $request->id_estudiante)//Filtrar solo las matrículas del estudiante especificado
            ->whereHas('curso', function ($q) use ($cursoDestino) {//Integridad referencial: El curso debe ser el curso destino
                $q->where('id_periodo', '!=', $cursoDestino->id_periodo);//Filtrar solo las matrículas del curso destino
            })
            ->orderBy('fecha_matricula', 'desc')//Ordenar por fecha de matrícula descendente
            ->first();//Obtener la última matrícula histórica interna
        //Si existe, obtener el nivel anterior y la jerarquía anterior
        if ($ultimaMatricula) {
            $nivelAnterior = $ultimaMatricula->curso->nivel;//Obtener el nivel anterior
            $jerarquiaAnterior = $nivelAnterior->orden_jerarquia;//Obtener la jerarquía anterior
            $jerarquiaDestino = $nivelDestino->orden_jerarquia;//Obtener la jerarquía destino
            //Verificar si se reproba
            $reprobo = $ultimaMatricula->calificaciones->contains(function ($calificacion) {//Verificar si la calificación de la matrícula histórica interna es reprobada o pierde
                $estado = strtolower($calificacion->estado_asignatura);//Obtener el estado de la calificación
                return $estado === 'reprobado' || $estado === 'pierde' || $calificacion->nota_final_definitiva < 7;//Verificar si la calificación es reprobada, pierde o es inferior a 7 puntos
            });

            
            $jerarquiasDB = Niveles_academicos::pluck('orden_jerarquia')->unique()->toArray();//Obtener todas las jerarquías de niveles académicos de la base de datos
            //Ordenar las jerarquías de niveles académicos de manera descendente
            usort($jerarquiasDB, function ($a, $b) {//Ordenar las jerarquías de niveles académicos de manera descendente
                $getPeso = function ($str) {//Función para obtener el peso de una jerarquía de niveles académicos
                    $strLower = strtolower($str);//Convertir la cadena a minúsculas
                    if (strpos($strLower, 'graduado') !== false) return 999;//Si la jerarquía es 'Graduado', devolver un peso de 999

                    preg_match('/\d+/', $str, $matches);//Extraer el número de la jerarquía
                    $num = isset($matches[0]) ? (int)$matches[0] : 0;//Obtener el número de la jerarquía

                    if (strpos($strLower, 'bachillerato') !== false) return $num + 10;//Si la jerarquía es 'Bachillerato', devolver un peso de 10 más alto

                    return $num;//Devolver el número de la jerarquía
                };
                return $getPeso($a) <=> $getPeso($b);//Ordenar las jerarquías de niveles académicos de manera descendente
            });

            $ordenProgreso = array_values($jerarquiasDB);//Convertir el array de jerarquías de niveles académicos en un array de valores
            // =========================================================

            $indiceAnterior = array_search($jerarquiaAnterior, $ordenProgreso);//Buscar el índice de la jerarquía anterior en el array de jerarquías de niveles académicos
            $indiceDestino = array_search($jerarquiaDestino, $ordenProgreso);//Buscar el índice de la jerarquía destino en el array de jerarquías de niveles académicos

            if ($indiceAnterior !== false && $indiceDestino !== false) {//Si se encuentra el índice de la jerarquía anterior y el índice de la jerarquía destino
                if ($reprobo) {//Si se encuentra la jerarquía reprobada
                    if ($indiceDestino !== $indiceAnterior) {//Si el índice de la jerarquía destino no coincide con el índice de la jerarquía anterior
                        return response()->json([//Devolver un mensaje de error indicando que el estudiante reprobó el periodo anterior y debe matricularse nuevamente en la jerarquía anterior
                            'error' => true,
                            'mensaje' => "El estudiante reprobó el periodo anterior. Debe matricularse nuevamente en: {$nivelAnterior->nombre}."
                        ], 422);
                    }
                    //Si no se encuentra la jerarquía reprobada, verificar si el índice de la jerarquía destino coincide con el índice de la jerarquía siguiente
                } else {
                    if ($indiceDestino !== ($indiceAnterior + 1)) {//Si el índice de la jerarquía destino no coincide con el índice de la jerarquía siguiente
                        $jerarquiaEsperada = $ordenProgreso[$indiceAnterior + 1] ?? 'Graduado';//Obtener la jerarquía esperada según el índice esperado
                        $nivelEsperado = \App\Models\Niveles_academicos::where('orden_jerarquia', $jerarquiaEsperada)->first();//Obtener el nivel esperado según la jerarquía esperada
                        $nombreEsperado = $nivelEsperado ? $nivelEsperado->nombre : 'el siguiente nivel correspondiente';//Obtener el nombre del nivel esperado

                        return response()->json([//Devolver un mensaje de error indicando que el estudiante aprobó el periodo anterior y debe matricularse en el nivel esperado
                            'error' => true,
                            'mensaje' => "El estudiante aprobó el periodo anterior. Le corresponde matricularse en: {$nombreEsperado}."
                        ], 422);
                    }
                }
            }
        }
        //Si no se encuentra el índice de la jerarquía anterior y el índice de la jerarquía destino, crear la matrícula utilizando un bucle foreach para iterar sobre el array de cursos y crear una nueva matrícula en la base de datos para cada uno.
        $matricula = new Matriculas();//Crear una nueva matrícula en la base de datos
        $matricula->id_estudiante = $request->id_estudiante;//Asignar el id de la matrícula
        $matricula->id_curso = $request->id_curso;//Asignar el id del curso
        $matricula->id_representante = $request->id_representante;//Asignar el id del representante
        $matricula->fecha_matricula = now();//Asignar la fecha de matriculación actual
        $matricula->es_nuevo = $request->es_nuevo ?? (is_null($ultimaMatricula) ? 1 : 0);//Asignar el valor de es_nuevo del formulario
        $matricula->estado = "Activa";//Asignar el estado de la matrícula como Activa
        $matricula->save();//Guardar la matrícula en la base de datos
        //Devolver los datos creados en formato JSON, incluyendo un mensaje de éxito
        return response()->json(['mensaje' => 'Matrícula generada con éxito', 'data' => $matricula], 200);
    }
    /**
     * Función para obtener el historial de matrículas específico, recibiendo como parámetro el id del representante y el objeto Request que contiene los datos enviados por el formulario.
     * La función getHistorial es la encargada de obtener el historial de matrículas específico, recibiendo como parámetro el id del representante y el objeto Request que contiene los datos enviados por el formulario. 
     * La función busca el historial de matrículas específico con el id proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. Si no encuentra el historial de matrículas, devuelve un mensaje de error indicando que no se encontró ningún historial de matrículas.
     */
    public function getHistorial(int $id_representante)
    {
        // 1. Buscamos cuál es el periodo lectivo que está activo actualmente
        $periodoActivo = Periodos_lectivos::where('estado_activo', 1)->first();

        // Si por alguna razón no hay periodo activo, devolvemos un arreglo vacío
        if (!$periodoActivo) {
            return response()->json([]);
        }

        // 2. Filtramos el historial del representante estrictamente para el periodo activo
        $historial = Matriculas::where('id_representante', $id_representante)//Filtrar solo las matrículas del representante especificado
            ->whereHas('curso', function ($query) use ($periodoActivo) {//Integridad referencial: El periodo activo debe ser el periodo del curso
                // Este es el filtro clave: solo cursos de este año/periodo
                $query->where('id_periodo', $periodoActivo->id_periodo);
            })
            ->with([
                'estudiante:id_persona,nombres,apellidos,cedula',
                'curso.nivel:id_nivel,nombre',
                'curso.especialidad:id_especialidad,nombre'
            ])//Incluir todas las relaciones necesarias para mostrar los datos de las matrículas(estudiante, curso, nivel, especialidad)
            ->orderBy('fecha_matricula', 'desc')//Ordenar por fecha de matrícula descendente
            ->get();//Obtener los datos de la consulta

        // 3. Formateamos la data para el frontend
        $data = $historial->map(function ($m) {//Iterar sobre cada matrícula
            return [
                'id_matricula'      => $m->id_matricula,
                'id_estudiante'     => $m->id_estudiante,
                'estudiante_nombre' => $m->estudiante->nombres . ' ' . $m->estudiante->apellidos,
                'estudiante_cedula' => $m->estudiante->cedula,
                'nivel_nombre'      => $m->curso->nivel->nombre,
                'especialidad'      => $m->curso->especialidad->nombre,
                'paralelo'          => $m->curso->paralelo,
                'fecha'             => date('d/m/Y H:i', strtotime($m->fecha_matricula)),
            ];//Devolver los datos de la matrícula junto con las calificaciones
        });
        //Devolver los datos en formato JSON
        return response()->json($data);
    }
    /**
     * Función para obtener los cursos matriculados específico, recibiendo como parámetro el id del representante y el objeto Request que contiene los datos enviados por el formulario.
     * La función getCursosMatriculados es la encargada de obtener los cursos matriculados específico, recibiendo como parámetro el id del representante y el objeto Request que contiene los datos enviados por el formulario. 
     * La función busca los cursos matriculados específico con el id proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. Si no encuentra los cursos matriculados, devuelve un mensaje de error indicando que no se encontró ningún curso matriculado.
     */
    public function getCursosMatriculados(string $id_representante)
    {
        // 1. Buscamos el periodo lectivo activo
        $periodoActivo = Periodos_lectivos::where('estado_activo', 1)->first();

        // Si no hay periodo activo, retornamos un arreglo vacío
        if (!$periodoActivo) {
            return response()->json([]);
        }

        // 2. Obtenemos las matrículas filtrando estrictamente por los cursos del periodo activo
        $matriculas = Matriculas::where('id_representante', $id_representante)//Filtrar solo las matrículas del representante especificado
            ->whereHas('curso', function ($query) use ($periodoActivo) {//Integridad referencial: El periodo activo debe ser el periodo del curso
                // Filtro clave: Solo traer matrículas asociadas a cursos de este periodo
                $query->where('id_periodo', $periodoActivo->id_periodo);//Filtrar solo las matrículas del periodo activo
            })
            ->with([
                'estudiante',
                'curso.nivel',
                'curso.especialidad',
                'curso.docentetutor',
                'curso.curso_asignaturas.asignatura',
                'curso.curso_asignaturas.docente',
                'curso.curso_asignaturas.horarios_clases'
            ])//Incluir todas las relaciones necesarias para mostrar los datos de las matrículas(estudiante, curso, nivel, especialidad, docentetutor, asignatura, horarios_clases)
            ->get();//Obtener los datos de la consulta

        // 3. Transformamos la colección para limpiar binarios 
        $matriculasLimpias = $matriculas->map(function ($matricula) {//Iterar sobre cada matrícula
            // Limpiar foto del Estudiante
            if ($matricula->estudiante) {//Si existe un estudiante
                $matricula->estudiante->foto = $matricula->estudiante->foto ? base64_encode($matricula->estudiante->foto) : null;//Convertir la foto de la tabla 'personas' a base64
            }

            // Limpiar foto del Docente Tutor
            if ($matricula->curso && $matricula->curso->docentetutor) {//Si existe un docente tutor
                $matricula->curso->docentetutor->foto = $matricula->curso->docentetutor->foto ? base64_encode($matricula->curso->docentetutor->foto) : null;//Convertir la foto de la tabla 'personas' a base64
            }

            // Limpiar fotos de los Docentes de cada Asignatura
            if ($matricula->curso && $matricula->curso->curso_asignaturas) {//Si existe una tabla 'curso_asignaturas'
                $matricula->curso->curso_asignaturas->each(function ($item) {//Iterar sobre cada asignatura
                    if ($item->docente) {//Si existe un docente
                        $item->docente->foto = $item->docente->foto ? base64_encode($item->docente->foto) : null;//Convertir la foto de la tabla 'personas' a base64
                    }
                });
            }

            return $matricula;//Devolver los datos de la matrícula limpiados
        });
        //Devolver los datos limpiados en formato JSON
        return response()->json($matriculasLimpias);
    }
    /**
     * Función para obtener los estudiantes por asignatura específico, recibiendo como parámetro el id del docente y el objeto Request que contiene los datos enviados por el formulario.
     * La función getEstudiantesPorAsignatura es la encargada de obtener los estudiantes por asignatura específico, recibiendo como parámetro el id del docente y el objeto Request que contiene los datos enviados por el formulario. 
     * La función busca los estudiantes por asignatura específico con el id proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. Si no encuentra los estudiantes por asignatura, devuelve un mensaje de error indicando que no se encontró ningún estudiante por asignatura.
     */
    public function getEstudiantesPorAsignatura(string $id_docente)
    {
        $hoy = date('Y-m-d');//Obtener la fecha actual

        // 1. Buscamos el periodo lectivo activo
        $periodoActivo = Periodos_lectivos::where('estado_activo', 1)->first();
        //Si no hay periodo activo, devolver un mensaje de error indicando que no se encontró ningún periodo lectivo activo
        if (!$periodoActivo) {
            return response()->json([]);
        }

        // 2. Obtener las asignaturas del docente filtradas estrictamente por el periodo activo
        $asignaturas = Curso_Asignaturas::where('id_docente', $id_docente)//Filtrar solo las asignaturas del docente especificado
            ->where('estado', 1)//Filtrar solo las asignaturas activas
            ->whereHas('curso', function ($query) use ($periodoActivo) {//Integridad referencial: El periodo activo debe ser el periodo del curso
                $query->where('id_periodo', $periodoActivo->id_periodo);//Filtrar solo las asignaturas del periodo activo
            })
            ->with([
                'asignatura',
                'curso.nivel',
                'curso.especialidad',
                'curso.matriculas.estudiante',
                'curso.matriculas.asistencias' => function ($query) use ($hoy) {
                    $query->whereDate('fecha', $hoy);
                }
            ])//Incluir todas las relaciones necesarias para mostrar los datos de las asignaturas(asignatura, nivel, especialidad, estudiante, asistencias)
            ->get();//Obtener los datos de la consulta

        // 3. Transformación de datos
        $data = $asignaturas->map(function ($item) {//Iterar sobre cada asignatura
            $id_actual = $item->id_curso_asignatura;//Obtener el id de la asignatura

            return [//Devolver los datos de la asignatura
                'id_curso_asignatura' => $id_actual,
                'nombre_asignatura' => $item->asignatura->nombre,
                'curso_info' => $item->curso->nivel->nombre . ' "' . $item->curso->paralelo . '"',
                'especialidad' => $item->curso->especialidad->nombre,
                'total_estudiantes' => $item->curso->matriculas->count(),//Obtener el número de estudiantes del curso
                'estudiantes' => $item->curso->matriculas->map(function ($m) use ($id_actual) {//Iterar sobre cada estudiante

                    // Como la DB ya filtró por fecha, esta colección solo tiene 0 o 1 registro.
                    // El consumo de memoria de esta búsqueda es casi nulo.
                    $asistenciaHoy = $m->asistencias->where('id_curso_asignatura', $id_actual)->first();

                    return [
                        'id_persona' => $m->estudiante->id_persona,
                        'cedula' => $m->estudiante->cedula,
                        'nombres' => $m->estudiante->nombres,
                        'apellidos' => $m->estudiante->apellidos,
                        // Nota: Convertir fotos BLOB a base64 también consume memoria.
                        // Si el error persiste, considera no enviar fotos muy grandes.
                        'foto' => $m->estudiante->foto ? base64_encode($m->estudiante->foto) : null,
                        'id_matricula' => $m->id_matricula,
                        'asistencia_guardada' => $asistenciaHoy ? $asistenciaHoy->estado : null//Obtener el estado de la asistencia
                    ];//Devolver los datos de la estudiante junto con la asistencia
                })->sortBy('apellidos')->values()->all()//Ordenar los estudiantes por apellidos de la matrícula
            ];
        });
        //Devolver los datos en formato JSON
        return response()->json($data);
    }
    /**
     * Función para buscar el historial de matrículas específico, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     * La función buscarHistorialMatriculas es la encargada de buscar el historial de matrículas específico, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario. 
     * La función busca el historial de matrículas específico con el cédula proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. Si no encuentra el historial de matrículas, devuelve un mensaje de error indicando que no se encontró ningún historial de matrículas.
     */
    public function buscarHistorialMatriculas(Request $request)
    {
        $cedula = $request->cedula;//Obtener el cédula del formulario

        // Buscamos a la persona (estudiante) por su cédula
        $estudiante = Personas::where('cedula', $cedula)//Filtrar solo la persona (estudiante) con la cédula proporcionada
            ->with([
                'matriculasestudiantes' => function ($query) {//Unir la tabla 'matriculas' con la tabla 'cursos' en base a la columna 'id_curso'
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
            ])->first();//Obtener la persona (estudiante) con la cédula proporcionada

        if (!$estudiante) {//Si no se encuentra la persona (estudiante), devolver un mensaje de error indicando que no se encontró ninguna persona (estudiante) con esa cédula
            return response()->json(['success' => false, 'message' => 'No se encontró un estudiante con esa cédula.'], 404);
        }

        if ($estudiante->matriculasestudiantes->isEmpty()) {
            //Si no se encuentra ninguna matrícula, devolver un mensaje de error indicando que no se encontró ninguna matrícula
            return response()->json(['success' => false, 'message' => 'El estudiante no posee historial de matrículas.'], 404);
        }

        // 1. Convertir la foto del estudiante a Base64
        if ($estudiante->foto) {
            $estudiante->foto = base64_encode($estudiante->foto);//Convertir la foto de la tabla 'personas' a base64
        }

        //
        // Como también son de la tabla 'Personas', si tienen datos BLOB romperán el JSON igual que el estudiante.
        foreach ($estudiante->matriculasestudiantes as $matricula) {//Iterar sobre cada matrícula
            if ($matricula->curso && $matricula->curso->docentetutor) {//Si existe un docente tutor
                $matricula->curso->docentetutor->makeHidden('foto');//Ocultar la foto de la tabla 'personas'
            }
            if ($matricula->curso && $matricula->curso->curso_asignaturas) {//Si existe una tabla 'curso_asignaturas'
                foreach ($matricula->curso->curso_asignaturas as $curso_asignatura) {//Iterar sobre cada asignatura
                    if ($curso_asignatura->docente) {//Si existe un docente
                        $curso_asignatura->docente->makeHidden('foto');//Ocultar la foto de la tabla 'personas'
                    }
                }
            }
        }
        //Devolver los datos en formato JSON
        return response()->json([
            'success' => true,
            'estudiante' => $estudiante
        ]);
    }
    /**
     * Función para anular una matrícula específica, recibiendo como parámetro el id de la matrícula.
     * La función anularMatricula es la encargada de anular una matrícula específica, recibiendo como parámetro el id de la matrícula. 
     * La función busca la matrícula especificada con el id proporcionado y, si lo encuentra, anula la matrícula. Si no encuentra la matrícula, devuelve un mensaje de error indicando que no se encontró la matrícula especificada.
     */
    public function anularMatricula(string $id)
    {
        // 1. Buscar la matrícula por su ID primario
        $matricula = Matriculas::find($id);//Obtener la matrícula con el id proporcionado
        //Si no encuentra la matrícula, devolver un mensaje de error indicando que no se encontró la matrícula especificada
        if (!$matricula) {
            return response()->json([//Devolver un mensaje de error indicando que no se encontró la matrícula especificada
                'success' => false,
                'message' => 'No se encontró la matrícula especificada.'
            ], 404);
        }

        // 2. Validar si ya se encuentra anulada
        if ($matricula->estado === 'Anulada') {//Si ya se encuentra anulada
            return response()->json([//Devolver un mensaje de error indicando que la matrícula ya ha sido anulada previamente
                'success' => false,
                'message' => 'Esta matrícula ya ha sido anulada previamente.'
            ], 400);
        }

        // 3. Cambiar el estado de la matrícula
        $matricula->estado = 'Anulada';//Cambiar el estado de la matrícula a Anulada
        $matricula->save();//Guardar los cambios en la base de datos
        //Devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        return response()->json([
            'success' => true,
            'message' => 'La matrícula ha sido anulada con éxito.'
        ]);
    }
}
