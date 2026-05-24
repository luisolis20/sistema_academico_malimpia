<?php

namespace App\Http\Controllers;

use App\Models\Horarios_clases;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class HorariosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perPage = min($request->input('per_page', 10), 20);
            $searchQuery = $request->input('search_query');

            // 1. CONSULTA BASE: Ordenamos por nivel y especialidad
            $query = DB::table('cursos')
                ->join('niveles_academicos', 'cursos.id_nivel', '=', 'niveles_academicos.id_nivel')
                ->join('especialidades', 'cursos.id_especialidad', '=', 'especialidades.id_especialidad')
                ->join('periodos_lectivos', 'cursos.id_periodo', '=', 'periodos_lectivos.id_periodo')
                ->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('curso_asignatura')
                        ->whereColumn('curso_asignatura.id_curso', 'cursos.id_curso');
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
                )
                // ORDENAMIENTO REQUERIDO
                ->orderBy('niveles_academicos.id_nivel', 'asc')
                ->orderBy('especialidades.nombre', 'asc')
                ->orderBy('cursos.paralelo', 'asc')
                ->where('periodos_lectivos.estado_activo', 1); // Solo mostrar cursos del periodo activo
            if (!empty($searchQuery)) {
                $query->where(function ($q) use ($searchQuery) {
                    $q->where('niveles_academicos.nombre', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('cursos.paralelo', 'LIKE', "%{$searchQuery}%");
                });
            }

            $data = $query->paginate($perPage);

            if ($data->isEmpty()) {
                return response()->json(['data' => [], 'message' => 'No se encontraron datos'], 200);
            }

            $cursoIds = collect($data->items())->pluck('id_curso')->toArray();

            // 2. Traer detalles incluyendo al DOCENTE
            $detalles = DB::table('curso_asignatura')
                ->join('asignaturas', 'curso_asignatura.id_asignatura', '=', 'asignaturas.id_asignatura')
                ->leftJoin('personas as docentes', 'curso_asignatura.id_docente', '=', 'docentes.id_persona')
                ->leftJoin('horarios_clases', 'horarios_clases.id_curso_asignatura', '=', 'curso_asignatura.id_curso_asignatura')
                ->whereIn('curso_asignatura.id_curso', $cursoIds)
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
                )
                ->get();

            // 3. Agrupación (Igual que antes)
            $dataItems = collect($data->items())->map(function ($curso) use ($detalles) {
                $cursoArray = (array) $curso;
                foreach ($cursoArray as $key => $value) {
                    if (is_string($value)) {
                        $cursoArray[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                    }
                }

                $detallesCurso = $detalles->where('id_curso', $curso->id_curso);
                $asignaturasAgrupadas = [];
                $tieneHorario = false; // Bandera para saber si el curso ya tiene horarios

                foreach ($detallesCurso as $detalle) {
                    $idCa = $detalle->id_curso_asignatura;

                    if (!isset($asignaturasAgrupadas[$idCa])) {
                        $asignaturasAgrupadas[$idCa] = [
                            'id_curso_asignatura' => $idCa,
                            'asignatura'          => mb_convert_encoding($detalle->asignatura, 'UTF-8', 'UTF-8'),
                            'horas_semanales'     => $detalle->horas_semanales,
                            'id_docente'          => $detalle->id_docente,
                            'nombre_docente'      => mb_convert_encoding($detalle->nombre_docente ?? 'Sin asignar', 'UTF-8', 'UTF-8'),
                            'horarios'            => []
                        ];
                    }

                    if (!empty($detalle->id_horario)) {
                        $tieneHorario = true;
                        $asignaturasAgrupadas[$idCa]['horarios'][] = [
                            'id_horario'  => $detalle->id_horario,
                            'dia_semana'  => mb_convert_encoding($detalle->dia_semana, 'UTF-8', 'UTF-8'),
                            'hora_inicio' => $detalle->hora_inicio,
                            'hora_fin'    => $detalle->hora_fin
                        ];
                    }
                }

                $cursoArray['asignaturas_asignadas'] = array_values($asignaturasAgrupadas);
                $cursoArray['tiene_horario'] = $tieneHorario; // Frontend usará esto para mostrar "Crear" o "Editar"

                return $cursoArray;
            })->toArray();

            return response()->json([
                'data' => $dataItems,
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'per_page'     => $data->perPage(),
                    'total'        => $data->total(),
                    'last_page'    => $data->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    public function guardarHorario(Request $request)
    {
        // Esperamos un array 'horarios' y el 'id_curso'
        $horarios = $request->input('horarios');
        $id_curso = $request->input('id_curso');

        // --- 0. OBTENER EL PERIODO ACTIVO ---
        $periodoActivo = DB::table('periodos_lectivos')->where('estado_activo', 1)->first();

        if (!$periodoActivo) {
            return response()->json([
                'error' => 'No se encontró un periodo lectivo activo en el sistema para validar los horarios.'
            ], 400);
        }

        $idPeriodoActivo = $periodoActivo->id_periodo;

        DB::beginTransaction();
        try {
            // 1. Validar cruce de docentes
            foreach ($horarios as $item) {
                // Obtenemos el docente de esta asignatura
                $ca = DB::table('curso_asignatura')->where('id_curso_asignatura', $item['id_curso_asignatura'])->first();

                if ($ca && $ca->id_docente) {
                    // Verificar si el docente ya está dando clases en ese día y rango de horas en OTRO curso DEL PERIODO ACTIVO
                    $cruce = DB::table('horarios_clases')
                        ->join('curso_asignatura', 'horarios_clases.id_curso_asignatura', '=', 'curso_asignatura.id_curso_asignatura')
                        ->join('cursos', 'curso_asignatura.id_curso', '=', 'cursos.id_curso') // <-- NUEVO JOIN
                        ->where('cursos.id_periodo', $idPeriodoActivo) // <-- FILTRO CLAVE: Solo el periodo actual
                        ->where('curso_asignatura.id_docente', $ca->id_docente)
                        ->where('curso_asignatura.id_curso', '!=', $id_curso) // Excluir el curso actual
                        ->where('horarios_clases.dia_semana', $item['dia_semana'])
                        ->where(function ($query) use ($item) {
                            // Lógica de solapamiento de horas: (Inicio1 < Fin2) AND (Fin1 > Inicio2)
                            $query->where('horarios_clases.hora_inicio', '<', $item['hora_fin'])
                                ->where('horarios_clases.hora_fin', '>', $item['hora_inicio']);
                        })
                        ->exists();

                    if ($cruce) {
                        return response()->json([
                            'error' => "El docente ya tiene asignada otra asignatura a la misma hora en otro curso de este periodo (Día: {$item['dia_semana']}, Hora: {$item['hora_inicio']} - {$item['hora_fin']})."
                        ], 422);
                    }
                }
            }

            // 2. Limpiar los horarios anteriores de ESTE curso (para reemplazarlos por los nuevos)
            $idsCursoAsignatura = DB::table('curso_asignatura')->where('id_curso', $id_curso)->pluck('id_curso_asignatura');

            if ($idsCursoAsignatura->isNotEmpty()) {
                DB::table('horarios_clases')->whereIn('id_curso_asignatura', $idsCursoAsignatura)->delete();
            }

            // 3. Insertar los nuevos horarios
            $insertData = [];
            foreach ($horarios as $item) {
                $insertData[] = [
                    'id_curso_asignatura' => $item['id_curso_asignatura'],
                    'dia_semana'          => $item['dia_semana'],
                    'hora_inicio'         => $item['hora_inicio'],
                    'hora_fin'            => $item['hora_fin'],
                ];
            }

            if (count($insertData) > 0) {
                DB::table('horarios_clases')->insert($insertData);
            }

            DB::commit();
            return response()->json(['message' => 'Horario guardado correctamente'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al guardar el horario: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
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
     * Display the specified resource.
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {}
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {}
    public function habilitar(string $id) {}
    public function getHorarioDocente($id_persona)
    {
        // Buscamos los horarios filtrando a través de la relación curso_asignatura
        $horarios = Horarios_clases::with([
            'curso_asignatura.asignatura',
            'curso_asignatura.curso.nivel',
            'curso_asignatura.curso.especialidad'
        ])
            ->whereHas('curso_asignatura', function ($query) use ($id_persona) {
                $query->where('id_docente', $id_persona)
                    ->where('estado', 1);
            })
            ->orderBy('hora_inicio', 'asc')
            ->get();

        // Estructuramos la respuesta
        $data = $horarios->map(function ($h) {
            return [
                'id' => $h->id_horario,
                'dia' => $h->dia_semana, // Ejemplo: 'Lunes', 'Martes'...
                'inicio' => date('H:i', strtotime($h->hora_inicio)),
                'fin' => date('H:i', strtotime($h->hora_fin)),
                'asignatura' => $h->curso_asignatura->asignatura->nombre,
                'curso' => $h->curso_asignatura->curso->nivel->nombre . ' "' . $h->curso_asignatura->curso->paralelo . '"',
                'especialidad' => $h->curso_asignatura->curso->especialidad->nombre
            ];
        });

        return response()->json($data);
    }
}
