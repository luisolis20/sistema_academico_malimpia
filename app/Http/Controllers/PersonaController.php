<?php

namespace App\Http\Controllers;

// Importación de clases necesarias para el controlador PersonaController
use App\Models\Personas; // Importación de la clase Personas
use Illuminate\Http\Request; // Importación de la clase Request para manejar las solicitudes HTTP
use Illuminate\Support\Facades\Response; // Importación de la clase Response para manejar las respuestas HTTP

// Clase PersonaController que representa un controlador en la aplicación para manejar las operaciones relacionadas con las personas
class PersonaController extends Controller
{
    /**
     * Función que muestra una lista de personas, las cuales pueden ser filtradas por página y por búsqueda.
     * La función index es la encargada de mostrar una lista de personas, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
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
            // Crear la consulta base
            $query = Personas::select(
                'personas.*'
            ); // Seleccionar todos los campos de la tabla 'personas'
            // Si hay una consulta de búsqueda, aplicarla a los campos relevantes
            if (! empty($searchQuery)) {
                // Crear una consulta de búsqueda para cada campo relevante
                $query->where(function ($q) use ($searchQuery) {
                    // Aplicar la consulta de búsqueda a cada campo relevante, en este caso, solo a cedula de persona
                    $q->where('personas.cedula', 'LIKE', "%{$searchQuery}%");
                });
            }
            // Obtener los datos paginados
            $data = $query->paginate($perPage);
            // Si no hay datos, devolver un mensaje de error
            if ($data->isEmpty()) {
                return response()->json(['data' => [], 'message' => 'No se encontraron datos'], 200);
            }
            // Transformar los datos a UTF-8 para evitar problemas de codificación al convertir a JSON
            $data->getCollection()->transform(function ($item) {// Iterar sobre cada elemento de la colección
                $attributes = $item->getAttributes(); // Obtener los atributos del elemento

                foreach ($attributes as $key => $value) {// Iterar sobre cada clave-valor del array
                    if (in_array($key, ['foto']) && ! empty($value)) {// Si el valor es una cadena de caracteres
                        // ✅ Convertir BLOB a base64
                        $attributes[$key] = base64_encode($value); // Convertir la cadena de caracteres a base64
                    } elseif (is_string($value) && ! in_array($key, ['foto'])) {// Si el valor es una cadena de caracteres
                        $attributes[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8'); // Convertir la cadena de caracteres a UTF-8
                    }
                }

                return $attributes; // Devolver los atributos del elemento transformados
            });

            // Devolver los datos paginados en formato JSON, incluyendo la información de paginación
            return response()->json([
                'data' => $data->items(),
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'last_page' => $data->lastPage(),
                ],

            ], 200);
            // Si ocurre algún error, devolver un mensaje de error en formato JSON
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al codificar los datos a JSON: '.$e->getMessage()], 500);
        }
    }

    /**
     * Función para insertar nuevas personas en la base de datos, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     * La función store es la encargada de insertar nuevas personas en la base de datos, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     * Antes de insertar las nuevas personas, se realiza una validación para asegurar que no existan conflictos entre personas, especialidades y periodos lectivos. Si se intenta insertar una persona que ya existe, se devuelve un mensaje de error indicando el conflicto. Si se intenta insertar una especialidad que ya existe, se devuelve un mensaje de error indicando el conflicto. Si se intenta insertar un periodo lectivo que ya existe, se devuelve un mensaje de error indicando el conflicto. Si la validación pasa sin problemas, se procede a insertar las nuevas personas y se devuelve un mensaje de éxito junto con los datos del nuevo registro.
     */
    public function store(Request $request)
    {
        $inputs = $request->input(); // Obtener los datos enviados por el formulario
        if (! empty($inputs['foto'])) {// Si hay una foto enviada
            $inputs['foto'] = base64_decode($inputs['foto']); // Decodificar la foto enviada
        }

        $res = Personas::create($inputs); // Crear el persona con los datos enviados
        $data = $res->toArray(); // Convertir el objeto a un array para devolverlo en formato JSON
        if (! empty($res->foto)) {// Si la foto existe
            $data['foto'] = base64_encode($res->foto); // Convertir la foto a base64
        }

        // Devolver los datos creados en formato JSON, incluyendo un mensaje de éxito
        return response()->json([
            'data' => $data,
            'mensaje' => 'Agregado con Éxito!!',
        ]);
    }

