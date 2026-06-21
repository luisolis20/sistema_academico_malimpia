<?php

namespace App\Http\Controllers;
//Importación de clases necesarias para el controlador HorariosController
use App\Models\Horarios_clases;//Importación de la clase Horarios_clases
use Illuminate\Http\Request;//Importación de la clase Request para manejar las solicitudes HTTP
use Illuminate\Support\Facades\DB;//Importación de la clase DB para acceder a las tablas de la base de datos

//Clase HorariosController que representa un controlador en la aplicación para manejar las operaciones relacionadas con los horarios de clases
class HorariosController extends Controller
{
    /**
     * Función que muestra una lista de horarios de clases, las cuales pueden ser filtradas por página y por búsqueda.
     * La función index es la encargada de mostrar una lista de horarios de clases, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     */
    public function index(Request $request)
    {
        try {
            $perPage = min($request->input('per_page', 10), 20);//Obtener el número de elementos por página, limitando el número de elementos por página a 20
            $searchQuery = $request->input('search_query');//Obtener la consulta de búsqueda

            // 1. CONSULTA BASE: Ordenamos por nivel y especialidad
            $query = DB::table('cursos')//Seleccionar todas las columnas de la tabla 'cursos'
                ->join('niveles_academicos', 'cursos.id_nivel', '=', 'niveles_academicos.id_nivel')//Unir la tabla 'niveles_academicos' con la tabla 'cursos' en base a la columna 'id_nivel'
                ->join('especialidades', 'cursos.id_especialidad', '=', 'especialidades.id_especialidad')//Unir la tabla 'especialidades' con la tabla 'cursos' en base a la columna 'id_especialidad'
                ->join('periodos_lectivos', 'cursos.id_periodo', '=', 'periodos_lectivos.id_periodo')//Unir la tabla 'periodos_lectivos' con la tabla 'cursos' en base a la columna 'id_periodo'
                ->whereExists(function ($q) {//Integrar la relación de cursos con el periodo activo
                    $q->select(DB::raw(1))//Seleccionar solo la columna '1'
                        ->from('curso_asignatura')//Seleccionar todas las columnas de la tabla 'curso_asignatura'
                        ->whereColumn('curso_asignatura.id_curso', 'cursos.id_curso');//Filtrar solo las asignaturas del curso
                })
                ->select(
                    'cursos.id_curso',
                    'cursos.paralelo',
                    'niveles_academicos.id_nivel',
                    'niveles_academicos.nombre as nivel',
                    'especialidades.id_especialidad as EspecialidadID',
                    'especialidades.nombre as nombre_especialidad',
                    'periodos_lectivos.id_periodo as PeriodoID',
                    'periodos_lectivos.nombre as nombre_periodo'
                )//Seleccionar todas las columnas de la tabla 'cursos', de la tabla 'niveles_academicos', de la tabla 'especialidades' y de la tabla 'periodos_lectivos'
                // ORDENAMIENTO REQUERIDO
                ->orderBy('niveles_academicos.id_nivel', 'asc')//Ordenar los datos de la consulta de búsqueda numéricamente dentro de cada grupo
                ->orderBy('especialidades.nombre', 'asc')//Ordenar los datos de la consulta de búsqueda numéricamente dentro de cada grupo
                ->orderBy('cursos.paralelo', 'asc')//Ordenar los datos de la consulta de búsqueda numéricamente dentro de cada grupo
                ->where('periodos_lectivos.estado_activo', 1); // Solo mostrar cursos del periodo activo
            if (!empty($searchQuery)) {//Si hay una consulta de búsqueda, aplicarla a los campos relevantes
                $query->where(function ($q) use ($searchQuery) {//Aplicar la consulta de búsqueda a cada campo relevante
                    $q->where('niveles_academicos.nombre', 'LIKE', "%{$searchQuery}%")//Filtrar solo los niveles académicos que coincidan con la consulta de búsqueda
                        ->orWhere('cursos.paralelo', 'LIKE', "%{$searchQuery}%");//Filtrar solo los cursos que coincidan con la consulta de búsqueda
                });
            }

            $data = $query->paginate($perPage);//Obtener los datos paginados
            //Si no hay datos, devolver un mensaje de error indicando que no se encontraron datos
            if ($data->isEmpty()) {
                return response()->json(['data' => [], 'message' => 'No se encontraron datos'], 200);
            }

            $cursoIds = collect($data->items())->pluck('id_curso')->toArray();//Obtener los IDs de los cursos

            // 2. Traer detalles incluyendo al DOCENTE
            $detalles = DB::table('curso_asignatura')//Seleccionar todas las columnas de la tabla 'curso_asignatura'
                ->join('asignaturas', 'curso_asignatura.id_asignatura', '=', 'asignaturas.id_asignatura')//Unir la tabla 'asignaturas' con la tabla 'curso_asignatura' en base a la columna 'id_asignatura'
                ->leftJoin('personas as docentes', 'curso_asignatura.id_docente', '=', 'docentes.id_persona')//Unir la tabla 'personas' con la tabla 'curso_asignatura' en base a la columna 'id_docente'
                ->leftJoin('horarios_clases', 'horarios_clases.id_curso_asignatura', '=', 'curso_asignatura.id_curso_asignatura')// Unir la tabla 'horarios_clases' con la tabla 'curso_asignatura' en base a la columna 'id_curso_asignatura'
                ->whereIn('curso_asignatura.id_curso', $cursoIds)//Filtrar solo los cursos que pertenecen al periodo activo
                ->select(
                    'curso_asignatura.id_curso',
                    'curso_asignatura.id_curso_asignatura',
                    'curso_asignatura.horas_semanales',
                    'curso_asignatura.id_docente',
                    DB::raw("CONCAT(docentes.nombres, ' ', docentes.apellidos) as nombre_docente"),
                    'asignaturas.nombre as asignatura',
                    'horarios_clases.id_horario',
                    'horarios_clases.dia_semana',
                    'horarios_clases.hora_inicio',
                    'horarios_clases.hora_fin'
                )//Seleccionar todas las columnas de la tabla 'curso_asignatura', de la tabla 'asignaturas', de la tabla 'horarios_clases' y de la tabla 'personas'
                ->get();//Obtener los datos de la consulta

            // 3. Agrupación (Igual que antes)
            $dataItems = collect($data->items())->map(function ($curso) use ($detalles) {//Iterar sobre cada elemento de la colección
                $cursoArray = (array) $curso;//Convertir el elemento a un array
                foreach ($cursoArray as $key => $value) {//Iterar sobre cada clave-valor del array
                    if (is_string($value)) {//Si el valor es una cadena de caracteres
                        $cursoArray[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');//Convertir la cadena de caracteres a UTF-8
                    }
                }

                $detallesCurso = $detalles->where('id_curso', $curso->id_curso);//Obtener los detalles del curso
                $asignaturasAgrupadas = [];//Inicializar el array de asignaturas agrupadas
                $tieneHorario = false; // Bandera para saber si el curso ya tiene horarios

                foreach ($detallesCurso as $detalle) {//Iterar sobre cada asignatura
                    $idCa = $detalle->id_curso_asignatura;//Obtener el id de la asignatura

                    if (!isset($asignaturasAgrupadas[$idCa])) {//Si no existe una asignatura con ese id, agregarla
                        $asignaturasAgrupadas[$idCa] = [//Agregar la asignatura al array de asignaturas agrupadas
                            'id_curso_asignatura' => $idCa,
                            'asignatura'          => mb_convert_encoding($detalle->asignatura, 'UTF-8', 'UTF-8'),
                            'horas_semanales'     => $detalle->horas_semanales,
                            'id_docente'          => $detalle->id_docente,
                            'nombre_docente'      => mb_convert_encoding($detalle->nombre_docente ?? 'Sin asignar', 'UTF-8', 'UTF-8'),
                            'horarios'            => []
                        ];
                    }
                    //Si existe una asignatura con ese id, agregar los datos del asistente
                    if (!empty($detalle->id_horario)) {//Si existe un horario
                        $tieneHorario = true;//Indicar que el curso tiene horarios
                        $asignaturasAgrupadas[$idCa]['horarios'][] = [
                            'id_horario'  => $detalle->id_horario,//Agregar el id del horario
                            'dia_semana'  => mb_convert_encoding($detalle->dia_semana, 'UTF-8', 'UTF-8'),//Agregar la dia de la semana
                            'hora_inicio' => $detalle->hora_inicio,
                            'hora_fin'    => $detalle->hora_fin
                        ];
                    }
                }

                $cursoArray['asignaturas_asignadas'] = array_values($asignaturasAgrupadas);//Agregar los asignaturas agrupadas al array de cursos   
                $cursoArray['tiene_horario'] = $tieneHorario; // Frontend usará esto para mostrar "Crear" o "Editar"

                return $cursoArray;//Devolver los datos de la asignatura junto con los detalles del curso
            })->toArray();//Convertir el array de cursos a un array de objetos
            //Devolver los datos en formato JSON
            return response()->json([
                'data' => $dataItems,
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'per_page'     => $data->perPage(),
                    'total'        => $data->total(),
                    'last_page'    => $data->lastPage(),
                ],
            ], 200);
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Función para guardar los horarios de clases, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     * La función guardarHorario es la encargada de guardar los horarios de clases, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario. 
     * Antes de guardar, se realiza una validación para asegurar que no existan conflictos entre asignaturas. Si se intenta guardar un asignatura que ya existe, se devuelve un mensaje de error indicando el conflicto. Si la validación pasa sin problemas, se procede a guardar los horarios de clases y se devuelve un mensaje de éxito junto con los datos del nuevo registro.
     */
    public function guardarHorario(Request $request)
    {
        // Esperamos un array 'horarios' y el 'id_curso'
        $horarios = $request->input('horarios');//Obtener los datos enviados por el formulario
        $id_curso = $request->input('id_curso');//Obtener el id del curso

        // --- 0. OBTENER EL PERIODO ACTIVO ---
        $periodoActivo = DB::table('periodos_lectivos')->where('estado_activo', 1)->first();//Obtener el periodo lectivo activo
        //Si no existe un periodo activo, devolver un mensaje de error indicando que no se encontró un periodo activo
        if (!$periodoActivo) {
            return response()->json([
                'error' => 'No se encontró un periodo lectivo activo en el sistema para validar los horarios.'
            ], 400);
        }

        $idPeriodoActivo = $periodoActivo->id_periodo;//Obtener el id del periodo activo

        DB::beginTransaction();//Iniciar una transacción de base de datos
        try {
            // 1. Validar cruce de docentes
            foreach ($horarios as $item) {//Iterar sobre cada horario
                // Obtenemos el docente de esta asignatura
                $ca = DB::table('curso_asignatura')->where('id_curso_asignatura', $item['id_curso_asignatura'])->first();//Obtener la asignatura del curso actual

                if ($ca && $ca->id_docente) {
                    // Verificar si el docente ya está dando clases en ese día y rango de horas en OTRO curso DEL PERIODO ACTIVO
                    $cruce = DB::table('horarios_clases')//Seleccionar todas las columnas de la tabla 'horarios_clases'
                        ->join('curso_asignatura', 'horarios_clases.id_curso_asignatura', '=', 'curso_asignatura.id_curso_asignatura')
                        ->join('cursos', 'curso_asignatura.id_curso', '=', 'cursos.id_curso') // <-- NUEVO JOIN
                        ->where('cursos.id_periodo', $idPeriodoActivo) // <-- FILTRO CLAVE: Solo el periodo actual
                        ->where('curso_asignatura.id_docente', $ca->id_docente)
                        ->where('curso_asignatura.id_curso', '!=', $id_curso) // Excluir el curso actual
                        ->where('horarios_clases.dia_semana', $item['dia_semana'])//Filtrar por el día y la semana
                        ->where(function ($query) use ($item) {
                            // Lógica de solapamiento de horas: (Inicio1 < Fin2) AND (Fin1 > Inicio2)
                            $query->where('horarios_clases.hora_inicio', '<', $item['hora_fin'])
                                ->where('horarios_clases.hora_fin', '>', $item['hora_inicio']);//Filtrar por el rango de horas
                        })
                        ->exists();//Verificar si la asignatura existe

                    if ($cruce) {//Si existe, devolver un mensaje de error indicando que la asignatura ya está asignada en el mismo horario
                        return response()->json([
                            'error' => "El docente ya tiene asignada otra asignatura a la misma hora en otro curso de este periodo (Día: {$item['dia_semana']}, Hora: {$item['hora_inicio']} - {$item['hora_fin']})."
                        ], 422);
                    }
                }
            }

            // 2. Limpiar los horarios anteriores de ESTE curso (para reemplazarlos por los nuevos)
            $idsCursoAsignatura = DB::table('curso_asignatura')->where('id_curso', $id_curso)->pluck('id_curso_asignatura');

            if ($idsCursoAsignatura->isNotEmpty()) {//Si existe una asignatura, limpiar los horarios anteriores de este curso
                DB::table('horarios_clases')->whereIn('id_curso_asignatura', $idsCursoAsignatura)->delete();
            }

            // 3. Insertar los nuevos horarios
            $insertData = [];//Inicializar el array de datos insertados
            foreach ($horarios as $item) {//Iterar sobre cada horario
                $insertData[] = [//Agregar los datos del horario a insertar
                    'id_curso_asignatura' => $item['id_curso_asignatura'],
                    'dia_semana'          => $item['dia_semana'],
                    'hora_inicio'         => $item['hora_inicio'],
                    'hora_fin'            => $item['hora_fin'],
                ];
            }
            //Si hay datos insertados, insertarlos en la base de datos
            if (count($insertData) > 0) {
                DB::table('horarios_clases')->insert($insertData);//Insertar los datos en la base de datos
            }

            DB::commit();//Confirmar la transacción de base de datos
            return response()->json(['message' => 'Horario guardado correctamente'], 200);//Devolver un mensaje de éxito indicando que el horario se guardó correctamente
        } catch (\Exception $e) {
            
            DB::rollBack();//Cancelar la transacción de base de datos
            return response()->json(['error' => 'Error al guardar el horario: ' . $e->getMessage()], 500);//Devolver un mensaje de error indicando que ocurrió un error al guardar el horario
        }
    }


    /**
     * Función para insertar nuevos horarios en la base de datos, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     * La función store es la encargada de insertar nuevos horarios en la base de datos, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario. 
     * Antes de insertar los nuevos horarios, se realiza una validación para asegurar que no existan conflictos entre asignaturas. Si se intenta insertar un asignatura que ya existe, se devuelve un mensaje de error indicando el conflicto. Si la validación pasa sin problemas, se procede a insertar los nuevos horarios y se devuelve un mensaje de éxito junto con los datos del nuevo registro.
     */
    public function store(Request $request)
    {
        //Obtener los datos enviados por el formulario
        $inputs = $request->input();
        //Crear el objeto Horarios_clases con los datos enviados
        $res = Horarios_clases::create($inputs);
        //Devolver los datos creados en formato JSON, incluyendo un mensaje de éxito
        return response()->json([
            'data' => $res,
            'mensaje' => "Agregado con Éxito!!",
        ]);
    }
    /**
     * Función para mostrar un horario de clase específico, recibiendo como parámetro el id del registro.
     * La función show es la encargada de mostrar un horario de clase específico, recibiendo como parámetro el id del registro. 
     * La función busca el horario de clase con el id proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. Si no encuentra el horario de clase, devuelve un mensaje de error indicando que no se encontró el horario de clase.           
     */
    public function show(string $id)
    {
        //Obtener el objeto Horarios_clases con el id proporcionado
        $res = Horarios_clases::find($id);
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
                'mensaje' => "La Asignatura con id: $id no Existe",
            ]);
        }
    }
    /**
     * Función para obtener los horarios de clase de un docente específico, recibiendo como parámetro el id del docente.
     * La función getHorarioDocente es la encargada de obtener los horarios de clase de un docente específico, recibiendo como parámetro el id del docente. 
     * La función busca los horarios de clase de un docente con el id proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. Si no encuentra los horarios de clase del docente, devuelve un mensaje de error indicando que no se encontró los horarios de clase del docente.
     */
    
    public function getHorarioDocente(int $id_persona)
    {
        // Buscamos los horarios filtrando a través de la relación curso_asignatura
        $horarios = Horarios_clases::with([
            'curso_asignatura.asignatura',
            'curso_asignatura.curso.nivel',
            'curso_asignatura.curso.especialidad'
        ])//Incluir todas las relaciones necesarias para mostrar los datos de los horarios de clases(curso_asignatura, asignatura, nivel, especialidad)
            ->whereHas('curso_asignatura', function ($query) use ($id_persona) {//Integrar la relación de curso_asignatura con el docente
                $query->where('id_docente', $id_persona)//Filtrar solo los cursos de asignaturas del docente especificado
                    ->where('estado', 1);//Filtrar solo los cursos activos
            })
            ->orderBy('hora_inicio', 'asc')//Ordenar los horarios de clase por hora de inicio
            ->get();//Obtener los datos de la consulta

        // Estructuramos la respuesta
        $data = $horarios->map(function ($h) {//Iterar sobre cada horario
            return [//Devolver los datos de la asignatura junto con los detalles del curso
                'id' => $h->id_horario,
                'dia' => $h->dia_semana, // Ejemplo: 'Lunes', 'Martes'...
                'inicio' => date('H:i', strtotime($h->hora_inicio)),//Obtener la hora de inicio del horario
                'fin' => date('H:i', strtotime($h->hora_fin)),//Obtener la hora de fin del horario
                'asignatura' => $h->curso_asignatura->asignatura->nombre,
                'curso' => $h->curso_asignatura->curso->nivel->nombre . ' "' . $h->curso_asignatura->curso->paralelo . '"',
                'especialidad' => $h->curso_asignatura->curso->especialidad->nombre
            ];
        });
        //Devolver los datos en formato JSON
        return response()->json($data);
    }
}
