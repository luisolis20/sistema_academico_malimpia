<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

   public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'correo' => 'required|string',
            'contrasena' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }
        $correo = $request->input('correo');
        $contrasena = $request->input('contrasena');
        $user = User::select('usuarios.*', 'roles.nombre_rol')
            ->join('roles', 'roles.id_rol', '=', 'usuarios.id_rol')
            ->where('correo', $correo)
            ->first();
        if($user){
            if(!Hash::check($contrasena, $user->contrasena)){
                return response()->json([
                    'error' => 'Contraseña incorrecta',
                ], Response::HTTP_UNAUTHORIZED);
            }
            if($user->estado !== 1){
                return response()->json([
                    'error' => 'Usuario inactivo',
                ], Response::HTTP_UNAUTHORIZED);
            }
            $token = auth()->login($user);
            return response()->json([
                'mensaje' => 'Autenticación exitosa',
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
                'nombre' => $user->nombre,
                'correo' => $user->correo,
                'rol' => $user->nombre_rol,
                'id_usuario' => $user->id_usuario,
            ]);
            
        }
        else {

            return response()->json([
                'error' => true,
                'mensaje' => "El Usuario: $correo no Existe",
            ], Response::HTTP_NOT_FOUND);
        }
   }
   public function me()
    {
        return response()->json(auth()->user());
    }
    public function logout()
    {
        //auth()->logout();
         try {
            $token = JWTAuth::getToken();
            if (! $token) {
                return response()->json(['error' => 'No hay token'], Response::HTTP_BAD_REQUEST);
            }
            JWTAuth::invalidate($token);

            return response()->json(['message' => 'Has cerrado sesion'], Response::HTTP_OK);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token inválido'], Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo cerrar sesion'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function refresh()
    {
        try {
            $token = JWTAuth::getToken();
            if (! $token) {
                return response()->json(['error' => 'No hay token'], Response::HTTP_BAD_REQUEST);
            }
            $nuevo_token = JWTAuth::refresh();
            JWTAuth::invalidate($token);

            return $this->respondWithToken($nuevo_token);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token inválido'], Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo refrescar sesion'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
        ], Response::HTTP_OK);
    }

}