    /**
     * Función para obtener la fotografía de una persona específica, recibiendo como parámetro el id de la persona.
     * La función getFotografia es la encargada de obtener la fotografía de una persona específica, recibiendo como parámetro el id de la persona.
     * La función busca la fotografía de la persona con el id proporcionado y, si lo encuentra, devuelve la fotografía en formato JSON junto con un mensaje de éxito. Si no encuentra la fotografía, devuelve un mensaje de error indicando que no se encontró la fotografía.
     */
    public function getFotografia(int $ci)
    {
        try {
            // 1. Obtener SÓLO la columna 'imagen' para el ID específico
            $persona = Personas::where('id_persona', $ci)// Filtrar solo la persona con el ID especificado
                ->select('foto')// Seleccionar solo la columna 'foto'
                ->first(); // Obtener la persona con el ID especificado

            // 2. Verificar si el producto existe y si tiene imagen
            if (! $persona || empty($persona->foto)) {
                // Devolver una respuesta HTTP 404 (Not Found)
                return response()->json(['error' => 'Fotografía no encontrada para el ID: '.$ci], 404);
            }

            $fotoBinaria = $persona->foto; // Obtener la fotografía de la persona

            // 3. Determinar el MIME type
            $mime = 'image/jpeg'; // MIME type por defecto

            // Intenta determinar el MIME type si el ambiente lo permite
            if (extension_loaded('fileinfo')) {// Si el ambiente lo permite
                $finfo = finfo_open(FILEINFO_MIME_TYPE); // Abrir el objeto finfo
                $detectedMime = finfo_buffer($finfo, $fotoBinaria); // Detectar el MIME type
                finfo_close($finfo); // Cerrar el objeto finfo

                if ($detectedMime && strpos($detectedMime, 'foto') === 0) {// Si se detectó un MIME type válido
                    $mime = $detectedMime; // Asignar el MIME type detectado
                }
            }

            // 4. Devolver la imagen como una respuesta binaria (STREAM)
            return Response::make($fotoBinaria, 200)// Devolver la imagen como una respuesta binaria (STREAM)
                ->header('Content-Type', $mime)// Asignar el MIME type
                ->header('Content-Disposition', 'inline; filename="foto_'.$ci.'"'); // Asignar el nombre del archivo
        } catch (\Throwable $e) {
            // Log::error('Error en getFotografia DController: ' . $e->getMessage()); // Opcional
            return response()->json(['error' => 'Error al obtener la imagen: '.$e->getMessage()], 500);
        }
    }

