<?php

namespace App\Http\Controllers;

use App\Models\Control_subida_notas;
use App\Models\Periodos_lectivos;
use Carbon\Carbon;
use Illuminate\Http\Request;


class Control_SubidaNotasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $periodoActivo = Periodos_lectivos::where('estado_activo', 1)->first();

            if (!$periodoActivo) {
                return response()->json(['data' => [], 'message' => 'No hay un periodo lectivo activo'], 404);
            }

            $query = Control_subida_notas::where('id_periodo', $periodoActivo->id_periodo);
            $data = $query->get();

            // MAGIA AUTOMÁTICA: Revisar si la fecha fin ya pasó para inhabilitarlos
            $now = Carbon::now();
            foreach ($data as $control) {
                if ($control->habilitado == 1 && $control->fecha_fin) {
                    $fechaFin = Carbon::parse($control->fecha_fin);
                    if ($now->greaterThanOrEqualTo($fechaFin)) {
                        $control->habilitado = 0;
                        $control->save();
                    }
                }
            }

            return response()->json([
                'data' => $data,
                'periodo_activo' => $periodoActivo
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }
   
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $inputs = $request->input();
        
        // Validación Q1 vs Q2 si se intenta guardar como habilitado
        if (isset($inputs['habilitado']) && $inputs['habilitado'] == 1) {
            $conflicto = $this->validarConflictoFases($inputs['id_periodo'], $inputs['fase_evaluacion']);
            if ($conflicto) {
                return response()->json(['error' => true, 'mensaje' => $conflicto], 400);
            }
        }

        $res = Control_subida_notas::create($inputs);

        return response()->json([
            'data' => $res,
            'mensaje' => "Fase configurada con Éxito!!",
        ]);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //Obtener el objeto Control_subida_notas con el id proporcionado
        $res = Control_subida_notas::find($id);
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
                'mensaje' => "El Control de Subida de Notas con id: $id no Existe",
            ]);
        }
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $res = Control_subida_notas::find($id);
        
        if (isset($res)) {
            $habilitado = $request->habilitado;
            $fecha_fin = $request->fecha_fin;

            // 1. Validación Q1 vs Q2 si se intenta guardar como habilitado
            if ($habilitado == 1) {
                $conflicto = $this->validarConflictoFases($request->id_periodo, $request->fase_evaluacion);
                if ($conflicto) {
                    return response()->json(['error' => true, 'mensaje' => $conflicto], 400);
                }
            }

            // 2. Validación de Inhabilitación automática si la fecha_fin ya se superó
            if ($fecha_fin) {
                $fechaFinParseada = Carbon::parse($fecha_fin);
                if (Carbon::now()->greaterThanOrEqualTo($fechaFinParseada)) {
                    $habilitado = 0; // Forzamos a que se inhabilite
                }
            }

            $res->id_periodo = $request->id_periodo;
            $res->fase_evaluacion = $request->fase_evaluacion;
            $res->fecha_inicio = $request->fecha_inicio;
            $res->fecha_fin = $fecha_fin;
            $res->habilitado = $habilitado;
            
            if ($res->save()) {
                return response()->json([
                    'data' => $res,
                    'mensaje' => "Actualizado con Éxito!! " . ($habilitado == 0 && $request->habilitado == 1 ? "(Se inhabilitó automáticamente porque la fecha fin es pasada o actual)" : ""),
                ]);
            } else {
                return response()->json(['error' => true, 'mensaje' => "Error al Actualizar"], 500);
            }
        } else {
            return response()->json(['error' => true, 'mensaje' => "El Control con id: $id no Existe"], 404);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //Obtener el objeto Control_subida_notas con el id proporcionado
        $res = Control_subida_notas::find($id);
        //Si el objeto existe, inhabilitar el nivel académico y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {
            $res->habilitado = 0;
            $res->save();
            $data = $res->toArray();
            if ($data) {
                //Devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
                return response()->json([
                    'data' => $data,
                    'mensaje' => "Inhabilitado con Éxito!!",
                ]);
            } else {
                //Si ocurre algún error, devolver un mensaje de error en formato JSON
                return response()->json([
                    'data' => $data,
                    'mensaje' => "El Control de Subida de Notas no existe (puede que ya lo haya eliminado)",
                ]);
            }
        } else {     
            //Si el objeto no existe, devolver un mensaje de error en formato JSON
            return response()->json([
                'error' => true,
                'mensaje' => "El Control de Subida de Notas con id: $id no Existe",
            ]);
        }
    }
    public function habilitar(string $id)
    {
        $res = Control_subida_notas::find($id);

        if (isset($res)) {
            // Validación Q1 vs Q2 antes de habilitar
            $conflicto = $this->validarConflictoFases($res->id_periodo, $res->fase_evaluacion);
            if ($conflicto) {
                return response()->json(['error' => true, 'mensaje' => $conflicto], 400);
            }

            $res->habilitado = 1;
            $res->save();
            
            return response()->json([
                'data' => $res->toArray(),
                'mensaje' => "Fase habilitada con Éxito!!",
            ]);
        } else {
            return response()->json(['error' => true, 'mensaje' => "El Control no Existe"], 404);
        }
    }
    private function validarConflictoFases($id_periodo, $fase_evaluacion) {
        $isQ1 = str_starts_with($fase_evaluacion, 'Q1');
        $isQ2 = str_starts_with($fase_evaluacion, 'Q2');

        if ($isQ1) {
            $q2Activos = Control_subida_notas::where('id_periodo', $id_periodo)
                ->where('fase_evaluacion', 'LIKE', 'Q2%')
                ->where('habilitado', 1)->count();
            if ($q2Activos > 0) return 'No se puede habilitar una fase Q1 porque existen fases del Q2 habilitadas.';
        }

        if ($isQ2) {
            $q1Activos = Control_subida_notas::where('id_periodo', $id_periodo)
                ->where('fase_evaluacion', 'LIKE', 'Q1%')
                ->where('habilitado', 1)->count();
            if ($q1Activos > 0) return 'No se puede habilitar una fase Q2 porque existen fases del Q1 habilitadas.';
        }

        return null; // Sin conflictos
    }
}
