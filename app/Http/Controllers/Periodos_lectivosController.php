<?php

namespace App\Http\Controllers;
//Importación de clases necesarias para el controlador Periodos_lectivosController
use App\Models\Periodos_lectivos;//Importación de la clase Periodos_lectivos
use Illuminate\Http\Request;//Importación de la clase Request para manejar las solicitudes HTTP

//Clase Periodos_lectivosController que representa un controlador en la aplicación para manejar las operaciones relacionadas con los periodos lectivos
class Periodos_lectivosController extends Controller
{
    /**
     * Función que muestra una lista de periodos lectivos, las cuales pueden ser filtradas por página y por búsqueda.
     * La función index es la encargada de mostrar una lista de periodos lectivos, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.  
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
            $query = Periodos_lectivos::select('periodos_lectivos.*');
            // Si hay una consulta de búsqueda, aplicarla a los campos relevantes
            if (! empty($searchQuery)) {
                // Crear una consulta de búsqueda para cada campo relevante
                $query->where(function ($q) use ($searchQuery) {
                    // Aplicar la consulta de búsqueda a cada campo relevante, en este caso, solo a nombre de nivel académico
                    $q->where('periodos_lectivos.nombre', 'LIKE', "%{$searchQuery}%");
                });
            }
            // Obtener los datos paginados
            $data = $query->paginate($perPage);
            // Si no hay datos, devolver un mensaje de error
            if ($data->isEmpty()) {
                return response()->json(['data' => [], 'message' => 'No se encontraron datos'], 200);
            }
            // Transformar los datos a UTF-8 para evitar problemas de codificación al convertir a JSON
            $data->getCollection()->transform(function ($item) {//Iterar sobre cada elemento de la colección
                $attributes = $item->getAttributes();//Obtener los atributos del elemento
                foreach ($attributes as $key => $value) {//Iterar sobre cada clave-valor del array
                    if (is_string($value)) {//Si el valor es una cadena de caracteres
                        $attributes[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');//Convertir la cadena de caracteres a UTF-8
                    }
                }

                return $attributes;//Devolver los atributos del elemento transformados  
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
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al codificar los datos a JSON: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Función para insertar nuevos periodos lectivos en la base de datos, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     * La función store es la encargada de insertar nuevos periodos lectivos en la base de datos, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario. 
     * Antes de insertar los nuevos periodos lectivos, se realiza una validación para asegurar que no existan conflictos entre periodos lectivos, especialidades y periodos lectivos. Si se intenta insertar un periodo lectivo que ya existe, se devuelve un mensaje de error indicando el conflicto. Si se intenta insertar una especialidad que ya existe, se devuelve un mensaje de error indicando el conflicto. Si se intenta insertar un periodo lectivo que ya existe, se devuelve un mensaje de error indicando el conflicto. Si la validación pasa sin problemas, se procede a insertar los nuevos periodos lectivos y se devuelve un mensaje de éxito junto con los datos del nuevo registro.
     */
    public function store(Request $request)
    {
        $inputs = $request->input();//Obtener los datos enviados por el formulario

        // 1. Verificar si ya existe un periodo activo
        $existeActivo = Periodos_lectivos::where('estado_activo', 1)->exists();

        if ($existeActivo) {
            // Si ya hay uno activo, el nuevo se guarda inhabilitado por seguridad
            $inputs['estado_activo'] = 0;//Inhabilitar el periodo lectivo
            $inputs['matriculas_abiertas'] = 0;//Inhabilitar las matriculas del periodo lectivo
        }

        $res = Periodos_lectivos::create($inputs);//Crear el periodo lectivo con los datos enviados
        //Devolver los datos creados en formato JSON, incluyendo un mensaje de éxito
        return response()->json([
            'data' => $res,
            'mensaje' => $existeActivo
                ? 'Agregado (Inhabilitado porque ya existe un periodo activo)'
                : 'Agregado con Éxito!!',
        ]);
    }

