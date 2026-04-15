<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolController; 
use App\Http\Controllers\PersonaController; 
use App\Http\Controllers\UserController; 
use App\Http\Controllers\FamiliaController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//Definir las rutas para el sistema
Route::prefix('sistma')->group(function (){
    //Definir las rutas para la autenticación
    Route::post('/login', [AuthController::class, 'login']);
    //Definir las rutas para los roles, permitiendo crear, leer, actualizar roles
    Route::apiResource('roles', RolController::class);
    //Definir endpoints para habilitar y deshabilitar roles
    Route::delete('ihabilitar_role/{id}', [RolController::class, 'destroy']);
    Route::delete('habilitar_role/{id}', [RolController::class, 'habilitar']);
    Route::get('roleshabilitados', [RolController::class, 'Roleshabilitados']);
    //Definir endpoint para las personas, permitiendo crear, leer, actualizar personas
    Route::apiResource('personas', PersonaController::class);
    Route::get('imagenpersona/{ci}', [PersonaController::class, 'getFotografia']);
    //Definir endpoints para habilitar y deshabilitar personas
    Route::delete('ihabilitar_persona/{id}', [PersonaController::class, 'destroy']);
    Route::delete('habilitar_persona/{id}', [PersonaController::class, 'habilitar']);
    Route::apiResource('usuarios', UserController::class);   
    //Definir endpoints para habilitar y deshabilitar usuarios
    Route::delete('ihabilitar_usuario/{id}', [UserController::class, 'destroy']);
    Route::delete('habilitar_usuario/{id}', [UserController::class, 'habilitar']);
    Route::post('usuarios/store', [UserController::class, 'storeUsuario']);
    Route::put('usuarios/update/{id}', [UserController::class, 'updateUsuario']);
    Route::post('usuarios/store_masivo', [UserController::class, 'storeMasivo']);
    Route::post('resetear_clave/{id}', [UserController::class, 'resetearClave']);
    Route::get('pendientes_masivo', [UserController::class, 'getPendientesMasivo']);
    Route::get('pendientes_mayores_masivo', [UserController::class, 'getPendientesMayoresMasivo']);

    //Definir las rutas para la familia, permitiendo crear, leer, actualizar familia
    Route::apiResource('familia', FamiliaController::class);
    Route::get('familiar/{ci}', [FamiliaController::class, 'show']);
    Route::post('familia/asignar', [FamiliaController::class, 'store']);
    Route::middleware('auth:api')->group(function (){
        Route::get('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/me', [AuthController::class, 'me']);
        
    });
});
