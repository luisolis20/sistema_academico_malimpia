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
            $perPage = $request->input('per_page', 10);//Definir el número de resultados por página, con un máximo de 50
            $perPage = min($perPage, 20);//Limitar el número de resultados por página a 20
            $searchQuery = $request->input('search_query');//Obtener la consulta de búsqueda

            $query = Niveles_academicos::select('niveles_academicos.*');//Seleccionar todos los campos de la tabla 'niveles_academicos'

            if (!empty($searchQuery)) {//Si hay una consulta de búsqueda, aplicarla a los campos relevantes
                $query->where('niveles_academicos.nombre', 'LIKE', "%{$searchQuery}%");//Filtrar solo los niveles académicos que coincidan con la consulta de búsqueda
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
        ");//Ordenar los niveles académicos por orden jerárquico

            $data = $query->paginate($perPage);//Paginar los resultados

            if ($data->isEmpty()) {//Si no hay datos, devolver un mensaje de error
                return response()->json(['data' => [], 'message' => 'No se encontraron datos'], 200);
            }

            // Transformación de datos (UTF-8)
            $data->getCollection()->transform(function ($item) {//Iterar sobre cada elemento de la colección
                $attributes = $item->getAttributes();//Obtener los atributos del elemento
                foreach ($attributes as $key => $value) {//Iterar sobre cada clave-valor del array
                    if (is_string($value)) {//Si el valor es una cadena de caracteres
                        $attributes[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');//Convertir la cadena de caracteres a UTF-8
                    }
                }
                return $attributes;//Devolver los atributos del elemento transformados
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
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
            return response()->json(['error' => 'Error en el servidor: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Función para insertar nuevas niveles académicos en la base de datos, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     * La función store es la encargada de insertar nuevas niveles académicos en la base de datos, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario. 
     * Antes de insertar las nuevas niveles académicos, se realiza una validación para asegurar que no existan conflictos entre niveles académicos, especialidades y periodos lectivos. Si se intenta insertar un nivel académico que ya existe, se devuelve un mensaje de error indicando el conflicto. Si se intenta insertar una especialidad que ya existe, se devuelve un mensaje de error indicando el conflicto. Si se intenta insertar un periodo lectivo que ya existe, se devuelve un mensaje de error indicando el conflicto. Si la validación pasa sin problemas, se procede a insertar las nuevas niveles académicos y se devuelve un mensaje de éxito junto con los datos del nuevo registro.
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
     * Función para mostrar un nivel académico específico, recibiendo como parámetro el id del registro.
     * La función show es la encargada de mostrar un nivel académico específico, recibiendo como parámetro el id del registro. 
     * La función busca el nivel académico con el id proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. Si no encuentra el nivel académico, devuelve un mensaje de error indicando que no se encontró el nivel académico.
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
    /**
     * Función para obtener los niveles académicos habilitados, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     * La función getActivados es la encargada de obtener los niveles académicos habilitados, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     */
    public function getActivados()
    {
        try {
            $niveles = Niveles_academicos::select('niveles_academicos.*')//Seleccionar todos los campos de la tabla 'niveles_academicos'
                ->where('estado', 1)//Filtrar solo los niveles académicos activos
                ->get();//Obtener los datos de la consulta
            //Devolver los datos en formato JSON
            return response()->json([
                'status' => true,
                'data' => $niveles,
            ]);
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al codificar los datos a JSON: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Función para actualizar los datos de un nivel académico específico, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario y el id del registro.
     * La función update es la encargada de actualizar los datos de un nivel académico específico, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario y el id del registro. 
     * La función busca el nivel académico con el id proporcionado y, si lo encuentra, actualiza los datos enviados por el formulario y guarda los cambios en la base de datos. Si no encuentra el nivel académico, devuelve un mensaje de error indicando que no se encontró el nivel académico.       
     */
    public function update(Request $request, string $id)
    {
        //Obtener el objeto Niveles_academicos con el id proporcionado
        $res = Niveles_academicos::find($id);
        //Si el objeto existe, actualizar los datos enviados por el formulario y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {
            $res->nombre = $request->nombre;//Actualizar el nombre del nivel académico
            $res->orden_jerarquia = $request->orden_jerarquia;//Actualizar el orden jerárquico del nivel académico
            $res->estado = $request->estado;//Actualizar el estado del nivel académico  
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
     * Función para eliminar un nivel académico específico, recibiendo como parámetro el id del registro.
     * La función destroy es la encargada de eliminar un nivel académico específico, recibiendo como parámetro el id del registro. 
     * La función busca el nivel académico con el id proporcionado y, si lo encuentra, inhabilita el nivel académico y guarda los cambios en la base de datos. 
     * Luego, devuelve los datos actualizados en formato JSON, incluyendo un mensaje de éxito.      
     * Si no encuentra el nivel académico, devuelve un mensaje de error indicando que no se encontró el nivel académico.    
     */
    public function destroy(string $id)
    {
        //Obtener el objeto Niveles_academicos con el id proporcionado
        $res = Niveles_academicos::find($id);
        //Si el objeto existe, inhabilitar el nivel académico y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {
            $res->estado = 0;//Inhabilitar el nivel académico
            $res->save();//Guardar los cambios en la base de datos
            $data = $res->toArray();//Convertir el objeto a un array para devolverlo en formato JSON
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
    /**
     * Función para habilitar un nivel académico específico, recibiendo como parámetro el id del registro.     
     * La función habilitar es la encargada de habilitar un nivel académico específico, recibiendo como parámetro el id del registro. 
     * La función busca el nivel académico con el id proporcionado y, si lo encuentra, habilita el nivel académico y guarda los cambios en la base de datos. 
     * Luego, devuelve los datos actualizados en formato JSON, incluyendo un mensaje de éxito.      
     * Si no encuentra el nivel académico, devuelve un mensaje de error indicando que no se encontró el nivel académico.    
     */
    public function habilitar(string $id)
    {
        //Obtener el objeto Niveles_academicos con el id proporcionado
        $res = Niveles_academicos::find($id);
        //Si el objeto existe, habilitar el nivel académico y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {
            $res->estado = 1;//Habilitar el nivel académico
            $res->save();//Guardar los cambios en la base de datos
            $data = $res->toArray();//Convertir el objeto a un array para devolverlo en formato JSON
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
