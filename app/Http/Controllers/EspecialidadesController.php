<?php

namespace App\Http\Controllers;
//Importación de clases necesarias para el controlador EspecialidadesController
use App\Models\Especialidades;//Importación de la clase Especialidades
use Illuminate\Http\Request;//Importación de la clase Request para manejar las solicitudes HTTP

//Clase EspecialidadesController que representa un controlador en la aplicación para manejar las operaciones relacionadas con las especialidades
class EspecialidadesController extends Controller
{
    /**
     * Función que muestra una lista de especialidades, las cuales pueden ser filtradas por página y por búsqueda.
     * La función index es la encargada de mostrar una lista de especialidades, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
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
            $query = Especialidades::select('especialidades.*');
            //Si hay una consulta de búsqueda, aplicarla a los campos relevantes
            if (! empty($searchQuery)) {
                //Crear una consulta de búsqueda para cada campo relevante
                $query->where(function ($q) use ($searchQuery) {
                    //Aplicar la consulta de búsqueda a cada campo relevante, en este caso, solo a nombre de nivel académico
                    $q->where('especialidades.nombre', 'LIKE', "%{$searchQuery}%");
                });
            }
            //Obtener los datos paginados
            $data = $query->paginate($perPage);
            //Si no hay datos, devolver un mensaje de error
            if ($data->isEmpty()) {
                return response()->json(['data' => [], 'message' => 'No se encontraron datos'], 200);
            }
            //Transformar los datos a UTF-8 para evitar problemas de codificación al convertir a JSON
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
            return response()->json(['error' => 'Error al codificar los datos a JSON: ' . $e->getMessage()], 500);
        }
    }
   
    /**
     * Función para insertar nuevas especialidades en la base de datos, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     * La función store es la encargada de insertar nuevas especialidades en la base de datos, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario. 
     * Antes de insertar las nuevas especialidades, se realiza una validación para asegurar que no existan conflictos entre niveles académicos, especialidades y periodos lectivos. Si se intenta insertar un nivel académico que ya existe, se devuelve un mensaje de error indicando el conflicto. Si se intenta insertar una especialidad que ya existe, se devuelve un mensaje de error indicando el conflicto. Si se intenta insertar un periodo lectivo que ya existe, se devuelve un mensaje de error indicando el conflicto. Si la validación pasa sin problemas, se procede a insertar las nuevas especialidades y se devuelve un mensaje de éxito junto con los datos del nuevo registro.
     */
    public function store(Request $request)
    {
        //Obtener los datos enviados por el formulario
        $inputs = $request->input();
        //Crear el objeto Especialidades con los datos enviados
        $res = Especialidades::create($inputs);
        //Devolver los datos creados en formato JSON, incluyendo un mensaje de éxito
        return response()->json([
            'data' => $res,
            'mensaje' => "Agregado con Éxito!!",
        ]);
    }
    /**
     * 
     * Función para mostrar un especialidad específica, recibiendo como parámetro el id del registro.
     * La función show es la encargada de mostrar un especialidad específica, recibiendo como parámetro el id del registro. 
     * La función busca el especialidad con el id proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. Si no encuentra el especialidad, devuelve un mensaje de error indicando que no se encontró el especialidad.
     */
    public function show(string $id)
    {
        //Obtener el objeto Especialidades con el id proporcionado
        $res = Especialidades::find($id);
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
                'mensaje' => "La Especialidad con id: $id no Existe",
            ]);
        }
    }
    /**
     * Función para obtener las especialidades habilitadas, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     * La función getActivados es la encargada de obtener las especialidades habilitadas, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     */
    public function getActivados(Request $request){
        try {
            $especialidades = Especialidades::select('especialidades.*')//Seleccionar todos los campos de la tabla 'especialidades'
                ->where('estado', 1)//Filtrar solo las especialidades activas
                ->get();//Obtener los datos de la consulta
            //Devolver los datos en formato JSON
            return response()->json([
                'status' => true,
                'data' => $especialidades,
            ]);
        } catch (\Exception $e) {
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
            return response()->json(['error' => 'Error al codificar los datos a JSON: '.$e->getMessage()], 500);
        }
    }
    /**
     * Función para actualizar los datos de un especialidad específico, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario y el id del registro.
     * La función update es la encargada de actualizar los datos de un especialidad específico, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario y el id del registro. 
     * La función busca el especialidad con el id proporcionado y, si lo encuentra, actualiza los datos enviados por el formulario y guarda los cambios en la base de datos. Si no encuentra el especialidad, devuelve un mensaje de error indicando que no se encontró el especialidad.
     */
    public function update(Request $request, string $id)
    {
        //Obtener el objeto Especialidades con el id proporcionado
        $res = Especialidades::find($id);
        //Si el objeto existe, actualizar los datos enviados por el formulario y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {
            $res->nombre = $request->nombre;//Actualizar el nombre de la especialidad
            $res->estado = $request->estado;//Actualizar el estado de la especialidad
            //Guardar los cambios en la base de datos
            if ($res->save()) {//Guardar los cambios en la base de datos
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
                'mensaje' => "La Especialidad con id: $id no Existe",
            ]);
        }
    }
    /**
     * Función para eliminar una especialidad específica, recibiendo como parámetro el id del registro.
     * La función destroy es la encargada de eliminar una especialidad específica, recibiendo como parámetro el id del registro. 
     * La función busca el especialidad con el id proporcionado y, si lo encuentra, inhabilita la especialidad y guarda los cambios en la base de datos. 
     * Luego, devuelve los datos actualizados en formato JSON, incluyendo un mensaje de éxito. 
     * Si no encuentra el especialidad, devuelve un mensaje de error indicando que no se encontró el especialidad.
     */
    public function destroy(string $id)
    {
        //Obtener el objeto Especialidades con el id proporcionado
        $res = Especialidades::find($id);
        //Si el objeto existe, inhabilitar la especialidad y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {
            $res->estado = 0;//Inhabilitar especialidad
            $res->save();//Guardar los cambios en la base de datos
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
                    'mensaje' => "La Especialidad no existe (puede que ya la haya eliminado)",
                ]);
            }
        } else {     
            //Si el objeto no existe, devolver un mensaje de error en formato JSON
            return response()->json([
                'error' => true,
                'mensaje' => "La Especialidad con id: $id no Existe",
            ]);
        }
    }
    /**
     * Función para habilitar una especialidad específica, recibiendo como parámetro el id del registro.
     * La función habilitar es la encargada de habilitar una especialidad específica, recibiendo como parámetro el id del registro. 
     * La función busca el especialidad con el id proporcionado y, si lo encuentra, habilita la especialidad y guarda los cambios en la base de datos. 
     * Luego, devuelve los datos actualizados en formato JSON, incluyendo un mensaje de éxito. 
     * Si no encuentra el especialidad, devuelve un mensaje de error indicando que no se encontró el especialidad.
     */
    public function habilitar(string $id)
    {
        //Obtener el objeto Especialidades con el id proporcionado
        $res = Especialidades::find($id);
        //Si el objeto existe, habilitar la especialidad y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {
            $res->estado = 1;//Habilitar la especialidad
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
                    'mensaje' => "La Especialidad no existe (puede que ya la haya eliminado)",
                ]);
            }
        } else {
            //Si el objeto no existe, devolver un mensaje de error en formato JSON
            return response()->json([
                'error' => true,
                'mensaje' => "La Especialidad con id: $id no Existe",
            ]);
        }
    }
}
