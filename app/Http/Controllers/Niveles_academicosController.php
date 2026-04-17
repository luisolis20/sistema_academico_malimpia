<?php

namespace App\Http\Controllers;

use App\Models\Niveles_academicos;
use Illuminate\Http\Request;


class Niveles_academicosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            //Definir el número de elementos por página, con un máximo de 50
            $perPage = $request->input('per_page', 10);
            //Limitar el número de elementos por página a 20
            $perPage = min($perPage, 20);
            //Obtener la consulta de búsqueda
            $searchQuery = $request->input('search_query');
            //Crear la consulta base
            $query = Niveles_academicos::select('niveles_academicos.*');
            //Si hay una consulta de búsqueda, aplicarla a los campos relevantes
            if (! empty($searchQuery)) {
                //Crear una consulta de búsqueda para cada campo relevante
                $query->where(function ($q) use ($searchQuery) {
                    //Aplicar la consulta de búsqueda a cada campo relevante, en este caso, solo a nombre de nivel académico
                    $q->where('niveles_academicos.nombre', 'LIKE', "%{$searchQuery}%");
                });
            }
            //Obtener los datos paginados
            $data = $query->paginate($perPage);
            //Si no hay datos, devolver un mensaje de error
            if ($data->isEmpty()) {
                return response()->json(['data' => [], 'message' => 'No se encontraron datos'], 200);
            }
            //Transformar los datos a UTF-8 para evitar problemas de codificación al convertir a JSON
            $data->getCollection()->transform(function ($item) {
                $attributes = $item->getAttributes();
                foreach ($attributes as $key => $value) {
                    if (is_string($value)) {
                        $attributes[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                    }
                }
                return $attributes;
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
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al codificar los datos a JSON: ' . $e->getMessage()], 500);
        }
    }
   
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Obtener los datos enviados por el formulario
        $inputs = $request->input();
        //Crear el objeto Niveles_academicos con los datos enviados
        $res = Niveles_academicos::create($inputs);
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
        //Obtener el objeto Niveles_academicos con el id proporcionado
        $res = Niveles_academicos::find($id);
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
                'mensaje' => "El nivel académico con id: $id no Existe",
            ]);
        }
    }
    // Traer niveles académicos activos
    public function getActivados()
    {
        try {
            $niveles = Niveles_academicos::select('niveles_academicos.*')
                ->where('estado', 1)
                ->get();

            return response()->json([
                'status' => true,
                'data' => $niveles,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al codificar los datos a JSON: '.$e->getMessage()], 500);
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //Obtener el objeto Niveles_academicos con el id proporcionado
        $res = Niveles_academicos::find($id);
        //Si el objeto existe, actualizar los datos enviados por el formulario y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {
            $res->nombre = $request->nombre;
            $res->orden_jerarquia = $request->orden_jerarquia;
            $res->estado = $request->estado;
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
                'mensaje' => "El nivel académico con id: $id no Existe",
            ]);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //Obtener el objeto Niveles_academicos con el id proporcionado
        $res = Niveles_academicos::find($id);
        //Si el objeto existe, inhabilitar el nivel académico y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {
            $res->estado = 0;
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
                    'mensaje' => "El nivel académico no existe (puede que ya la haya eliminado)",
                ]);
            }
        } else {     
            //Si el objeto no existe, devolver un mensaje de error en formato JSON
            return response()->json([
                'error' => true,
                'mensaje' => "El nivel académico con id: $id no Existe",
            ]);
        }
    }
    public function habilitar(string $id)
    {
        //Obtener el objeto Niveles_academicos con el id proporcionado
        $res = Niveles_academicos::find($id);
        //Si el objeto existe, habilitar el nivel académico y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {
            $res->estado = 1;
            $res->save();
            $data = $res->toArray();
            if ($data) {
                //Devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
                return response()->json([
                    'data' => $data,
                    'mensaje' => "Habilitado con Éxito!!",
                ]);
            } else {
                //Si ocurre algún error, devolver un mensaje de error en formato JSON
                return response()->json([
                    'data' => $data,
                    'mensaje' => "El nivel académico no existe (puede que ya la haya eliminado)",
                ]);
            }
        } else {
            //Si el objeto no existe, devolver un mensaje de error en formato JSON
            return response()->json([
                'error' => true,
                'mensaje' => "El rol con id: $id no Existe",
            ]);
        }
    }
}
