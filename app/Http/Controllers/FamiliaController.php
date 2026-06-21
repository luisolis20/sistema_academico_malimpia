<?php

namespace App\Http\Controllers;
//Importación de clases necesarias para el controlador FamiliaController
use App\Models\Personas;//Importación de la clase Personas
use App\Models\Familia;//Importación de la clase Familia
use Illuminate\Http\Request;//Importación de la clase Request para manejar las solicitudes HTTP
use Illuminate\Support\Facades\DB;//Importación de la clase DB para acceder a las tablas de la base de datos

//Clase FamiliaController que representa un controlador en la aplicación para manejar las operaciones relacionadas con las familias

class FamiliaController extends Controller
{
    /**
     * Función que muestra una lista de familias, las cuales pueden ser filtradas por página y por búsqueda.
     * La función index es la encargada de mostrar una lista de familias, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     */
    public function index(Request $request)
    {
        try {
            $perPage = min($request->input('per_page', 10), 20);//Obtener el número de elementos por página, limitando el número de elementos por página a 20
            $searchQuery = $request->input('search_query');//Obtener la consulta de búsqueda

            // 1. Consulta Principal: Obtenemos SOLO a las personas (sin duplicados)
            $query = Personas::select(
                'personas.id_persona as personID',
                'personas.cedula',
                'personas.nombres',
                'personas.apellidos',
                'personas.foto',
                'personas.fecha_nacimiento',
                'personas.sexo',
                'usuarios.id_rol',  
                'roles.nombre as nombre_rol',
            )//Seleccionar todas las columnas de la tabla 'personas', de la tabla 'usuarios', de la tabla 'roles'
                ->join('usuarios', 'usuarios.id_persona', '=', 'personas.id_persona')//Unir la tabla 'usuarios' con la tabla 'personas' en base a la columna 'id_persona'
                ->join('roles', 'roles.id_rol', '=', 'usuarios.id_rol')//Unir la tabla 'roles' con la tabla 'usuarios' en base a la columna 'id_rol'
                ->whereRaw('TIMESTAMPDIFF(YEAR, personas.fecha_nacimiento, CURDATE()) >= 20')//Filtrar solo las personas con el año de nacimiento superior o igual a 20
                ->where('usuarios.estado', 1);//Filtrar solo las personas con el estado 1

            if (! empty($searchQuery)) {//Si hay una consulta de búsqueda, aplicarla a los campos relevantes
                $query->where(function ($q) use ($searchQuery) {//Aplicar la consulta de búsqueda a cada campo relevante
                    $q->where('personas.cedula', 'LIKE', "%{$searchQuery}%")//Filtrar solo las personas que coincidan con la consulta de búsqueda
                        ->orWhere('personas.nombres', 'LIKE', "%{$searchQuery}%")//Filtrar solo las personas que coincidan con la consulta de búsqueda
                        ->orWhere('personas.apellidos', 'LIKE', "%{$searchQuery}%");//Filtrar solo las personas que coincidan con la consulta de búsqueda
                });
            }

            // Paginamos limpio, sin filas duplicadas
            $data = $query->paginate($perPage);//Obtener los datos paginados
            //Si no hay datos, devolver un mensaje de error indicando que no se encontraron datos
            if ($data->isEmpty()) {
                return response()->json(['data' => [], 'message' => 'No se encontraron datos'], 200);
            }

            // 2. Extraemos los IDs de las personas de esta página para buscar a sus familiares
            $personIds = collect($data->items())->pluck('personID')->toArray();

            // 3. Buscamos los familiares de estas personas e incluimos sus datos personales (nombres, cédula)
            $familiaresRaw = DB::table('familia')//Seleccionar todas las columnas de la tabla 'familia'
                ->join('personas', 'familia.id_estudiante', '=', 'personas.id_persona')//Unir la tabla 'personas' con la tabla 'familia' en base a la columna 'id_persona'
                ->whereIn('familia.id_representante', $personIds)//Filtrar solo los familiares de las personas de la página
                ->select(
                    'familia.id_representante',
                    'familia.id_estudiante',
                    'familia.parentesco',
                    'personas.cedula',
                    'personas.nombres',
                    'personas.apellidos'
                )//Seleccionar todas las columnas de la tabla 'familia', de la tabla 'personas'
                ->get();//Obtener los datos de la consulta

            // Agrupamos la lista de familiares usando el ID del representante
            $familiaresAgrupados = $familiaresRaw->groupBy('id_representante');

            // 4. Transformamos la data para inyectar el arreglo de familiares a cada persona
            $data->getCollection()->transform(function ($item) use ($familiaresAgrupados) {//Iterar sobre cada elemento de la colección
                // Dependiendo de cómo devuelva paginate, extraemos los atributos
                $attributes = is_array($item) ? $item : $item->getAttributes();//Obtener los atributos del elemento

                // Procesamiento de fotos y codificación
                foreach ($attributes as $key => $value) {//Iterar sobre cada clave-valor del array
                    if ($key === 'foto' && ! empty($value)) {//Si el valor es una cadena de caracteres
                        $attributes[$key] = base64_encode($value);//Convertir la cadena de caracteres a base64
                    } elseif (is_string($value) && $key !== 'foto') {//Si el valor es una cadena de caracteres
                        $attributes[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');//Convertir la cadena de caracteres a UTF-8
                    }
                }

                // Inyectamos el arreglo de familiares. Si no tiene, se envía un arreglo vacío []
                $idPersona = $attributes['personID'];//Obtener el id de la persona
                $attributes['familiares'] = isset($familiaresAgrupados[$idPersona])//Si existe un arreglo de familiares
                    ? $familiaresAgrupados[$idPersona]->toArray()//Obtener el arreglo de familiares
                    : [];//Si no existe, devolver un arreglo vacío

                return $attributes;//Devolver los atributos del elemento transformados
            });
            //Devolver los datos paginados en formato JSON, incluyendo la información de paginación
            return response()->json([
                'data' => $data->items(),
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'per_page'     => $data->perPage(),
                    'total'        => $data->total(),
                    'last_page'    => $data->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Función para insertar nuevas familias en la base de datos, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     * La función store es la encargada de insertar nuevas familias en la base de datos, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario. 
     * Antes de insertar las nuevas familias, se realiza una validación para asegurar que no existan conflictos entre personas. Si se intenta insertar una persona que ya existe, se devuelve un mensaje de error indicando el conflicto. Si la validación pasa sin problemas, se procede a insertar las nuevas familias y se devuelve un mensaje de éxito junto con los datos del nuevo registro.
     */
    public function store(Request $request)
    {
        // 1. Validación de los datos entrantes
        $request->validate([
            'id_representante' => 'required|integer|exists:personas,id_persona',
            'familiares'       => 'required|array|min:1',
            'familiares.*.personID' => 'required|integer|exists:personas,id_persona',
            'familiares.*.parentesco' => 'required|string|max:50',
        ]);

        // 2. Iniciamos una transacción
        DB::beginTransaction();//Iniciar una transacción de base de datos

        try {
            $idRepresentante = $request->input('id_representante');//Obtener el id del representante
            $familiares = $request->input('familiares');//Obtener los datos de las familiares

            // Extraer todos los IDs de los familiares que vienen del Frontend
            $idsFamiliaresNuevos = collect($familiares)->pluck('personID')->toArray();//Obtener los IDs de los familiares nuevos

            // PASO CLAVE: Eliminar de la BD los familiares que ya no están en la lista del Frontend
            Familia::where('id_representante', $idRepresentante)//Filtrar solo las familias del representante especificado
                ->whereNotIn('id_estudiante', $idsFamiliaresNuevos)//Filtrar solo las familias que no coincidan con los IDs de los familiares nuevos
                ->delete();//Eliminar las familias

            // 3. Crear o actualizar los que sí vienen
            foreach ($familiares as $familiar) {//Iterar sobre cada familiar
                // Evitamos que una persona sea representante de sí misma
                if ($idRepresentante == $familiar['personID']) {//Si la persona es la misma que el representante
                    continue;//Saltar el proceso de creación o actualización
                }
                //Crear o actualizar la familia
                Familia::updateOrCreate(
                    [
                        'id_representante' => $idRepresentante,//Asignar el id del representante
                        'id_estudiante'    => $familiar['personID'],//Asignar el id de la persona
                    ],
                    [
                        'parentesco'       => $familiar['parentesco'],//Asignar el parentesco
                    ]
                );
            }

            DB::commit();//Confirmar la transacción de base de datos
            //Devolver un mensaje de éxito indicando que las familias se actualizaron exitosamente
            return response()->json([
                'success' => true,
                'message' => 'Familia guardada correctamente.',
            ], 200);
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
        } catch (\Exception $e) {
            DB::rollBack();//Cancelar la transacción de base de datos
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al asignar la familia.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Función para obtener los familiares de una persona específica, recibiendo como parámetro el id de la persona.
     * La función getFamiliaresByPersona es la encargada de obtener los familiares de una persona específica, recibiendo como parámetro el id de la persona. 
     * La función busca los familiares de la persona con el id proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. Si no encuentra los familiares de la persona, devuelve un mensaje de error indicando que no se encontró los familiares de la persona.
     */
    public function getFamiliaresByPersona(string $id_persona)
    {
        // Buscamos los familiares donde el ID enviado sea el representante
        $familiares = Familia::with(['familiapersona2']) // Cargamos la relación del familiar
            ->where('id_representante', $id_persona)//Filtrar solo los familiares del representante especificado
            ->get();//Obtener los datos de la consulta
        //Si no encuentra los familiares de la persona, devolver un mensaje de error indicando que no se encontró los familiares de la persona
        if ($familiares->isEmpty()) {//Si no encuentra los familiares de la persona

            return response()->json([//Devolver los datos en formato JSON
                'error' => false,
                'data' => [],
                'mensaje' => 'No posee familia asignada'
            ]);
        }

        // Transformamos los datos para incluir la foto en base64
        $data = $familiares->map(function ($f) {//Iterar sobre cada familiar
            $persona = $f->familiapersona2;//Obtener la persona del familiar
            return [//Devolver los datos de la persona junto con el parentesco
                'id_persona' => $persona->id_persona,
                'cedula'     => $persona->cedula,
                'nombres'    => $persona->nombres,
                'apellidos'  => $persona->apellidos,
                'parentesco' => $f->parentesco,
                'telefono'   => $persona->telefono,
                'correo'     => $persona->correo,
                'foto'       => $persona->foto ? base64_encode($persona->foto) : null,
            ];
        });
        //Devolver los datos en formato JSON
        return response()->json([
            'error' => false,
            'data' => $data
        ]);
    }
    /**
     * Función para obtener los familiares de una persona específica, recibiendo como parámetro el id de la persona.
     * La función getFamiliaresEstByPersona es la encargada de obtener los familiares de una persona específica, recibiendo como parámetro el id de la persona. 
     * La función busca los familiares de la persona con el id proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. Si no encuentra los familiares de la persona, devuelve un mensaje de error indicando que no se encontró los familiares de la persona.
     */
    public function getFamiliaresEstByPersona(int $id_persona)
    {
        // Buscamos los familiares donde el ID enviado sea el representante
        $familiares = Familia::with(['familiapersona1']) // Cargamos la relación del familiar
            ->where('id_estudiante', $id_persona)//Filtrar solo los familiares de la persona especificada
            ->get();//Obtener los datos de la consulta

        if ($familiares->isEmpty()) {//Si no encuentra los familiares de la persona
            return response()->json([//Devolver los datos en formato JSON
                'error' => false,
                'data' => [],
                'mensaje' => 'No posee familia asignada'
            ]);
        }

        // Transformamos los datos para incluir la foto en base64
        $data = $familiares->map(function ($f) {//Iterar sobre cada familiar
            $persona = $f->familiapersona1;//Obtener la persona del familiar
            if($f->parentesco == 'Hijo/a' && $persona->sexo == 'Femenino'){//Si el parentesco es Hijo/a y la persona es Femenina
                $nuevoparentesco = 'Mamá';//Asignar el nuevo parentesco
            }
            else if($f->parentesco == 'Hijo/a' && $persona->sexo == 'Masculino'){//Si el parentesco es Hijo/a y la persona es Masculino
                $nuevoparentesco = 'Padre';//Asignar el nuevo parentesco
            }else if($f->parentesco == 'Sobrino/a' && $persona->sexo == 'Femenino'){//Si el parentesco es Sobrino/a y la persona es Femenina
                $nuevoparentesco = 'Tía';//Asignar el nuevo parentesco
            }else if($f->parentesco == 'Sobrino/a' && $persona->sexo == 'Masculino'){//Si el parentesco es Sobrino/a y la persona es Masculino
                $nuevoparentesco = 'Tío';//Asignar el nuevo parentesco
            }else if($f->parentesco == 'Nieto/a' && $persona->sexo == 'Femenino'){//Si el parentesco es Nieto/a y la persona es Femenina
                $nuevoparentesco = 'Abuela';//Asignar el nuevo parentesco
            }else if($f->parentesco == 'Nieto/a' && $persona->sexo == 'Masculino'){//Si el parentesco es Nieto/a y la persona es Masculino
                $nuevoparentesco = 'Abuelo';//Asignar el nuevo parentesco
            }else{
                //Si no se encuentra ningún parentesco válido, asignar el parentesco original
                $nuevoparentesco = $f->parentesco;
            }
            return [//Devolver los datos de la persona junto con el parentesco
                'id_persona' => $persona->id_persona,
                'cedula'     => $persona->cedula,
                'nombres'    => $persona->nombres,
                'apellidos'  => $persona->apellidos,
                'parentesco' => $nuevoparentesco,
                'telefono'   => $persona->telefono,
                'sexo'       => $persona->sexo,
                'correo'     => $persona->correo,
                'foto'       => $persona->foto ? base64_encode($persona->foto) : null,
            ];
        });
        //Devolver los datos en formato JSON
        return response()->json([
            'error' => false,
            'data' => $data
        ]);
    }

    /**
     * Función para mostrar un familiar específico, recibiendo como parámetro el id del registro.
     * La función show es la encargada de mostrar un familiar específico, recibiendo como parámetro el id del registro. 
     * La función busca el familiar con el id proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. Si no encuentra el familiar, devuelve un mensaje de error indicando que no se encontró el familiar.
     */
    public function show(string $id)
    {
        $query = Personas::select(
            'personas.id_persona as personID',
            'personas.cedula',
            'personas.nombres',
            'personas.apellidos',
            'personas.foto',
            'personas.fecha_nacimiento',
            'personas.sexo',
            'usuarios.id_usuario',
            'usuarios.username',
            'usuarios.estado'
        )//Seleccionar todas las columnas de la tabla 'personas', de la tabla 'usuarios', de la tabla 'roles'
            ->join('usuarios', 'usuarios.id_persona', '=', 'personas.id_persona')//Unir la tabla 'usuarios' con la tabla 'personas' en base a la columna 'id_persona'
            // CORRECCIÓN 1: Menores de 20 años (< 20)
            ->whereRaw('TIMESTAMPDIFF(YEAR, personas.fecha_nacimiento, CURDATE()) < 20')//Filtrar solo las personas con el año de nacimiento inferior o igual a 20
            ->where('usuarios.estado', 1)//Filtrar solo las personas con el estado 1
            ->where('personas.cedula', $id);//Filtrar solo las personas con el cédula proporcionado

        $data = $query->first(); // Obtenemos el objeto directamente

        // CORRECCIÓN 2: Evaluamos si es nulo (no usamos isEmpty)
        if (!$data) {
            return response()->json(['data' => [], 'message' => 'No se encontraron datos'], 200);
        }

        // CORRECCIÓN 3: Limpieza de datos directamente sobre el objeto
        $attributes = $data->getAttributes();//Obtener los atributos del objeto
        foreach ($attributes as $key => $value) {//Iterar sobre cada clave-valor del array
            if ($key === 'foto' && !empty($value)) {//Si el valor es una cadena de caracteres
                $attributes[$key] = base64_encode($value);//Convertir la cadena de caracteres a base64
            } elseif (is_string($value) && $key !== 'foto') {//Si el valor es una cadena de caracteres
                $attributes[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');//Convertir la cadena de caracteres a UTF-8
            }
        }

        // Reemplazamos los atributos limpios
        $data->setRawAttributes($attributes);
        //Devolver los datos en formato JSON
        return response()->json([
            'data' => $data,
            'mensaje' => 'Encontrado con Éxito!!',
        ]);
    }
}
