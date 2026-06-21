<?php

namespace App\Http\Controllers;
//Importación de clases necesarias para el controlador AsignaturasController
use App\Models\Asignaturas;//Importación de la clase Asignaturas
use Illuminate\Http\Request;//Importación de la clase Request para manejar las solicitudes HTTP

//Clase AsignaturasController que representa un controlador en la aplicación para manejar las operaciones relacionadas con las asignaturas
class AsignaturasController extends Controller
{
    /**
     * Función que muestra una lista de asignaturas, las cuales pueden ser filtradas por página y por búsqueda.
     */
    public function index(Request $request)//La función index es la encargada de mostrar una lista de asignaturas, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
    {
        try {
            //Definir el número de elementos por página, con un máximo de 50
            $perPage = $request->input('per_page', 10);
            //Limitar el número de elementos por página a 20
            $perPage = min($perPage, 20);
            //Obtener la consulta de búsqueda
            $searchQuery = $request->input('search_query');
            //Crear la consulta base
            $query = Asignaturas::select('asignaturas.*');//Seleccionar todos los campos de la tabla asignaturas
            //Si hay una consulta de búsqueda, aplicarla a los campos relevantes
            if (! empty($searchQuery)) {
                //Crear una consulta de búsqueda para cada campo relevante
                $query->where(function ($q) use ($searchQuery) {
                    //Aplicar la consulta de búsqueda a cada campo relevante, en este caso, solo a nombre de nivel académico
                    $q->where('asignaturas.nombre', 'LIKE', "%{$searchQuery}%");
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
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al codificar los datos a JSON: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Función que devuelve una lista de asignaturas habilitadas.
     */
    public function getActivados(){//La función getActivados es la encargada de devolver una lista de asignaturas habilitadas, no recibiendo parámetros.
        try {
            //Obtener una lista de asignaturas habilitadas, seleccionando solo los campos relevantes y aplicando un filtro para obtener solo las asignaturas con estado 1 (habilitadas)
            $asignaturas = Asignaturas::select('asignaturas.*')//Seleccionar todos los campos de la tabla asignaturas
                ->where('estado', 1)//Filtrar solo las asignaturas con estado 1 (habilitadas)
                ->get();//Obtener los datos de la consulta
            //Devolver los datos en formato JSON, incluyendo un mensaje de éxito
            return response()->json([
                'status' => true,//Indicar que la solicitud fue exitosa
                'data' => $asignaturas,//Enviar los datos devueltos por la consulta
            ]);
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al codificar los datos a JSON: '.$e->getMessage()], 500);
        }
    }
   
    /**
     * Función para insertar nuevas asignaturas en la base de datos, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     */
    public function store(Request $request)
    {
        //Obtener los datos enviados por el formulario
        $inputs = $request->input();
        //Crear el objeto Asignaturas con los datos enviados
        $res = Asignaturas::create($inputs);
        //Devolver los datos creados en formato JSON, incluyendo un mensaje de éxito
        return response()->json([
            'data' => $res,//Enviar los datos devueltos por la consulta
            'mensaje' => "Agregado con Éxito!!",//Enviar un mensaje de éxito
        ]);
    }
    /**
     * Función para mostrar los detalles de una asignatura específica, recibiendo como parámetros el id de la asignatura.
     */
    public function show(string $id)
    {
        //Obtener el objeto Asignaturas con el id proporcionado
        $res = Asignaturas::find($id);
        //Si el objeto existe, devolver los datos en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {
            return response()->json([
                'data' => $res,//Enviar los datos devueltos por la consulta
                'mensaje' => "Encontrado con Éxito!!",//Enviar un mensaje de éxito
            ]);
        } else {
            //Si el objeto no existe, devolver un mensaje de error en formato JSON
            return response()->json([
                'error' => true,//Indicar que la solicitud no tuvo éxito
                'mensaje' => "La Asignatura con id: $id no Existe",//Enviar un mensaje de error indicando que la asignatura no existe
            ]);
        }
    }
    /**
     * Función para actualizar los datos de una asignatura en la base de datos, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario y el id de la asignatura.
     */
    public function update(Request $request, string $id)
    {
        //Obtener el objeto Asignaturas con el id proporcionado
        $res = Asignaturas::find($id);
        //Si el objeto existe, actualizar los datos enviados por el formulario y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {//Si el objeto existe
            $res->nombre = $request->nombre;//Actualizar el nombre de la asignatura
            $res->estado = $request->estado;//Actualizar el estado de la asignatura
            //Guardar los cambios en la base de datos
            if ($res->save()) {
                //Devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
                return response()->json([
                    'data' => $res,//Enviar los datos devueltos por la consulta
                    'mensaje' => "Actualizado con Éxito!!",//Enviar un mensaje de éxito
                ]);
            } else {
                //Si ocurre algún error, devolver un mensaje de error en formato JSON
                return response()->json([
                    'error' => true,//Indicar que la solicitud no tuvo éxito
                    'mensaje' => "Error al Actualizar",//Enviar un mensaje de error
                ]);
            }
        } else {
            //Si el objeto no existe, devolver un mensaje de error en formato JSON
            return response()->json([
                'error' => true,//Indicar que la solicitud no tuvo éxito
                'mensaje' => "La Asignatura con id: $id no Existe",//Enviar un mensaje de error
            ]);
        }
    }
    /**
     * Función para eliminar una asignatura específica de la base de datos, recibiendo como parámetros el id de la asignatura.
     * En este caso, en lugar de eliminar físicamente el registro de la base de datos, se inhabilita la asignatura estableciendo su estado a 0, lo que permite mantener un historial de las asignaturas eliminadas y evitar problemas de integridad referencial en caso de que existan relaciones con otras tablas.
     */
    public function destroy(string $id)
    {
        //Obtener el objeto Asignaturas con el id proporcionado
        $res = Asignaturas::find($id);
        //Si el objeto existe, inhabilitar el nivel académico y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {//Si el objeto existe
            $res->estado = 0;//Establecer el estado de la asignatura a 0 (inhabilitada)
            $res->save();//Guardar los cambios en la base de datos
            $data = $res->toArray();//Convertir el objeto a un array para devolverlo en formato JSON
            if ($data) {
                //Devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
                return response()->json([
                    'data' => $data,//Enviar los datos devueltos por la consulta
                    'mensaje' => "Inhabilitado con Éxito!!",//Enviar un mensaje de éxito
                ]);
            } else {
                //Si ocurre algún error, devolver un mensaje de error en formato JSON
                return response()->json([
                    'data' => $data,//Enviar los datos devueltos por la consulta
                    'mensaje' => "La Asignatura no existe (puede que ya la haya eliminado)",//Enviar un mensaje de error indicando que la asignatura no existe
                ]);
            }
        } else {     
            //Si el objeto no existe, devolver un mensaje de error en formato JSON
            return response()->json([
                'error' => true,//Indicar que la solicitud no tuvo éxito
                'mensaje' => "La Asignatura con id: $id no Existe",//Enviar un mensaje de error indicando que la asignatura no existe
            ]);
        }
    }
    /** 
     * Función para habilitar una asignatura específica en la base de datos, recibiendo como parámetros el id de la asignatura.
     */
    public function habilitar(string $id)
    {
        //Obtener el objeto Asignaturas con el id proporcionado
        $res = Asignaturas::find($id);
        //Si el objeto existe, habilitar el nivel académico y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {//Si el objeto existe
            $res->estado = 1;//Establecer el estado de la asignatura a 1 (habilitada)
            $res->save();//Guardar los cambios en la base de datos
            $data = $res->toArray();//Convertir el objeto a un array para devolverlo en formato JSON
            if ($data) {
                //Devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
                return response()->json([
                    'data' => $data,//Enviar los datos devueltos por la consulta
                    'mensaje' => "Habilitado con Éxito!!",//Enviar un mensaje de éxito
                ]);
            } else {
                //Si ocurre algún error, devolver un mensaje de error en formato JSON
                return response()->json([
                    'data' => $data,//Enviar los datos devueltos por la consulta
                    'mensaje' => "La Asignatura no existe (puede que ya la haya eliminado)",//Enviar un mensaje de error indicando que la asignatura no existe
                ]);
            }
        } else {
            //Si el objeto no existe, devolver un mensaje de error en formato JSON
            return response()->json([
                'error' => true,//Indicar que la solicitud no tuvo éxito
                'mensaje' => "La Asignatura con id: $id no Existe",//Enviar un mensaje de error indicando que la asignatura no existe
            ]);
        }
    }
}
