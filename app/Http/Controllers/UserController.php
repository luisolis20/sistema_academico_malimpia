<?php

namespace App\Http\Controllers;
//Importación de clases necesarias para el controlador UserController
use App\Models\Personas;//Importación de la clase Personas
use App\Models\User;//Importación de la clase User
use Illuminate\Http\Request;//Importación de la clase Request para manejar las solicitudes HTTP
use Illuminate\Support\Facades\DB;//Importación de la clase DB para acceder a las tablas de la base de datos
use Illuminate\Support\Facades\Hash;//Importación de la clase Hash para generar claves

//Clase UserController que representa un controlador en la aplicación para manejar las operaciones relacionadas con los usuarios
class UserController extends Controller
{
    /**
     * Función que muestra una lista de usuarios, las cuales pueden ser filtradas por página y por búsqueda.
     * La función index es la encargada de mostrar una lista de usuarios, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     */
    public function index(Request $request)
    {
        try {
            $perPage = min($request->input('per_page', 10), 20);//Limitar el número de elementos por página a 20
            $searchQuery = $request->input('search_query');//Obtener la consulta de búsqueda
            $status = $request->input('status');//Obtener el estado del usuario
            $filterAge = $request->input('filter_age');//Obtener el filtro de edad

            // 1. Consulta principal (Paginada y filtrada)
            $query = Personas::select(
                'personas.id_persona as personID',
                'personas.cedula',
                'personas.nombres',
                'personas.apellidos',
                'personas.foto',
                'personas.fecha_nacimiento',
                'personas.sexo',
                'usuarios.*',
                'roles.id_rol as RoleID',
                'roles.nombre as nombre_rol'
            )//Seleccionar todas las columnas de la tabla 'personas', de la tabla 'usuarios' y de la tabla 'roles'
                ->leftJoin('usuarios', 'usuarios.id_persona', '=', 'personas.id_persona')//Unir la tabla 'usuarios' con la tabla 'personas' en el campo 'id_persona'
                ->leftJoin('roles', 'roles.id_rol', '=', 'usuarios.id_rol');//Unir la tabla 'roles' con la tabla 'usuarios' en el campo 'id_rol'

            if (! empty($searchQuery)) {//Si hay una consulta de búsqueda, aplicarla a los campos relevantes
                $query->where(function ($q) use ($searchQuery) {//Filtrar solo los usuarios que coincidan con la consulta de búsqueda
                    $q->where('personas.cedula', 'LIKE', "%{$searchQuery}%")//Filtrar solo los usuarios que coincidan con la consulta de búsqueda
                        ->orWhere('personas.nombres', 'LIKE', "%{$searchQuery}%")//Filtrar solo los usuarios que coincidan con la consulta de búsqueda
                        ->orWhere('personas.apellidos', 'LIKE', "%{$searchQuery}%")//Filtrar solo los usuarios que coincidan con la consulta de búsqueda
                        ->orWhere('roles.nombre', 'LIKE', "%{$searchQuery}%");//Filtrar solo los usuarios que coincidan con la consulta de búsqueda
                });
            }
            //Obtener los datos paginados
            if (! empty($status)) {//Si el estado del usuario es especificado
                $query->where('usuarios.estado', 'LIKE', "{$status}");//Filtrar solo los usuarios que coincidan con el estado especificado
            }
            if ($filterAge === 'under_20') {//Si el filtro de edad es menor de 20
                $query->whereRaw('TIMESTAMPDIFF(YEAR, personas.fecha_nacimiento, CURDATE()) < 20');//Filtrar solo los usuarios que tienen una edad inferior a 20
            } elseif ($filterAge === 'over_20') {//Si el filtro de edad es mayor de 20
                $query->whereRaw('TIMESTAMPDIFF(YEAR, personas.fecha_nacimiento, CURDATE()) >= 20');//Filtrar solo los usuarios que tienen una edad mayor a 20
            }

            $data = $query->paginate($perPage);//Paginar los resultados

            if ($data->isEmpty()) {//Si no hay datos, devolver un mensaje de error
                return response()->json(['data' => [], 'message' => 'No se encontraron datos'], 200);//Devolver un mensaje de error
            }

            // Transformación de datos (Imágenes y codificación)
            $data->getCollection()->transform(function ($item) {//Iterar sobre cada elemento de la colección
                $attributes = $item->getAttributes();//Obtener los atributos del elemento
                foreach ($attributes as $key => $value) {//Iterar sobre cada clave-valor del array
                    if ($key === 'foto' && ! empty($value)) {//Si el valor es una cadena de caracteres
                        $attributes[$key] = base64_encode($value);//Convertir la cadena de caracteres a base64
                    } elseif (is_string($value) && $key !== 'foto') {//Si el valor es una cadena de caracteres
                        $attributes[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');//Convertir la cadena de caracteres a UTF-8
                    }
                }
                return $attributes;//Devolver los atributos del elemento transformados
            });

            // ---------------------------------------------------------
            // 2. NUEVO: Estadísticas Generales
            // ---------------------------------------------------------
            
            // Total de usuarios en el sistema (que tienen un registro en la tabla usuarios)
            $totalUsuarios = DB::table('usuarios')->count();

            // Total de usuarios agrupados por rol
            // Retornará algo como: {"Estudiante": 150, "Profesor": 25, "Admin": 5}
            $usuariosPorRol = DB::table('usuarios')//Seleccionar todas las columnas de la tabla 'usuarios'
                ->join('roles', 'usuarios.id_rol', '=', 'roles.id_rol')//Unir la tabla 'roles' con la tabla 'usuarios' en el campo 'id_rol'
                ->select('roles.nombre', DB::raw('count(usuarios.id_usuario) as total'))//Seleccionar el campo 'nombre' de la tabla 'roles' y el campo 'total' de la tabla 'usuarios'
                ->groupBy('roles.id_rol', 'roles.nombre')//Agrupar por 'id_rol' y 'nombre'
                ->pluck('total', 'roles.nombre');//Obtener el campo 'total' de la tabla 'usuarios' agrupado por 'nombre'

            // 3. Retornamos la respuesta incluyendo las estadísticas
            return response()->json([
                'data' => $data->items(),
                'estadisticas' => [
                    'total_general' => $totalUsuarios,
                    'totales_por_rol' => $usuariosPorRol
                ],
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'last_page' => $data->lastPage(),
                ],
            ], 200);
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Función para obtener a todos los usuarios que no tienen un registro en la tabla 'usuarios' y que tienen una edad inferior a 20.
     * La función getPendientesMasivo es la encargada de obtener a todos los usuarios que no tienen un registro en la tabla 'usuarios' y que tienen una edad inferior a 20.
     * La función busca a todos los usuarios que no tienen un registro en la tabla 'usuarios' y que tienen una edad inferior a 20, seleccionando solo las columnas 'id_persona' y 'cedula'.
     * Luego, devuelve los datos en formato JSON, incluyendo la información de paginación.
     */
    public function getPendientesMasivo(Request $request)
    {
        try {
            // Traemos a TODOS los que no tienen usuario y son menores de 20
            $personas = Personas::select('personas.id_persona as personID', 'personas.cedula')//Seleccionar todas las columnas de la tabla 'personas', de la tabla 'usuarios' y de la tabla 'roles'
                ->leftJoin('usuarios', 'usuarios.id_persona', '=', 'personas.id_persona')//Unir la tabla 'usuarios' con la tabla 'personas' en el campo 'id_persona'
                ->whereNull('usuarios.id_usuario')//Filtrar solo los usuarios que no tienen un registro en la tabla 'usuarios'
                ->whereRaw('TIMESTAMPDIFF(YEAR, personas.fecha_nacimiento, CURDATE()) < 20')//Filtrar solo los usuarios que tienen una edad inferior a 20
                ->get();//Obtener los datos de la consulta

            return response()->json(['data' => $personas], 200);//Devolver los datos en formato JSON
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Función para obtener a todos los usuarios que no tienen un registro en la tabla 'usuarios' y que tienen una edad mayor a 20.
     * La función getPendientesMayoresMasivo es la encargada de obtener a todos los usuarios que no tienen un registro en la tabla 'usuarios' y que tienen una edad mayor a 20.
     * La función busca a todos los usuarios que no tienen un registro en la tabla 'usuarios' y que tienen una edad mayor a 20, seleccionando solo las columnas 'id_persona' y 'cedula'.
     * Luego, devuelve los datos en formato JSON, incluyendo la información de paginación.
     */
    public function getPendientesMayoresMasivo(Request $request)
    {
        try {
            // Traemos a TODOS los que no tienen usuario y son menores de 20
            $personas = Personas::select('personas.id_persona as personID', 'personas.cedula')//Seleccionar todas las columnas de la tabla 'personas', de la tabla 'usuarios' y de la tabla 'roles'
                ->leftJoin('usuarios', 'usuarios.id_persona', '=', 'personas.id_persona')//Unir la tabla 'usuarios' con la tabla 'personas' en el campo 'id_persona'
                ->whereNull('usuarios.id_usuario')//Filtrar solo los usuarios que no tienen un registro en la tabla 'usuarios'
                ->whereRaw('TIMESTAMPDIFF(YEAR, personas.fecha_nacimiento, CURDATE()) >= 20')//Filtrar solo los usuarios que tienen una edad mayor a 20
                ->get();//Obtener los datos de la consulta
            //Devolver los datos en formato JSON
            return response()->json(['data' => $personas], 200);
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Función para crear un solo usuario (desde el Modal).
     * La función storeUsuario es la encargada de crear un solo usuario (desde el Modal).
     * La función busca el usuario con el id_persona proporcionado y, si lo encuentra, crea el usuario con los datos enviados por el formulario.
     * Si no encuentra el usuario, devuelve un mensaje de error indicando que no se encontró el usuario.
     */
    public function storeUsuario(Request $request)
    {
        try {
            $existe = DB::table('usuarios')->where('id_persona', $request->id_persona)->exists();//Verificar si el usuario ya existe
            if ($existe) {//Si ya existe, devolver un mensaje de error
                return response()->json(['error' => 'Esta persona ya tiene un usuario asignado.'], 400);
            }
            //Crear el usuario con los datos enviados por el formulario
            $usuario = DB::table('usuarios')->insert([
                'id_persona' => $request->id_persona,
                'id_rol' => $request->id_rol,
                'username' => $request->username,
                'clave' => Hash::make($request->username), // Clave por defecto = cedula
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);//Crear el usuario con los datos enviados por el formulario

            return response()->json(['message' => 'Usuario creado exitosamente.'], 201);//Devolver un mensaje de éxito
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear usuario: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Función para actualizar un usuario específico, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario y el id del registro.
     * La función updateUsuario es la encargada de actualizar un usuario específico, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario y el id del registro.
     * La función busca el usuario con el id proporcionado y, si lo encuentra, actualiza los datos enviados por el formulario y guarda los cambios en la base de datos. Si no encuentra el usuario, devuelve un mensaje de error indicando que no se encontró el usuario.
     */
    public function updateUsuario(Request $request, int $id)
    {
        try {
            // 1. Verificamos que el usuario que intentan editar realmente exista
            $usuarioExiste = DB::table('usuarios')->where('id_usuario', $id)->exists();//Verificar si el usuario existe
            if (!$usuarioExiste) {//Si no existe, devolver un mensaje de error
                return response()->json(['error' => 'Usuario no encontrado.'], 404);
            }

            // 2. (Opcional pero recomendado) Verificar que el nuevo username no lo tenga otro usuario
            $usernameOcupado = DB::table('usuarios')//Verificar si el nuevo username ya está en uso por otro usuario
                ->where('username', $request->username)//Filtrar solo los usuarios que coincidan con el nuevo username
                ->where('id_usuario', '!=', $id) // Excluimos al usuario actual
                ->exists();//Verificar si existe otro usuario

            if ($usernameOcupado) {//Si existe otro usuario con el mismo username, devolver un mensaje de error
                return response()->json(['error' => 'Este nombre de usuario ya está en uso por otra persona.'], 400);
            }

            // 3. Ejecutamos la actualización
            DB::table('usuarios')//Actualizar los datos del usuario
                ->where('id_usuario', $id)//Filtrar solo los usuarios que coincidan con el id especificado
                ->update([
                    'id_persona' => $request->id_persona,
                    'id_rol' => $request->id_rol,
                    'username' => $request->username,
                    'clave' => Hash::make($request->username), // Encriptar la clave por defecto = cedula
                    'estado' => 1,
                    'updated_at' => now(),
                ]);//Actualizar los datos del usuario

            return response()->json(['message' => 'Usuario actualizado exitosamente.'], 200);//Devolver un mensaje de éxito
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar usuario: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Función para registrar a varios usuarios por lotes (chunks).
     * La función storeMasivo es la encargada de registrar a varios usuarios por lotes (chunks).
     * La función busca los usuarios con los ids de personas proporcionados y, si los encuentra, crean los usuarios con los datos enviados por el formulario.
     * Si no encuentra el usuario, devuelve un mensaje de error indicando que no se encontró el usuario.
     */
    public function storeMasivo(Request $request)
    {
        $personas = $request->input('personas'); // Array de [personID, cedula]
        $errores = [];//Inicializar el array de errores
        $registrados = 0;//Inicializar contador de usuarios registrados

        // Buscar el id_rol para 'estudiante'. Si no existe, pon un ID por defecto (ej: 3)
        $rolEstudiante = DB::table('roles')->where('nombre', 'LIKE', '%estudiante%')//Buscar el rol 'estudiante'
            ->where('estado', 1)->first();//Filtrar solo los roles activos
        $idRol = $rolEstudiante ? $rolEstudiante->id_rol : 3;//Asignar el id de rol 'estudiante' o el ID por defecto (ej: 3)

        foreach ($personas as $p) {//Iterar sobre cada persona
            try {
                $existe = DB::table('usuarios')->where('id_persona', $p['personID'])->exists();//Verificar si el usuario ya existe
                if (! $existe) {//Si no existe, crear el usuario con los datos enviados por el formulario
                    DB::table('usuarios')->insert([
                        'id_persona' => $p['personID'],
                        'id_rol' => $idRol,
                        'username' => $p['cedula'],
                        'clave' => Hash::make($p['cedula']),
                        'estado' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);//Crear el usuario con los datos enviados por el formulario
                    $registrados++;//Incrementar contador de usuarios registrados
                }
                //Si ocurre algún error, devolver un mensaje de error en formato JSON   
            } catch (\Exception $e) {
                $errores[] = $p['cedula'];//Agregar el cedula del usuario a la lista de errores
            }
        }
        //Devolver los datos en formato JSON
        return response()->json([
            'registrados' => $registrados,
            'errores' => $errores,
        ], 200);
    }
    /**
     * Función para resetear la clave de un usuario específico, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario y el id del registro.
     * La función resetearClave es la encargada de resetear la clave de un usuario específico, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario y el id del registro.
     * La función busca el usuario con el id proporcionado y, si lo encuentra, actualiza la clave del usuario con la nueva clave enviada por el formulario y guarda los cambios en la base de datos. Si no encuentra el usuario, devuelve un mensaje de error indicando que no se encontró el usuario.
     */
    public function resetearClave(Request $request, string $id)
    {
        try {
            $request->validate([
                'nueva_clave' => 'required|string'
            ]);//Validar que la clave nueva sea una cadena de caracteres

            $usuario = User::findOrFail($id);//Obtener el usuario con el id proporcionado
            $usuario->clave = Hash::make($request->nueva_clave); // Hasheamos la cédula

            if ($usuario->save()) {//Guardar los cambios en la base de datos
                //Si el guardado es exitoso, devolver los datos del usuario actualizados en formato JSON, incluyendo un mensaje de éxito
                return response()->json([
                    'data' => $usuario,
                    'mensaje' => 'Clave reseteada correctamente',
                ]);
            } else {
                //Si ocurre algún error, devolver un mensaje de error en formato JSON
                return response()->json([//Devolver los datos en formato JSON
                    'error' => true,
                    'mensaje' => 'Error al resetear clave',
                ]);
            }
            //Si ocurre algún error, devolver un mensaje de error en formato JSON
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Función para insertar nuevos usuarios en la base de datos, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     * La función store es la encargada de insertar nuevos usuarios en la base de datos, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     * Antes de insertar los nuevos usuarios, se realiza una validación para asegurar que no existan conflictos entre usuarios, especialidades y periodos lectivos. Si se intenta insertar un usuario que ya existe, se devuelve un mensaje de error indicando el conflicto. Si se intenta insertar una especialidad que ya existe, se devuelve un mensaje de error indicando el conflicto. Si se intenta insertar un periodo lectivo que ya existe, se devuelve un mensaje de error indicando el conflicto. Si la validación pasa sin problemas, se procede a insertar los nuevos usuarios y se devuelve un mensaje de éxito junto con los datos del nuevo registro.
     */
    public function store(Request $request)
    {
        $inputs = $request->input();//Obtener los datos enviados por el formulario
        $inputs['clave'] = Hash::make($inputs['clave']);//Encriptar la clave por defecto = cedula
        $res = User::create($inputs);//Crear el usuario con los datos enviados por el formulario
        //Devolver los datos creados en formato JSON, incluyendo un mensaje de éxito
        return response()->json([
            'data' => $res,
            'mensaje' => 'Agregado con Éxito!!',
        ]);
    }

    /**
     * Función para mostrar un usuario específico, recibiendo como parámetro el id del registro.
     * La función show es la encargada de mostrar un usuario específico, recibiendo como parámetro el id del registro.     
     * La función busca el usuario con el id proporcionado y, si lo encuentra, devuelve los datos en formato JSON junto con un mensaje de éxito. Si no encuentra el usuario, devuelve un mensaje de error indicando que no se encontró el usuario.
     */
    public function show(string $id)
    {
        $res = User::select('usuarios.*','roles.nombre as nombre_rol')//Seleccionar todos los campos de la tabla 'usuarios', de la tabla 'roles'
            ->join('roles', 'roles.id_rol', '=', 'usuarios.id_rol')//Unir la tabla 'roles' con la tabla 'usuarios' en el campo 'id_rol'
            ->where('usuarios.id_usuario', $id)//Filtrar solo los usuarios que coincidan con el id especificado
            ->first();//Obtener los datos de la consulta
        if ($res) { //Si el usuario existe
            return response()->json([//Devolver los datos en formato JSON
                'data' => $res,
                'mensaje' => 'Encontrado con Éxito!!',
            ]);
        }
        //Si el usuario no existe, devolver un mensaje de error en formato JSON
        return response()->json([
            'error' => true,
            'mensaje' => "El Usuario con id: $id no Existe",
        ]);
    }

    /**
     * Función para actualizar los datos de un usuario específico, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario y el id del registro.
     * La función update es la encargada de actualizar los datos de un usuario específico, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario y el id del registro.
     * La función busca el usuario con el id proporcionado y, si lo encuentra, actualiza los datos enviados por el formulario y guarda los cambios en la base de datos. Si no encuentra el usuario, devuelve un mensaje de error indicando que no se encontró el usuario.
     */
    public function update(Request $request, string $id)
    {
        $res = User::find($id);//Obtener el usuario con el id proporcionado
        if (isset($res)) {//Si el usuario existe
            $res->id_persona = $request->id_persona;//Actualizar el id de la persona del usuario
            $res->id_rol = $request->id_rol;//Actualizar el id del rol del usuario
            $res->username = $request->username;//Actualizar el nombre de usuario del usuario
            $res->clave = Hash::make($request->clave);//Encriptar la clave por defecto = cedula
            $res->estado = $request->estado;//Actualizar el estado del usuario
            if ($res->save()) {//Guardar los cambios en la base de datos
                //Si el guardado es exitoso, devolver los datos del usuario actualizados en formato JSON, incluyendo un mensaje de éxito
                return response()->json([
                    'data' => $res,
                    'mensaje' => 'Actualizado con Éxito!!',
                ]);
            } else {
                //Si ocurre algún error, devolver un mensaje de error en formato JSON   
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error al Actualizar',
                ]);
            }
            //Si el usuario no existe, devolver un mensaje de error en formato JSON
        } else {
            return response()->json([
                'error' => true,
                'mensaje' => "El Usuario con id: $id no Existe",
            ]);
        }
    }

    /**
     * Función para eliminar un usuario específico, recibiendo como parámetro el id del registro.
     * La función destroy es la encargada de eliminar un usuario específico, recibiendo como parámetro el id del registro.
     * La función busca el usuario con el id proporcionado y, si lo encuentra, inhabilita el usuario y guarda los cambios en la base de datos.
     * Luego, devuelve los datos actualizados en formato JSON, incluyendo un mensaje de éxito.
     * Si no encuentra el usuario, devuelve un mensaje de error indicando que no se encontró el usuario.
     */
    public function destroy(string $id)
    {
        $res = User::find($id);//Obtener el usuario con el id proporcionado
        if (isset($res)) {//Si el usuario existe

            $res->estado = 0;//Inhabilitar el usuario
            if ($res->save()) {//Guardar los cambios en la base de datos
                //Si el guardado es exitoso, devolver los datos del usuario actualizados en formato JSON, incluyendo un mensaje de éxito    
                return response()->json([
                    'data' => $res,
                    'mensaje' => 'Usuario Deshabilitado',
                ]);
            } else {
                //Si ocurre algún error, devolver un mensaje de error en formato JSON
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error al Eliminar',
                ]);
            }
            //Si el usuario no existe, devolver un mensaje de error en formato JSON
        } else {
            return response()->json([
                'error' => true,
                'mensaje' => "El Usuario con id: $id no Existe",
            ]);
        }
    }
    /**
     * Función para habilitar un usuario específico, recibiendo como parámetro el id del registro.
     * La función habilitar es la encargada de habilitar un usuario específico, recibiendo como parámetro el id del registro.
     * La función busca el usuario con el id proporcionado y, si lo encuentra, habilita el usuario y guarda los cambios en la base de datos.
     * Luego, devuelve los datos actualizados en formato JSON, incluyendo un mensaje de éxito.
     * Si no encuentra el usuario, devuelve un mensaje de error indicando que no se encontró el usuario.
     */
    public function habilitar(string $id)
    {
        $res = User::find($id);//Obtener el usuario con el id proporcionado
        if (isset($res)) {//Si el usuario existe

            $res->estado = 1;//Habilitar el usuario
            if ($res->save()) {//Guardar los cambios en la base de datos
                //Si el guardado es exitoso, devolver los datos del usuario actualizados en formato JSON, incluyendo un mensaje de éxito
                return response()->json([
                    'data' => $res,
                    'mensaje' => 'Usuario Habilitado',
                ]);
            } else {
                //Si ocurre algún error, devolver un mensaje de error en formato JSON
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error al Eliminar',
                ]);
            }
            //Si el usuario no existe, devolver un mensaje de error en formato JSON
        } else {
            return response()->json([
                'error' => true,
                'mensaje' => "El Usuario con id: $id no Existe",
            ]);
        }
    }
}
