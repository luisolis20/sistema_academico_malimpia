<?php

namespace App\Http\Controllers;

use App\Models\Personas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;


class PersonaController extends Controller
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
            $query = Personas::select(
                'personas.*'
            );
            //Si hay una consulta de búsqueda, aplicarla a los campos relevantes
            if (! empty($searchQuery)) {
                //Crear una consulta de búsqueda para cada campo relevante
                $query->where(function ($q) use ($searchQuery) {
                    //Aplicar la consulta de búsqueda a cada campo relevante, en este caso, solo a cedula de persona
                    $q->where('personas.cedula', 'LIKE', "%{$searchQuery}%");
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
                    if (in_array($key, ['foto']) && ! empty($value)) {
                        // ✅ Convertir BLOB a base64
                        $attributes[$key] = base64_encode($value);
                    } elseif (is_string($value) && ! in_array($key, ['foto'])) {
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
        $inputs = $request->input();
        if (! empty($inputs['foto'])) {
            $inputs['foto'] = base64_decode($inputs['foto']);
        }

        $res = Personas::create($inputs);
        $data = $res->toArray();
        if (! empty($res->foto)) {
            $data['foto'] = base64_encode($res->foto);
        }

        return response()->json([
            'data' => $data,
            'mensaje' => 'Agregado con Éxito!!',
        ]);
    }
    public function getFotografia($ci)
    {
        try {
            // 1. Obtener SÓLO la columna 'imagen' para el ID específico
            $persona = Personas::where('id_persona', $ci)
                ->select('foto')
                ->first();

            // 2. Verificar si el producto existe y si tiene imagen
            if (! $persona || empty($persona->foto)) {
                // Devolver una respuesta HTTP 404 (Not Found)
                return response()->json(['error' => 'Fotografía no encontrada para el ID: ' . $ci], 404);
            }

            $fotoBinaria = $persona->foto;

            // 3. Determinar el MIME type
            $mime = 'image/jpeg'; // MIME type por defecto

            // Intenta determinar el MIME type si el ambiente lo permite
            if (extension_loaded('fileinfo')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $detectedMime = finfo_buffer($finfo, $fotoBinaria);
                finfo_close($finfo);

                if ($detectedMime && strpos($detectedMime, 'foto') === 0) {
                    $mime = $detectedMime;
                }
            }

            // 4. Devolver la imagen como una respuesta binaria (STREAM)
            return Response::make($fotoBinaria, 200)
                ->header('Content-Type', $mime)
                ->header('Content-Disposition', 'inline; filename="foto_' . $ci . '"');
        } catch (\Throwable $e) {
            // Log::error('Error en getFotografia DController: ' . $e->getMessage()); // Opcional
            return response()->json(['error' => 'Error al obtener la imagen: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Personas::select('personas.*')
            ->where('personas.id_persona', $id)
            ->paginate(20);
        if ($data->isEmpty()) {
            return response()->json(['error' => 'No se encontraron datos para el ID especificado'], 404);
        }

        // Convertir los campos a UTF-8 válido para cada página
        $data->getCollection()->transform(function ($item) {
            $attributes = $item->getAttributes();

            foreach ($attributes as $key => $value) {
                if (in_array($key, ['foto']) && ! empty($value)) {
                    // ✅ Convertir BLOB a base64
                    $attributes[$key] = base64_encode($value);
                } elseif (is_string($value) && ! in_array($key, ['foto'])) {
                    $attributes[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                }
            }

            return $attributes;
        });

        // Retornar la respuesta JSON con los metadatos de paginación
        try {
            return response()->json([
                'data' => $data->items(),
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'last_page' => $data->lastPage(),
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
        //Obtener el objeto Persona con el id proporcionado
        $res = Personas::find($id);
        //Si el objeto existe, actualizar los datos enviados por el formulario y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {
            $res->cedula = $request->cedula;
            $res->nombres = $request->nombres;
            $res->apellidos = $request->apellidos;
            $res->fecha_nacimiento = $request->fecha_nacimiento;
            $res->direccion = $request->direccion;
            $res->telefono = $request->telefono;
            $res->correo = $request->correo;
            $res->sexo = $request->sexo;
            if (!empty($request->foto)) {
                $res->foto = base64_decode($request->foto);
            }
            $res->estado = $request->estado;
            //Guardar los cambios en la base de datos
            if ($res->save()) {
                $data = $res->toArray();
                if (!empty($res->foto)) {
                    $data['foto'] = base64_encode($res->foto);
                }

                return response()->json([
                    'data' => $data,
                    'mensaje' => 'Actualizado con Éxito!!',
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
                'mensaje' => "La Persona con id: $id no Existe",
            ]);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //Obtener el objeto Persona con el id proporcionado
        $res = Personas::find($id);
        //Si el objeto existe, inhabilitar la persona y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {
            $res->estado = 0;
            $res->save();
            $data = $res->toArray();
            if ($data) {
                if (!empty($res->foto)) {
                    $data['foto'] = base64_encode($res->foto);
                }
                return response()->json([
                    'data' => $data,
                    'mensaje' => 'Inhabilitado con Éxito!!',
                ]);
            } else {
                if (!empty($res->foto)) {
                    $data['foto'] = base64_encode($res->foto);
                }
                return response()->json([
                    'data' => $data,
                    'mensaje' => 'La persona no existe (puede que ya la haya eliminado)',
                ]);
            }
        } else {
            //Si el objeto no existe, devolver un mensaje de error en formato JSON
            return response()->json([
                'error' => true,
                'mensaje' => "La Persona con id: $id no Existe",
            ]);
        }
    }
    public function habilitar(string $id)
    {
        //Obtener el objeto Persona con el id proporcionado
        $res = Personas::find($id);
        //Si el objeto existe, habilitar la persona y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {
            $res->estado = 1;
            $res->save();
            $data = $res->toArray();
            if ($data) {
                if (!empty($res->foto)) {
                    $data['foto'] = base64_encode($res->foto);
                }
                return response()->json([
                    'data' => $data,
                    'mensaje' => 'Habilitado con Éxito!!',
                ]);
            } else {
                if (!empty($res->foto)) {
                    $data['foto'] = base64_encode($res->foto);
                }
                return response()->json([
                    'data' => $data,
                    'mensaje' => 'La persona no existe (puede que ya la haya eliminado)',
                ]);
            }
        } else {
            //Si el objeto no existe, devolver un mensaje de error en formato JSON
            return response()->json([
                'error' => true,
                'mensaje' => "La Persona con id: $id no Existe",
            ]);
        }
    }
}
