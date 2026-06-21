<?php

namespace App\Http\Controllers;
//Importación de clases necesarias para el controlador Niveles_academicosController
use App\Models\Niveles_academicos;//Importación de la clase Niveles_academicos
use Illuminate\Http\Request;//Importación de la clase Request para manejar las solicitudes HTTP

//Clase Niveles_academicosController que representa un controlador en la aplicación para manejar las operaciones relacionadas con los niveles académicos
class Niveles_academicosController extends Controller
{
    /**
     * Función que muestra una lista de niveles académicos, las cuales pueden ser filtradas por página y por búsqueda.
     * La función index es la encargada de mostrar una lista de niveles académicos, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $perPage = min($perPage, 20);
            $searchQuery = $request->input('search_query');

            $query = Niveles_academicos::select('niveles_academicos.*');

            if (!empty($searchQuery)) {
                $query->where('niveles_academicos.nombre', 'LIKE', "%{$searchQuery}%");
            }

            // --- LÓGICA DE ORDENAMIENTO PERSONALIZADO ---
            // Usamos CASE para asignar un peso específico a cada patrón de nombre
            $query->orderByRaw("
            CASE 
                WHEN nombre = '0' THEN 1
                WHEN nombre LIKE '1ro%' AND nombre NOT LIKE '%Bachillerato' THEN 2
                WHEN nombre LIKE '2do%' AND nombre NOT LIKE '%Bachillerato' THEN 3
                WHEN nombre LIKE '3ro%' AND nombre NOT LIKE '%Bachillerato' THEN 4
                WHEN nombre LIKE '4to%' THEN 5
                WHEN nombre LIKE '5to%' THEN 6
                WHEN nombre LIKE '6to%' THEN 7
                WHEN nombre LIKE '7mo%' THEN 8
                WHEN nombre LIKE '8vo%' THEN 9
                WHEN nombre LIKE '9no%' THEN 10
                WHEN nombre LIKE '10mo%' THEN 11
                WHEN nombre LIKE '1ro%Bachillerato' THEN 12
                WHEN nombre LIKE '2do%Bachillerato' THEN 13
                WHEN nombre LIKE '3ro%Bachillerato' THEN 14
                ELSE 99 
            END ASC
        ");

            $data = $query->paginate($perPage);

            if ($data->isEmpty()) {
                return response()->json(['data' => [], 'message' => 'No se encontraron datos'], 200);
            }

            // Transformación de datos (UTF-8)
            $data->getCollection()->transform(function ($item) {
                $attributes = $item->getAttributes();
                foreach ($attributes as $key => $value) {
                    if (is_string($value)) {
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
            return response()->json(['error' => 'Error en el servidor: ' . $e->getMessage()], 500);
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
            return response()->json(['error' => 'Error al codificar los datos a JSON: ' . $e->getMessage()], 500);
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