    /**
     * Función para mostrar un periodo lectivo específico, recibiendo como parámetro el id del registro.
     * La función show es la encargada de mostrar un periodo lectivo específico, recibiendo como parámetro el id del registro. 
     * La función busca el periodo lectivo con el id proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. Si no encuentra el periodo lectivo, devuelve un mensaje de error indicando que no se encontró el periodo lectivo.
     */
    public function show(string $id)
    {
        // Obtener el objeto Periodos_lectivos con el id proporcionado
        $res = Periodos_lectivos::find($id);
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
                'mensaje' => "El Periodo Lectivo con id: $id no Existe",
            ]);
        }
    }

    /**
     * Función para obtener los periodos lectivos habilitados, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     * La función getActivados es la encargada de obtener los periodos lectivos habilitados, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     */
    public function getActivados()
    {
        try {
            $periodos = Periodos_lectivos::select('periodos_lectivos.*')//Seleccionar todos los campos de la tabla 'periodos_lectivos'
                ->where('estado_activo', 1)//Filtrar solo los periodos lectivos activos
                ->get();//Obtener los datos de la consulta
            //Devolver los datos en formato JSON
            return response()->json([
                'status' => true,
                'data' => $periodos,
            ]);
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al codificar los datos a JSON: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Función para actualizar los datos de un periodo lectivo específico, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario y el id del registro.
     * La función update es la encargada de actualizar los datos de un periodo lectivo específico, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario y el id del registro.
     */
    public function update(Request $request, string $id)
    {
        $res = Periodos_lectivos::find($id);//Obtener el objeto Periodos_lectivos con el id proporcionado

        if (isset($res)) {//Si el objeto existe
            // 1. Verificar si existe OTRO periodo activo (excluyendo el actual)
            $otroPeriodoActivo = Periodos_lectivos::where('estado_activo', 1)//Filtrar solo los periodos lectivos activos
                ->where('id_periodo', '!=', $id)//Filtrar solo los periodos lectivos que no sean el actual
                ->exists();//Verificar si existe otro periodo activo

            if ($otroPeriodoActivo) {//Si intentamos activar este pero ya hay otro, forzamos a 0
                // Si intentamos activar este pero ya hay otro, forzamos a 0
                $res->estado_activo = 0;//Inhabilitar el periodo lectivo
                $res->matriculas_abiertas = 0;//Inhabilitar las matriculas del periodo lectivo
            } else {
                // Si no hay conflictos, tomamos los valores del request normalmente
                $res->estado_activo = $request->estado_activo;//Actualizar el estado del periodo lectivo
                $res->matriculas_abiertas = $request->matriculas_abiertas;//Actualizar las matriculas del periodo lectivo
            }

            $res->nombre = $request->nombre;//Actualizar el nombre del periodo lectivo
            $res->fecha_inicio = $request->fecha_inicio;//Actualizar la fecha de inicio del periodo lectivo
            $res->fecha_fin = $request->fecha_fin;//Actualizar la fecha de fin del periodo lectivo

            if ($res->save()) {//Guardar los cambios en la base de datos
                return response()->json([//Devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
                    'data' => $res,
                    'mensaje' => $otroPeriodoActivo
                        ? 'Actualizado (Se mantuvo inhabilitado por conflicto de periodos activos)'
                        : 'Actualizado con Éxito!!',
                ]);
            }
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al Actualizar',
            ], 500);
        }
        //Si el objeto no existe, devolver un mensaje de error en formato JSON
        return response()->json([
            'error' => true,
            'mensaje' => "El Periodo Lectivo con id: $id no Existe",
        ], 404);
    }

    /**
     * Función para eliminar un periodo lectivo específico, recibiendo como parámetro el id del registro.
     * La función destroy es la encargada de eliminar un periodo lectivo específico, recibiendo como parámetro el id del registro. 
     * La función busca el periodo lectivo con el id proporcionado y, si lo encuentra, inhabilita el periodo lectivo y guarda los cambios en la base de datos. 
     * Luego, devuelve los datos actualizados en formato JSON, incluyendo un mensaje de éxito.
     * Si no encuentra el periodo lectivo, devuelve un mensaje de error indicando que no se encontró el periodo lectivo.
     */
    public function destroy(string $id)
    {
        // Obtener el objeto Periodos_lectivos con el id proporcionado
        $res = Periodos_lectivos::find($id);
        // Si el objeto existe, inhabilitar el nivel académico y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {
            $res->matriculas_abiertas = 0;//Inhabilitar las matriculas del periodo lectivo
            $res->estado_activo = 0;//Inhabilitar el periodo lectivo
            $res->save();//Guardar los cambios en la base de datos
            $data = $res->toArray();//Convertir el objeto a un array para devolverlo en formato JSON
            if ($data) {//Si el objeto existe
                // Devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
                return response()->json([
                    'data' => $data,
                    'mensaje' => 'Inhabilitado con Éxito!!',
                ]);
            } else {
                // Si ocurre algún error, devolver un mensaje de error en formato JSON
                return response()->json([
                    'data' => $data,
                    'mensaje' => 'El Periodo Lectivo no existe (puede que ya lo haya eliminado)',
                ]);
            }
        } else {
            // Si el objeto no existe, devolver un mensaje de error en formato JSON
            return response()->json([
                'error' => true,
                'mensaje' => "El Periodo Lectivo con id: $id no Existe",
            ]);
        }
    }
    /**
     * Función para habilitar un periodo lectivo específico, recibiendo como parámetro el id del registro.     
     * La función habilitar es la encargada de habilitar un periodo lectivo específico, recibiendo como parámetro el id del registro. 
     * La función busca el periodo lectivo con el id proporcionado y, si lo encuentra, habilita el periodo lectivo y guarda los cambios en la base de datos. 
     * Luego, devuelve los datos actualizados en formato JSON, incluyendo un mensaje de éxito.      
     * Si no encuentra el periodo lectivo, devuelve un mensaje de error indicando que no se encontró el periodo lectivo.
     */
    public function habilitar(string $id)
    {
        // Obtener el objeto Periodos_lectivos con el id proporcionado
        $res = Periodos_lectivos::find($id);
        // Si el objeto existe, habilitar el nivel académico y guardar los cambios, luego devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
        if (isset($res)) {//Si el objeto existe
            $res->matriculas_abiertas = 1;//Habilitar las matriculas del periodo lectivo
            $res->estado_activo = 1;//Habilitar el periodo lectivo
            $res->save();//Guardar los cambios en la base de datos
            $data = $res->toArray();//Convertir el objeto a un array para devolverlo en formato JSON
            if ($data) {//Si el objeto existe
                // Devolver los datos actualizados en formato JSON, incluyendo un mensaje de éxito
                return response()->json([
                    'data' => $data,
                    'mensaje' => 'Habilitado con Éxito!!',
                ]);
            } else {
                // Si ocurre algún error, devolver un mensaje de error en formato JSON
                return response()->json([
                    'data' => $data,
                    'mensaje' => 'El Periodo Lectivo no existe (puede que ya lo haya eliminado)',
                ]);
            }
        } else {
            // Si el objeto no existe, devolver un mensaje de error en formato JSON
            return response()->json([
                'error' => true,
                'mensaje' => "El Periodo Lectivo con id: $id no Existe",
            ]);
        }
    }
}