    /**
     * Función para mostrar una persona específica, recibiendo como parámetro el id de la persona.
     * La función show es la encargada de mostrar una persona específica, recibiendo como parámetro el id de la persona.
     * La función busca la persona con el id proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. Si no encuentra la persona, devuelve un mensaje de error indicando que no se encontró la persona.
     */
    public function show(string $id)
    {
        try {
            $data = Personas::select('personas.*')// Seleccionar todos los campos de la tabla 'personas'
                ->where('personas.id_persona', $id)// Filtrar solo la persona con el ID especificado
                ->paginate(20); // Paginar los resultados
            if ($data->isEmpty()) {// Si no hay datos, devolver un mensaje de error
                return response()->json(['error' => 'No se encontraron datos para el ID especificado'], 404);
            }

            // Convertir los campos a UTF-8 válido para cada página
            $data->getCollection()->transform(function ($item) {// Iterar sobre cada elemento de la colección
                $attributes = $item->getAttributes(); // Obtener los atributos del elemento

                foreach ($attributes as $key => $value) {// Iterar sobre cada clave-valor del array
                    if (in_array($key, ['foto']) && ! empty($value)) {// Si el valor es una cadena de caracteres

                        $attributes[$key] = base64_encode($value); // Convertir la cadena de caracteres a base64
                    } elseif (is_string($value) && ! in_array($key, ['foto'])) {// Si el valor es una cadena de caracteres
                        $attributes[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8'); // Convertir la cadena de caracteres a UTF-8
                    }
                }

                return $attributes; // Devolver los atributos del elemento transformados
            });

            // Retornar la respuesta JSON con los metadatos de paginación

            return response()->json([
                'data' => $data->items(),
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'last_page' => $data->lastPage(),
            ]);
            // Si ocurre algún error, devolver un mensaje de error en formato JSON
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al codificar los datos a JSON: '.$e->getMessage()], 500);
        }
    }

    /**
     * Función para actualizar los datos de una persona específica, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario y el id del registro.
     * La función update es la encargada de actualizar los datos de una persona específica, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario y el id del registro. 
     * La función busca el persona con el id proporcionado y, si lo encuentra, actualiza los datos enviados por el formulario y guarda los cambios en la base de datos. Si no encuentra el persona, devuelve un mensaje de error indicando que no se encontró el persona.
     */
    public function update(Request $request, string $id)
    {
        // Obtener el objeto Persona con el id proporcionado
        $res = Personas::find($id);
        // Si el objeto existe, actualizar los datos enviados por el formulario y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {// Si el objeto existe
            $res->cedula = $request->cedula;// Actualizar el cedula de la persona
            $res->nombres = $request->nombres;// Actualizar los nombres de la persona
            $res->apellidos = $request->apellidos;// Actualizar los apellidos de la persona
            $res->fecha_nacimiento = $request->fecha_nacimiento;// Actualizar la fecha de nacimiento de la persona
            $res->direccion = $request->direccion;// Actualizar la dirección de la persona
            $res->telefono = $request->telefono;// Actualizar el teléfono de la persona
            $res->correo = $request->correo;// Actualizar el correo electrónico de la persona
            $res->sexo = $request->sexo;// Actualizar el sexo de la persona
            if (! empty($request->foto)) {// Si hay una foto enviada
                $res->foto = base64_decode($request->foto);// Decodificar la foto enviada
            }
            $res->estado = $request->estado;// Actualizar el estado de la persona
            // Guardar los cambios en la base de datos
            if ($res->save()) {// Guardar los cambios en la base de datos   
                $data = $res->toArray();// Convertir el objeto a un array para devolverlo en formato JSON
                if (! empty($res->foto)) {// Si la foto existe
                    $data['foto'] = base64_encode($res->foto);// Convertir la foto a base64
                }
                // Devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
                return response()->json([
                    'data' => $data,
                    'mensaje' => 'Actualizado con Éxito!!',
                ]);
            } else {
                // Si ocurre algún error, devolver un mensaje de error en formato JSON
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error al Actualizar',
                ]);
            }
        } else {
            // Si el objeto no existe, devolver un mensaje de error en formato JSON
            return response()->json([
                'error' => true,
                'mensaje' => "La Persona con id: $id no Existe",
            ]);
        }
    }

    /**
     * Función para eliminar una persona específica, recibiendo como parámetro el id del registro.
     * La función destroy es la encargada de eliminar una persona específica, recibiendo como parámetro el id del registro. 
     * La función busca la persona con el id proporcionado y, si lo encuentra, inhabilita la persona y guarda los cambios en la base de datos. 
     * Luego, devuelve los datos actualizados en formato JSON, incluyendo un mensaje de éxito.      
     * Si no encuentra la persona, devuelve un mensaje de error indicando que no se encontró la persona.
     */
    public function destroy(string $id)
    {
        // Obtener el objeto Persona con el id proporcionado
        $res = Personas::find($id);
        // Si el objeto existe, inhabilitar la persona y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {
            $res->estado = 0;// Inhabilitar la persona
            $res->save();// Guardar los cambios en la base de datos
            $data = $res->toArray();// Convertir el objeto a un array para devolverlo en formato JSON
            if ($data) {// Si el objeto existe
                if (! empty($res->foto)) {// Si la foto existe
                    $data['foto'] = base64_encode($res->foto);// Convertir la foto a base64
                }
                // Devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
                return response()->json([
                    'data' => $data,
                    'mensaje' => 'Inhabilitado con Éxito!!',
                ]);
            } else {
                if (! empty($res->foto)) {// Si la foto existe
                    $data['foto'] = base64_encode($res->foto);// Convertir la foto a base64
                }
                // Devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
                return response()->json([
                    'data' => $data,
                    'mensaje' => 'La persona no existe (puede que ya la haya eliminado)',
                ]);
            }
        } else {
            // Si el objeto no existe, devolver un mensaje de error en formato JSON
            return response()->json([
                'error' => true,
                'mensaje' => "La Persona con id: $id no Existe",
            ]);
        }
    }
    /**
     * Función para habilitar una persona específica, recibiendo como parámetro el id del registro.     
     * La función habilitar es la encargada de habilitar una persona específica, recibiendo como parámetro el id del registro. 
     * La función busca la persona con el id proporcionado y, si lo encuentra, habilita la persona y guarda los cambios en la base de datos. 
     * Luego, devuelve los datos actualizados en formato JSON, incluyendo un mensaje de éxito.      
     * Si no encuentra la persona, devuelve un mensaje de error indicando que no se encontró la persona.
     */
    public function habilitar(string $id)
    {
        // Obtener el objeto Persona con el id proporcionado
        $res = Personas::find($id);
        // Si el objeto existe, habilitar la persona y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {
            $res->estado = 1;// Habilitar la persona
            $res->save();// Guardar los cambios en la base de datos
            $data = $res->toArray();// Convertir el objeto a un array para devolverlo en formato JSON
            if ($data) {// Si el objeto existe
                if (! empty($res->foto)) {// Si la foto existe
                    $data['foto'] = base64_encode($res->foto);// Convertir la foto a base64
                }
                // Devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
                return response()->json([
                    'data' => $data,
                    'mensaje' => 'Habilitado con Éxito!!',
                ]);
            } else {
                if (! empty($res->foto)) {// Si la foto existe
                    $data['foto'] = base64_encode($res->foto);// Convertir la foto a base64
                }
                // Devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
                return response()->json([
                    'data' => $data,
                    'mensaje' => 'La persona no existe (puede que ya la haya eliminado)',
                ]);
            }
        } else {
            // Si el objeto no existe, devolver un mensaje de error en formato JSON
            return response()->json([
                'error' => true,
                'mensaje' => "La Persona con id: $id no Existe",
            ]);
        }
    }
}
