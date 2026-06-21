<?php

namespace App\Http\Controllers;
//Importación de clases necesarias para el controlador Control_SubidaNotasController
use App\Models\Calificaciones;//Importación de la clase Calificaciones
use App\Models\Control_subida_notas;//Importación de la clase Control_subida_notas
use App\Models\Curso_Asignaturas;//Importación de la clase Curso_Asignaturas
use App\Models\Matriculas;//Importación de la clase Matriculas
use App\Models\Niveles_academicos;//Importación de la clase Niveles_academicos
use App\Models\Periodos_lectivos;//Importación de la clase Periodos_lectivos
use Carbon\Carbon;//Importación de la clase Carbon para manejar fechas y horas
use Illuminate\Http\Request;//Importación de la clase Request para manejar las solicitudes HTTP

//Clase Control_SubidaNotasController que representa un controlador en la aplicación para manejar las operaciones relacionadas con el registro de subida de notas
class Control_SubidaNotasController extends Controller
{
    /**
     * Función que muestra una lista de controles de subida de notas, las cuales pueden ser filtradas por página y por búsqueda.
     * La función index es la encargada de mostrar una lista de controles de subida de notas, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     */
    public function index(Request $request)
    {
        try {
            //Obtener el periodo lectivo activo
            $periodoActivo = Periodos_lectivos::where('estado_activo', 1)->first();//Obtener el periodo lectivo activo
            //Si no hay periodo activo, devolver un mensaje de error
            if (! $periodoActivo) {
                return response()->json(['data' => [], 'message' => 'No hay un periodo lectivo activo'], 404);
            }

            $query = Control_subida_notas::where('id_periodo', $periodoActivo->id_periodo);//Obtener los datos de la consulta
            $data = $query->get();//Obtener los datos de la consulta

            
            $now = Carbon::now();//Obtener la fecha actual
            foreach ($data as $control) {//Iterar sobre cada control
                if ($control->habilitado == 1 && $control->fecha_fin) {//Si el control está habilitado y tiene una fecha de fin
                    $fechaFin = Carbon::parse($control->fecha_fin);//Obtener la fecha de fin del control
                    if ($now->greaterThanOrEqualTo($fechaFin)) {//Si la fecha actual es mayor o igual a la fecha de fin del control
                        $control->habilitado = 0;//Deshabilitar el control
                        $control->save();//Guardar los cambios en la base de datos
                    }
                }
            }
            //Devolver los datos en formato JSON, incluyendo un mensaje de éxito
            return response()->json([
                'data' => $data,//Enviar los datos devueltos por la consulta
                'periodo_activo' => $periodoActivo,//Enviar el periodo lectivo activo
            ], 200);
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Función para insertar nuevas controles de subida de notas en la base de datos, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     * La función store es la encargada de insertar nuevas controles de subida de notas en la base de datos, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario. 
     * Antes de guardar, se realiza una validación para asegurar que no existan conflictos entre fases Q1 y Q2 habilitadas para el mismo periodo. Si se intenta habilitar una fase Q1 mientras hay una fase Q2 activa, o viceversa, se devuelve un mensaje de error indicando el conflicto. Si la validación pasa sin problemas, se procede a crear el nuevo control de subida de notas y se devuelve un mensaje de éxito junto con los datos del nuevo registro.
     */
    public function store(Request $request)
    {
        $inputs = $request->input();//Obtener los datos enviados por el formulario

        // Validación Q1 vs Q2 si se intenta guardar como habilitado
        if (isset($inputs['habilitado']) && $inputs['habilitado'] == 1) {//Si se intenta guardar como habilitado
            $conflicto = $this->validarConflictoFases($inputs['id_periodo'], $inputs['fase_evaluacion']);//Validar si hay conflictos entre fases Q1 y Q2 habilitadas para el mismo periodo
            if ($conflicto) {//Si hay conflictos, devolver un mensaje de error indicando el conflicto
                return response()->json(['error' => true, 'mensaje' => $conflicto], 400);
            }
        }
        //Crear el registro de control de subida de notas
        $res = Control_subida_notas::create($inputs);
        //Devolver los datos creados en formato JSON, incluyendo un mensaje de éxito
        return response()->json([
            'data' => $res,
            'mensaje' => 'Fase configurada con Éxito!!',
        ]);
    }

    /**
     * Función para mostrar un control de subida de notas específico, recibiendo como parámetro el id del registro.
     * La función show es la encargada de mostrar un control de subida de notas específico, recibiendo como parámetro el id del registro. La función busca el control de subida de notas con el id proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. Si no encuentra el control de subida de notas, devuelve un mensaje de error indicando que el registro no existe.
     */
    public function show(string $id)
    {
        // Obtener el objeto Control_subida_notas con el id proporcionado
        $res = Control_subida_notas::find($id);
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
                'mensaje' => "El Control de Subida de Notas con id: $id no Existe",
            ]);
        }
    }

    /**
     * Función para actualizar los datos de un control de subida de notas específico, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario y el id del registro.
     * La función update es la encargada de actualizar los datos de un control de subida de notas específico, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario y el id del registro. La función busca el control de subida de notas con el id proporcionado y, si lo encuentra, actualiza los datos enviados por el formulario y guarda los cambios en la base de datos. Si no encuentra el control de subida de notas, devuelve un mensaje de error indicando que el registro no existe.
     */
    public function update(Request $request, string $id)
    {
        $res = Control_subida_notas::find($id);//Obtener el objeto Control_subida_notas con el id proporcionado

        if (isset($res)) {//Si el objeto existe
            $habilitado = $request->habilitado;//Obtener el valor de habilitado del formulario
            $fecha_fin = $request->fecha_fin;//Obtener la fecha de fin del formulario

            // 1. Validación Q1 vs Q2 si se intenta guardar como habilitado
            if ($habilitado == 1) {//Si se intenta guardar como habilitado
                $conflicto = $this->validarConflictoFases($request->id_periodo, $request->fase_evaluacion);//Validar si hay conflictos entre fases Q1 y Q2 habilitadas para el mismo periodo
                if ($conflicto) {//Si hay conflictos, devolver un mensaje de error indicando el conflicto
                    return response()->json(['error' => true, 'mensaje' => $conflicto], 400);
                }
            }

            // 2. Validación de Inhabilitación automática si la fecha_fin ya se superó
            if ($fecha_fin) {//Si se ha indicado una fecha de fin
                $fechaFinParseada = Carbon::parse($fecha_fin);//Parsear la fecha de fin para compararla con la fecha actual
                if (Carbon::now()->greaterThanOrEqualTo($fechaFinParseada)) {//Si la fecha actual es mayor o igual a la fecha de fin, inhabilitar automáticamente
                    $habilitado = 0; // Forzamos a que se inhabilite
                }
            }
            //Actualizar los datos del control de subida de notas
            $res->id_periodo = $request->id_periodo;//Actualizar el id del periodo
            $res->fase_evaluacion = $request->fase_evaluacion;//Actualizar la fase de evaluación
            $res->fecha_inicio = $request->fecha_inicio;//Actualizar la fecha de inicio
            $res->fecha_fin = $fecha_fin;//Actualizar la fecha de fin
            $res->habilitado = $habilitado;//Actualizar el estado de habilitado
            //Guardar los cambios en la base de datos
            if ($res->save()) {//Guardar los cambios en la base de datos
                return response()->json([//Devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
                    'data' => $res,
                    'mensaje' => 'Actualizado con Éxito!! ' . ($habilitado == 0 && $request->habilitado == 1 ? '(Se inhabilitó automáticamente porque la fecha fin es pasada o actual)' : ''),
                ]);
                //Si ocurre algún error, devolver un mensaje de error en formato JSON
            } else {
                return response()->json(['error' => true, 'mensaje' => 'Error al Actualizar'], 500);
            }
        } else {
            //Si el objeto no existe, devolver un mensaje de error en formato JSON
            return response()->json(['error' => true, 'mensaje' => "El Control con id: $id no Existe"], 404);
        }
    }

    /**
     * Función para eliminar un control de subida de notas específico, recibiendo como parámetro el id del registro.
     * La función destroy es la encargada de eliminar un control de subida de notas específico, recibiendo como parámetro el id del registro. La función busca el control de subida de notas con el id proporcionado y, si lo encuentra, inhabilita el nivel académico y guarda los cambios en la base de datos. 
     * Luego, devuelve los datos actualizados en formato JSON, incluyendo un mensaje de éxito. 
     * Si no encuentra el control de subida de notas, devuelve un mensaje de error indicando que el registro no existe.
     */
    public function destroy(string $id)
    {
        // Obtener el objeto Control_subida_notas con el id proporcionado
        $res = Control_subida_notas::find($id);
        // Si el objeto existe, inhabilitar el nivel académico y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {//Si el objeto existe
            $res->habilitado = 0;//Inhabilitar el nivel académico
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
                    'mensaje' => 'El Control de Subida de Notas no existe (puede que ya lo haya eliminado)',
                ]);
            }
        } else {
            // Si el objeto no existe, devolver un mensaje de error en formato JSON
            return response()->json([
                'error' => true,
                'mensaje' => "El Control de Subida de Notas con id: $id no Existe",
            ]);
        }
    }
    /**
     * Función para habilitar un control de subida de notas específico, recibiendo como parámetro el id del registro.
     * La función habilitar es la encargada de habilitar un control de subida de notas específico, recibiendo como parámetro el id del registro. La función busca el control de subida de notas con el id proporcionado y, si lo encuentra, habilita el nivel académico y guarda los cambios en la base de datos. 
     * Luego, devuelve los datos actualizados en formato JSON, incluyendo un mensaje de éxito. 
     * Si no encuentra el control de subida de notas, devuelve un mensaje de error indicando que el registro no existe.
     */
    public function habilitar(string $id)
    {
        $res = Control_subida_notas::find($id);//Obtener el objeto Control_subida_notas con el id proporcionado

        if (isset($res)) {//Si el objeto existe
            // Validación Q1 vs Q2 antes de habilitar
            $conflicto = $this->validarConflictoFases($res->id_periodo, $res->fase_evaluacion);//Validar si hay conflictos entre fases Q1 y Q2 habilitadas para el mismo periodo
            if ($conflicto) {//Si hay conflictos, devolver un mensaje de error indicando el conflicto   
                return response()->json(['error' => true, 'mensaje' => $conflicto], 400);
            }

            $res->habilitado = 1;//Habilitar el nivel académico
            $res->save();//Guardar los cambios en la base de datos
            //Devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
            return response()->json([
                'data' => $res->toArray(),
                'mensaje' => 'Fase habilitada con Éxito!!',
            ]);
        } else {
            return response()->json(['error' => true, 'mensaje' => 'El Control no Existe'], 404);
        }
    }
    /**
     * Función privada para validar si hay conflictos entre fases Q1 y Q2 habilitadas para el mismo periodo.
     * La función validarConflictoFases es una función privada que se encarga de validar si hay conflictos entre fases Q1 y Q2 habilitadas para el mismo periodo. La función recibe como parámetros el id del periodo y la fase de evaluación que se intenta habilitar.
     * La función verifica si la fase de evaluación que se intenta habilitar es del tipo Q1 o Q2. 
     * Si la fase de evaluación es del tipo Q1, se verifica si hay otros controles de subida de notas habilitados para el periodo que tengan la fase Q2. 
     * Si la fase de evaluación es del tipo Q2, se verifica si hay otros controles de subida de notas habilitados para el periodo que tengan la fase Q1. 
     * Si hay conflictos, la función devuelve un mensaje de error indicando el conflicto. Si no hay conflictos, devuelve null.
     */
    private function validarConflictoFases(int $id_periodo, string $fase_evaluacion)
    {
        $isQ1 = str_starts_with($fase_evaluacion, 'Q1');//Verificar si la fase de evaluación es del tipo Q1
        $isQ2 = str_starts_with($fase_evaluacion, 'Q2');//Verificar si la fase de evaluación es del tipo Q2
        //Verificar si la fase de evaluación es del tipo Q1
        if ($isQ1) {
            $q2Activos = Control_subida_notas::where('id_periodo', $id_periodo)//Obtener los controles de subida de notas habilitados para el periodo que tengan la fase Q2
                ->where('fase_evaluacion', 'LIKE', 'Q2%')//Filtrar solo los controles de subida de notas que tengan la fase Q2
                ->where('habilitado', 1)->count();//Contar el número de controles de subida de notas habilitados
            if ($q2Activos > 0) {//Si hay controles de subida de notas habilitados para el periodo que tengan la fase Q2, devolver un mensaje de error indicando el conflicto
                return 'No se puede habilitar una fase Q1 porque existen fases del Q2 habilitadas.';
            }
        }
        //Verificar si la fase de evaluación es del tipo Q2
        if ($isQ2) {
            $q1Activos = Control_subida_notas::where('id_periodo', $id_periodo)//Obtener los controles de subida de notas habilitados para el periodo que tengan la fase Q1
                ->where('fase_evaluacion', 'LIKE', 'Q1%')//Filtrar solo los controles de subida de notas que tengan la fase Q1
                ->where('habilitado', 1)->count();//Contar el número de controles de subida de notas habilitados
            if ($q1Activos > 0) {//Si hay controles de subida de notas habilitados para el periodo que tengan la fase Q1, devolver un mensaje de error indicando el conflicto
                return 'No se puede habilitar una fase Q2 porque existen fases del Q1 habilitadas.';
            }
        }

        return null; // Sin conflictos
    }

    /**
     * Función para obtener las asignaturas que da un docente específico, recibiendo como parámetro el id del docente.
     * La función getAsignaturasDocente es la encargada de obtener las asignaturas que da un docente específico, recibiendo como parámetro el id del docente. 
     * La función busca las asignaturas que pertenecen al docente con el id proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. 
     * Si no encuentra el docente, devuelve un mensaje de error indicando que el docente no existe.
     */
    public function getAsignaturasDocente(int $id_docente)
    {
        // 1. Buscamos el periodo lectivo activo
        $periodoActivo = Periodos_lectivos::where('estado_activo', 1)->first();//Obtener el periodo lectivo activo

        // Si no hay periodo activo, retornamos un arreglo vacío de inmediato
        if (! $periodoActivo) {
            return response()->json([], 200);
        }

        // 2. Obtener asignaturas asegurando que el curso sea del periodo vigente
        $asignaturas = Curso_Asignaturas::with(['curso.nivel', 'curso.especialidad', 'asignatura'])//Incluir todas las relaciones necesarias para mostrar los datos de las asignaturas(curso, nivel, especialidad, asignatura)
            ->where('id_docente', $id_docente)//Filtrar solo las asignaturas del docente especificado
            ->where('estado', 1)//Filtrar solo las asignaturas activas
            ->whereHas('curso', function ($query) use ($periodoActivo) {
                // Filtro clave: Solo materias vinculadas a cursos de este periodo
                $query->where('id_periodo', $periodoActivo->id_periodo);
            })
            ->get();//Obtener los datos de la consulta
        //Devolver los datos en formato JSON
        return response()->json($asignaturas, 200);
    }

    /**
     * Función para obtener los estudiantes, sus calificaciones y fases activas para una asignatura específica, recibiendo como parámetro el id de la asignatura.
     * La función getEstudiantesAsignatura es la encargada de obtener los estudiantes, sus calificaciones y fases activas para una asignatura específica, recibiendo como parámetro el id de la asignatura. 
     * La función busca los estudiantes, sus calificaciones y fases activas para la asignatura con el id proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. 
     * Si no encuentra la asignatura, devuelve un mensaje de error indicando que la asignatura no existe.
     */
    public function getEstudiantesAsignatura(int $id_curso_asignatura)
    {
        try {
            // 1. Obtener el periodo lectivo activo actual
            $periodoActivo = Periodos_lectivos::where('estado_activo', 1)->first();//Obtener el periodo lectivo activo
            //Si no hay periodo activo, devolver un mensaje de error
            if (!$periodoActivo) {
                return response()->json(['mensaje' => 'No hay un periodo lectivo activo configurado.'], 404);
            }

            // 2. Buscamos la asignatura FORZANDO que el curso pertenezca al periodo activo
            $asignatura = Curso_Asignaturas::with([//Incluir todas las relaciones necesarias para mostrar los datos de las asignaturas(curso, matriculas, calificaciones)
                'curso.matriculas.estudiante',
                'curso.matriculas.calificaciones' => function ($q) use ($id_curso_asignatura) {
                    // Solo traer las calificaciones de esta materia específica
                    $q->where('id_curso_asignatura', $id_curso_asignatura);//Filtrar solo las calificaciones de la asignatura especificada
                },
            ])
                ->whereHas('curso', function ($q) use ($periodoActivo) {//Integridad referencial: El curso debe ser del periodo activo
                    // Integridad referencial: El curso debe ser del periodo activo
                    $q->where('id_periodo', $periodoActivo->id_periodo);//Filtrar solo los cursos del periodo activo
                })
                ->find($id_curso_asignatura);//Obtener la asignatura con el id proporcionado
            //Si no encuentra la asignatura, devolver un mensaje de error indicando que la asignatura no existe
            if (!$asignatura) {
                return response()->json(['mensaje' => 'Asignatura no encontrada o no corresponde al periodo activo.'], 404);
            }

            // 3. Fases habilitadas filtradas ESTRICTAMENTE por el periodo activo
            // Evita que fases "activas" de años anteriores afecten el ingreso actual
            $fasesActivas = Control_subida_notas::where('id_periodo', $periodoActivo->id_periodo)//Obtener las fases habilitadas del periodo activo
                ->where('habilitado', 1)//Filtrar solo las fases habilitadas
                ->pluck('fase_evaluacion');//Obtener las fases habilitadas

            // 4. Mapeamos las matrículas del curso
            $estudiantes = $asignatura->curso->matriculas->map(function ($m) use ($id_curso_asignatura) {//Iterar sobre cada matrícula
                $calificacion = $m->calificaciones->first();//Obtener la calificación de la matrícula

                // Si no tiene registro, armamos el esqueleto en ceros
                if (!$calificacion) {
                    $calificacion = [//Armar esqueleto en ceros
                        'id_matricula' => $m->id_matricula,
                        'id_curso_asignatura' => $id_curso_asignatura,
                        'q1_p1' => '0.00',
                        'q1_p2' => '0.00',
                        'q1_p3' => '0.00',
                        'q1_examen' => '0.00',
                        'q1_promedio' => '0.00',
                        'q2_p1' => '0.00',
                        'q2_p2' => '0.00',
                        'q2_p3' => '0.00',
                        'q2_examen' => '0.00',
                        'q2_promedio' => '0.00',
                        'promedio_anual' => '0.00',
                        'nota_supletorio' => null,
                        'nota_remedial' => null,
                        'nota_gracia' => null,
                        'nota_final_definitiva' => '0.00',
                        'estado_asignatura' => 'Reprobado',
                    ];
                }

                return [//Devolver los datos de la matrícula junto con las calificaciones
                    'id_matricula' => $m->id_matricula,
                    'estudiante' => [
                        'id_persona' => $m->estudiante->id_persona,
                        'cedula' => $m->estudiante->cedula,
                        'nombres' => $m->estudiante->nombres,
                        'apellidos' => $m->estudiante->apellidos,
                        'foto' => $m->estudiante->foto ? base64_encode($m->estudiante->foto) : null,
                    ],
                    'calificaciones' => $calificacion,
                ];
            })->sortBy(function ($item) {//Ordenar los estudiantes por apellidos de la matrícula
                return $item['estudiante']['apellidos'];
            })->values();//Devolver los valores de la colección

            return response()->json([//Devolver los datos en formato JSON
                'estudiantes' => $estudiantes,
                'fases_activas' => $fasesActivas,
                'periodo_actual' => $periodoActivo->nombre 
            ], 200);
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error interno en el servidor: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Función para guardar las calificaciones de los estudiantes, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     * La función guardarCalificaciones es la encargada de guardar las calificaciones de los estudiantes, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario. 
     * La función itera sobre cada estudiante y guarda las calificaciones en la base de datos, si la calificación existe, actualiza las calificaciones, si no existe, crea las calificaciones. 
     * Luego, devuelve los datos actualizados en formato JSON, incluyendo un mensaje de éxito. 
     * Si ocurre algún error, devuelve un mensaje de error en formato JSON.
     */
    public function guardarCalificaciones(Request $request)
    {
        $estudiantes = $request->estudiantes; // Array de estudiantes con sus notas

        foreach ($estudiantes as $est) {//Iterar sobre cada estudiante
            $datosNota = $est['calificaciones'];//Obtener las calificaciones del estudiante

            Calificaciones::updateOrCreate(//Actualizar o crear un nuevo registro de calificaciones
                [
                    'id_matricula' => $datosNota['id_matricula'],
                    'id_curso_asignatura' => $datosNota['id_curso_asignatura'],
                ],
                [
                    'q1_p1' => $datosNota['q1_p1'],
                    'q1_p2' => $datosNota['q1_p2'],
                    'q1_p3' => $datosNota['q1_p3'],
                    'q1_examen' => $datosNota['q1_examen'],
                    'q1_promedio' => $datosNota['q1_promedio'],
                    'q2_p1' => $datosNota['q2_p1'],
                    'q2_p2' => $datosNota['q2_p2'],
                    'q2_p3' => $datosNota['q2_p3'],
                    'q2_examen' => $datosNota['q2_examen'],
                    'q2_promedio' => $datosNota['q2_promedio'],
                    'promedio_anual' => $datosNota['promedio_anual'],
                    'nota_supletorio' => $datosNota['nota_supletorio'],
                    'nota_remedial' => $datosNota['nota_remedial'],
                    'nota_gracia' => $datosNota['nota_gracia'],
                    'nota_final_definitiva' => $datosNota['nota_final_definitiva'],
                    'estado_asignatura' => $datosNota['estado_asignatura'],
                ]
            );
        }
        //Devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        return response()->json(['mensaje' => 'Calificaciones guardadas exitosamente'], 200);
    }   
    /**
     * Función para obtener las calificaciones actuales de un estudiante específico, recibiendo como parámetro el id del estudiante.
     * La función getCalificacionesActuales es la encargada de obtener las calificaciones actuales de un estudiante específico, recibiendo como parámetro el id del estudiante. 
     * La función busca las calificaciones actuales de la matrícula del estudiante con el id proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. 
     * Si no encuentra la matrícula, devuelve un mensaje de error indicando que la matrícula no existe.
     */
    public function getCalificacionesActuales(int $id_estudiante)
    {
        // Buscamos la matrícula del estudiante en el periodo activo
        $matricula = Matriculas::with([//Incluir todas las relaciones necesarias para mostrar los datos de las matrículas(curso, periodo, nivel, especialidad, asignatura, calificaciones)
            'curso.periodo',
            'curso.nivel',
            'curso.especialidad',
            'calificaciones.curso_asignatura.asignatura',
        ])
            ->where('id_estudiante', $id_estudiante)//Filtrar solo las matrículas del estudiante especificado
            ->whereHas('curso.periodo', function ($query) {//Integridad referencial: El periodo debe ser del periodo activo
                $query->where('estado_activo', 1); // Solo el periodo activo
            })
            ->first();//Obtener la matrícula con el id proporcionado
        //Si no encuentra la matrícula, devolver un mensaje de error indicando que la matrícula no existe
        if (! $matricula) {
            return response()->json([
                'status' => 'error',
                'mensaje' => 'El estudiante no tiene una matrícula activa en el periodo actual.',
            ], 404);
        }

        // Estructuramos la información del curso y periodo
        $cursoInfo = [//Estructuramos la información del curso y periodo
            'periodo' => $matricula->curso->periodo->nombre,
            'nivel' => $matricula->curso->nivel->nombre,
            'especialidad' => $matricula->curso->especialidad ? $matricula->curso->especialidad->nombre : '',
            'paralelo' => $matricula->curso->paralelo,
        ];

        // Mapeamos las calificaciones
        $calificaciones = $matricula->calificaciones->map(function ($calificacion) {//Iterar sobre cada calificación
            return [//Devolver los datos de la calificación
                'asignatura' => $calificacion->curso_asignatura->asignatura->nombre,
                // Quimestre 1
                'q1_p1' => $calificacion->q1_p1,
                'q1_p2' => $calificacion->q1_p2,
                'q1_p3' => $calificacion->q1_p3,
                'q1_promedio' => $calificacion->q1_promedio,

                // Quimestre 2
                'q2_p1' => $calificacion->q2_p1,
                'q2_p2' => $calificacion->q2_p2,
                'q2_p3' => $calificacion->q2_p3,
                'q2_promedio' => $calificacion->q2_promedio,
                'promedio_anual' => $calificacion->promedio_anual,
                'nota_supletorio' => $calificacion->nota_supletorio,
                'nota_remedial' => $calificacion->nota_remedial,
                'nota_gracia' => $calificacion->nota_gracia,
                'nota_final_definitiva' => $calificacion->nota_final_definitiva,
                'estado_asignatura' => $calificacion->estado_asignatura,
            ];
        });
        //Devolver los datos en formato JSON
        return response()->json([
            'status' => 'success',
            'curso' => $cursoInfo,
            'calificaciones' => $calificaciones,
        ], 200);
    }
    /**
     * Función para buscar las matrículas asociadas a una cédula específica, recibiendo como parámetro el cédula.
     * La función buscarPorCedula es la encargada de buscar las matrículas asociadas a una cédula específica, recibiendo como parámetro el cédula. 
     * La función busca las matrículas asociadas a la cédula del estudiante con el cédula proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. 
     * Si no encuentra ninguna matrícula, devuelve un mensaje de error indicando que no se encontró ninguna matrícula.
     */
    public function buscarPorCedula(string $cedula)
    {
        try {
            // Buscamos todas las matrículas asociadas a la cédula del estudiante
            $matriculas = Matriculas::whereHas('estudiante', function ($query) use ($cedula) {//Filtrar solo las matrículas asociadas a la cédula del estudiante
                $query->where('cedula', $cedula);//Filtrar solo las matrículas asociadas a la cédula del estudiante
            })
                ->with([//Incluir todas las relaciones necesarias para mostrar los datos de las matrículas(estudiante, curso, periodo, nivel, especialidad, asignatura, calificaciones, asistencias)
                    'estudiante',
                    'curso.periodo',
                    'curso.nivel',
                    'curso.especialidad',
                    'calificaciones.curso_asignatura.asignatura',
                    'asistencias', 
                ])
                ->get();//Obtener los datos de la consulta

            if ($matriculas->isEmpty()) {//Si no se encontraron matrículas, devolver un mensaje de error indicando que no se encontró ninguna matrícula
                return response()->json([
                    'status' => 'error',
                    'message' => 'No se encontró ningún estudiante o historial con el número de cédula proporcionado.',
                ], 404);
            }

            // Extraemos los datos del estudiante del primer registro de matrícula
            $estudiante = $matriculas->first()->estudiante;

            // Procesamos el historial de calificaciones y asistencia agrupando por cada matrícula/periodo
            $historial = $matriculas->map(function ($matricula) {//Iterar sobre cada matrícula
                $subpromedios = 0;
                $materiasContadas = 0;
                $reprobadas = 0;

                //Procesamiento de CALIFICACIONES
                $calificacionesProcesadas = $matricula->calificaciones->map(function ($cal) use (&$subpromedios, &$materiasContadas, &$reprobadas) {//Iterar sobre cada calificación
                    $notaFinal = floatval($cal->nota_final_definitiva ?? 0);//Obtener la nota final definida o promedio anual
                    $subpromedios += $notaFinal;//Sumar la nota final definida o promedio anual
                    $materiasContadas++;//Incrementar contador de materias contadas

                    // Determinar estado de la asignatura
                    $estado = $cal->estado_asignatura;
                    if (! $estado) {//Si no se encuentra un estado, establecerlo en APROBADO si la nota final es mayor o igual a 7 o REPROBADO en caso contrario
                        $estado = $notaFinal >= 7 ? 'APROBADO' : 'REPROBADO';
                    }
                    if (strtoupper($estado) === 'REPROBADO') {//Si la nota final es REPROBADO, incrementar contador de reprobadas
                        $reprobadas++;
                    }

                    return [//Devolver los datos de la calificación
                        'asignatura' => $cal->curso_asignatura->asignatura->nombre ?? 'N/A',
                        'nota_final' => $notaFinal,
                        'estado' => $estado,
                    ];
                });
                //Cálculo de promedio general
                $promedioPeriodo = $materiasContadas > 0 ? round($subpromedios / $materiasContadas, 2) : 0;//Promedio general de las calificaciones
                // El curso completo se aprueba si el promedio es >= 7 y no tiene materias reprobadas
                $estadoCurso = ($promedioPeriodo >= 7 && $reprobadas === 0) ? 'APROBADO' : 'REPROBADO';

                // Cálculo de asistencias
                $totalAsistencias = $matricula->asistencias->count();//Total de asistencias registradas para la matrícula

                // Filtramos solo las que no penalizan (Presente y Justificado)
                $asistenciasValidas = $matricula->asistencias->filter(function ($a) {//Filtrar solo las asistencias validas (Presente y Justificado)
                    return in_array(strtolower($a->estado), ['presente', 'justificado']);//Verificar si la asistencia es presente o justificada
                })->count();//Contar el número de asistencias validas

                // Calculamos el porcentaje
                $porcentajeAsistencia = $totalAsistencias > 0 //Verificar si hay asistencias registradas
                    ? round(($asistenciasValidas / $totalAsistencias) * 100, 2)//Calcular el porcentaje de asistencias validas
                    : 100.00;//Si no hay asistencias, devolver 100.00

                return [//Devolver los datos de la matrícula junto con las calificaciones y asistencias
                    'id_matricula' => $matricula->id_matricula,
                    'periodo' => $matricula->curso->periodo->nombre ?? 'N/A',
                    'nivel_id' => $matricula->curso->id_nivel,
                    'nivel_nombre' => $matricula->curso->nivel->nombre ?? 'N/A',
                    'paralelo' => $matricula->curso->paralelo ?? '',
                    'especialidad' => $matricula->curso->especialidad->nombre ?? null,
                    'promedio_general' => $promedioPeriodo,
                    'estado_curso' => $estadoCurso,
                    'porcentaje_asistencia' => $porcentajeAsistencia, 
                    'calificaciones' => $calificacionesProcesadas,
                ];
            });

            // Traemos todos los niveles académicos registrados en el sistema ordenados por jerarquía
            $nivelesSistema = Niveles_academicos::where('estado', 1)//Filtrar solo los niveles académicos activos
                ->orderBy('orden_jerarquia', 'asc')//Ordenar por orden jerarquia
                ->get(['id_nivel', 'nombre', 'orden_jerarquia']);//Obtener los datos de la consulta

            return response()->json([//Devolver los datos en formato JSON
                'status' => 'success',
                'estudiante' => [//Datos del Estudiante
                    'nombres' => $estudiante->nombres,
                    'apellidos' => $estudiante->apellidos,
                    'cedula' => $estudiante->cedula,
                    'foto' => $estudiante->foto ? base64_encode($estudiante->foto) : null,
                    'telefono' => $estudiante->telefono,
                ],
                'historial' => $historial,
                'niveles_sistema' => $nivelesSistema,
            ], 200);
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage(),
            ], 500);
        }
    }
}
