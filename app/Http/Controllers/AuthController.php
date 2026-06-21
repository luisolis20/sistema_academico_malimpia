<?php

namespace App\Http\Controllers;
//Importación de clases necesarias para el controlador AuthController
use App\Models\User;//Modelo de usuario para interactuar con la base de datos
use Illuminate\Http\Request;//Clase para manejar las solicitudes HTTP
use Illuminate\Http\Response;//Clase para manejar las respuestas HTTP
use Illuminate\Support\Facades\Validator;//Clase para validar los datos de entrada
use Tymon\JWTAuth\Exceptions\TokenInvalidException;//Excepción para manejar tokens JWT inválidos
use Tymon\JWTAuth\Facades\JWTAuth;//Facades para manejar la autenticación JWT
use Illuminate\Support\Facades\Hash;//Clase para manejar el hashing de contraseñas
//Clase AuthController que representa un controlador en la aplicación para manejar las operaciones relacionadas con la autenticación
class AuthController extends Controller
{
    /**
     * Función que devuelve un token JWT para la autenticación de usuarios, recibiendo como parámetros el objeto Request que contiene los datos enviados por el formulario.
     */
    public function login(Request $request)
    {   
        // Validación de los datos enviados por el formulario
        $validator = Validator::make($request->all(), [
            'correo' => 'required|string',
            'contrasena' => 'required|string',
        ]);
        //Si ocurre algún error, devolver un mensaje de error en formato JSON con el código de estado HTTP 400 (Bad Request)
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }
        //Obtener los datos de la solicitud
        $correo = $request->input('correo');//Obtener el correo de la persona
        $contrasena = $request->input('contrasena');//Obtener la contraseña de la persona
        $user = User::select(//Seleccionar todos los campos de la tabla 'usuarios' y algunos campos específicos de las tablas relacionadas 'roles' y 'personas'
            'usuarios.*',
            'roles.nombre as nombre_rol',
            'personas.id_persona as persona_id',
            'personas.nombres as nombre_persona',
            'personas.apellidos as apellidos_persona',
            'personas.correo as correo_persona',
            'personas.cedula as cedula_persona'
        )
            ->join('roles', 'roles.id_rol', '=', 'usuarios.id_rol')//Unir la tabla 'roles' con la tabla 'usuarios' en base a la columna 'id_rol'
            ->join('personas', 'personas.id_persona', '=', 'usuarios.id_persona')//Unir la tabla 'personas' con la tabla 'usuarios' en base a la columna 'id_persona'
            ->where('username', $correo)//Filtrar solo los usuarios con el correo proporcionado
            ->first();//Obtener el primer registro de la consulta
        //Si el usuario existe, verificar la contraseña
        if ($user) {
            //Verificar si la contraseña es correcta utilizando la función Hash::check() que compara la contraseña proporcionada con la contraseña almacenada en la base de datos
            if (!Hash::check($contrasena, $user->clave)) {
                return response()->json([
                    'error' => true,//Si la contraseña es incorrecta, devolver un mensaje de error en formato JSON con el código de estado HTTP 401 (Unauthorized)
                    'mensaje' => "Usuario correcto pero contraseña incorrecta",//Enviar un mensaje de error indicando que la contraseña es incorrecta
                ], Response::HTTP_UNAUTHORIZED);//Devolver el código de estado HTTP 401 (Unauthorized)
            }
            //Verificar si el usuario está bloqueado, si el estado del usuario es diferente de 1, devolver un mensaje de error indicando que el usuario está bloqueado
            if ($user->estado !== 1) {
                return response()->json([//Devolver un mensaje de error indicando que el usuario está bloqueado
                    'error' => true,//Indicar que la solicitud no tuvo éxito
                    'mensaje' => "Usuario bloqueado",//Enviar un mensaje de error indicando que el usuario está bloqueado
                ], Response::HTTP_UNAUTHORIZED);//Devolver el código de estado HTTP 401 (Unauthorized)
            }

            $token = auth()->login($user);//Autenticar el usuario
            //Devolver un mensaje de éxito indicando que la autenticación se realizó correctamente
            return response()->json([//Devolver los datos de la respuesta
                'mensaje' => 'Autenticación exitosa',//Enviar un mensaje de éxito indicando que la autenticación se realizó correctamente
                'token' => $token,//Enviar el token JWT generado
                'token_type' => 'bearer',//Indicar el tipo de token utilizado (Bearer)
                'expires_in' => config('jwt.ttl') * 60,//Indicar la cantidad de tiempo en segundos que durará el token (60 minutos)
                'username' => $user->username,//Enviar el nombre de usuario del usuario autenticado
                'correo' => $user->correo_persona,//Enviar el correo del usuario autenticado
                'nombre' => $user->nombre_persona,//Enviar el nombre del usuario autenticado
                'apellidos' => $user->apellidos_persona,//Enviar el apellidos del usuario autenticado
                'cedula' => $user->cedula_persona,//Enviar la cedula del usuario autenticado
                'rol' => $user->nombre_rol,//Enviar el nombre del rol del usuario autenticado
                'id_usuario' => $user->id_usuario,//Enviar el id del usuario autenticado
                'id_persona' => $user->persona_id,//Enviar el id de la persona del usuario autenticado
            ]);
            //Si el usuario no existe, devolver un mensaje de error en formato JSON con el código de estado HTTP 404 (Not Found)
        } else {
            //Si el usuario no existe, devolver un mensaje de error en formato JSON con el código de estado HTTP 404 (Not Found)
            return response()->json([
                'error' => true,//Indicar que la solicitud no tuvo éxito
                'mensaje' => "El Usuario: $correo no Existe",//Enviar un mensaje de error indicando que el usuario no existe
            ], Response::HTTP_NOT_FOUND);//Devolver el código de estado HTTP 404 (Not Found)
        }
    }
    /**
     * Función que devuelve la información del usuario autenticado, no se reciben parámetros, pero se utiliza el token JWT para identificar al usuario autenticado y devolver su información en formato JSON.
     */
    public function me()
    {
        //Obtener el token JWT del usuario autenticado
        return response()->json(auth()->user());
    }
    /**
     * Función que cierra la sesión del usuario autenticado, no se reciben parámetros, pero se utiliza el token JWT para identificar al usuario autenticado y cierra la sesión.
     */
    public function logout()
    {
        try {
            //Obtener el token JWT del usuario autenticado
            $token = JWTAuth::getToken();
            //Si no hay token, devolver un mensaje de error en formato JSON con el código de estado HTTP 400 (Bad Request)
            if (! $token) {
                return response()->json(['error' => 'No hay token'], Response::HTTP_BAD_REQUEST);
            }
            //Invalidar el token JWT del usuario autenticado para cerrar la sesión
            JWTAuth::invalidate($token);
            //Devolver un mensaje de éxito indicando que la sesión se ha cerrado correctamente
            return response()->json(['message' => 'Has cerrado sesion'], Response::HTTP_OK);//Devolver el código de estado HTTP 200 (OK)
        } catch (TokenInvalidException $e) {
            //Si ocurre algún error, devolver un mensaje de error en formato JSON con el código de estado HTTP 401 (Unauthorized)
            return response()->json(['error' => 'Token inválido'], Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            //Si ocurre algún error, devolver un mensaje de error en formato JSON con el código de estado HTTP 500 (Internal Server Error)
            return response()->json(['error' => 'No se pudo cerrar sesion'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * Función que devuelve un nuevo token JWT para la autenticación de usuarios, no se reciben parámetros, pero se utiliza el token JWT para identificar al usuario autenticado y devolver un nuevo token JWT en formato JSON.
     */
    public function refresh()
    {
        try {
            //Obtener el token JWT del usuario autenticado
            $token = JWTAuth::getToken();
            //Si no hay token, devolver un mensaje de error en formato JSON con el código de estado HTTP 400 (Bad Request)
            if (! $token) {
                return response()->json(['error' => 'No hay token'], Response::HTTP_BAD_REQUEST);
            }
            //Obtener un nuevo token JWT para la autenticación de usuarios, invalidar el token JWT actual para evitar que se siga utilizando y devolver el nuevo token JWT en formato JSON
            $nuevo_token = JWTAuth::refresh();
            JWTAuth::invalidate($token);
            //Devolver el nuevo token JWT en formato JSON con el código de estado HTTP 200 (OK)
            return $this->respondWithToken($nuevo_token);
        } catch (TokenInvalidException $e) {
            //Si ocurre algún error, devolver un mensaje de error en formato JSON con el código de estado HTTP 401 (Unauthorized)
            return response()->json(['error' => 'Token inválido'], Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            //Si ocurre algún error, devolver un mensaje de error en formato JSON con el código de estado HTTP 500 (Internal Server Error)
            return response()->json(['error' => 'No se pudo refrescar sesion'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * Función que devuelve un token JWT en formato JSON, recibe como parámetros el token JWT y el tipo de token (Bearer).
     */
    protected function respondWithToken($token)
    {   
        //Devolver el token JWT en formato JSON con el código de estado HTTP 200 (OK)
        return response()->json([
            'token' => $token,//Devolver el token JWT
            'token_type' => 'bearer',//Devolver el tipo de token utilizado (Bearer)
            'expires_in' => JWTAuth::factory()->getTTL() * 60,//Devolver la cantidad de tiempo en segundos que durará el token (60 minutos)
        ], Response::HTTP_OK);//Devolver el código de estado HTTP 200 (OK)
    }
}
