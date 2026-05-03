<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FamiliaController;
use App\Http\Controllers\Niveles_academicosController;
use App\Http\Controllers\EspecialidadesController;
use App\Http\Controllers\AsignaturasController;
use App\Http\Controllers\Periodos_lectivosController;
use App\Http\Controllers\CursosController;
use App\Http\Controllers\Curso_AsignaturasController;
use App\Http\Controllers\HorariosController;
use App\Http\Controllers\Cronograma_matriculasController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\Control_SubidaNotasController;


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
Route::prefix('sistma')->group(function () {
    //Definir las rutas para la autenticación
    Route::post('/login', [AuthController::class, 'login']);
    //Definir las rutas para los roles, permitiendo crear, leer, actualizar roles
    Route::apiResource('roles', RolController::class);
    //Definir endpoints para habilitar y deshabilitar roles
    Route::delete('ihabilitar_role/{id}', [RolController::class, 'destroy']);
    Route::delete('habilitar_role/{id}', [RolController::class, 'habilitar']);
    Route::get('roleshabilitados', [RolController::class, 'Roleshabilitados']);
    //Definir endpoint para las personas, permitiendo crear, leer, actualizar personas
    Route::apiResource('personas', PersonaController::class)->middleware('throttle:10000,1');
    Route::get('imagenpersona/{ci}', [PersonaController::class, 'getFotografia'])->middleware('throttle:10000,1');
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
    Route::get('familiares-de/{id_persona}', [FamiliaController::class, 'getFamiliaresByPersona']);
    Route::get('familiares-est-de/{id_persona}', [FamiliaController::class, 'getFamiliaresEstByPersona']);

    //Definir las rutas para los niveles académicos, permitiendo crear, leer, actualizar niveles académicos
    Route::apiResource('niveles_academicos', Niveles_academicosController::class);
    Route::get('niveles_academicos_activos', [Niveles_academicosController::class, 'getActivados']);
    //Definir endpoints para habilitar y deshabilitar niveles académicos
    Route::delete('ihabilitar_nivel/{id}', [Niveles_academicosController::class, 'destroy']);
    Route::delete('habilitar_nivel/{id}', [Niveles_academicosController::class, 'habilitar']);
    //Definir las rutas para los especialidades, permitiendo crear, leer, actualizar especialidades
    Route::apiResource('especialidades', EspecialidadesController::class);
    Route::get('especialidades_activos', [EspecialidadesController::class, 'getActivados']);
    //Definir endpoints para habilitar y deshabilitar especialidades
    Route::delete('ihabilitar_especialidad/{id}', [EspecialidadesController::class, 'destroy']);
    Route::delete('habilitar_especialidad/{id}', [EspecialidadesController::class, 'habilitar']);
    //Definir las rutas para los asignaturas, permitiendo crear, leer, actualizar asignaturas
    Route::apiResource('asignaturas', AsignaturasController::class);
    Route::get('asignaturas_activos', [AsignaturasController::class, 'getActivados']);
    //Definir endpoints para habilitar y deshabilitar asignaturas
    Route::delete('ihabilitar_asignatura/{id}', [AsignaturasController::class, 'destroy']);
    Route::delete('habilitar_asignatura/{id}', [AsignaturasController::class, 'habilitar']);
    //Definir las rutas para los periodos lectivos, permitiendo crear, leer, actualizar periodos lectivos
    Route::apiResource('periodos_lectivos', Periodos_lectivosController::class);
    Route::get('periodos_lectivos_activos', [Periodos_lectivosController::class, 'getActivados']);
    //Definir endpoints para habilitar y deshabilitar periodos lectivos
    Route::delete('ihabilitar_periodo_lectivo/{id}', [Periodos_lectivosController::class, 'destroy']);
    Route::delete('habilitar_periodo_lectivo/{id}', [Periodos_lectivosController::class, 'habilitar']);
    //Definir las rutas para los cursos, permitiendo crear, leer, actualizar cursos
    Route::apiResource('cursos', CursosController::class);
    Route::get('cursos_activos', [CursosController::class, 'getActivados']);
    Route::get('cursos_docente/{id}', [CursosController::class, 'getCursosDocente']);
    //Definir endpoints para habilitar y deshabilitar cursos
    Route::delete('ihabilitar_curso/{id}', [CursosController::class, 'destroy']);
    Route::delete('habilitar_curso/{id}', [CursosController::class, 'habilitar']);
    Route::delete('desasignar_docente_curso/{id}', [CursosController::class, 'desasignarDocente']);
    Route::get('docente/carga-academica/{id_persona}', [CursosController::class, 'getCargaAcademica']);
    //Definir las rutas para los curso_asignaturas, permitiendo crear, leer, actualizar curso_asignaturas
    Route::apiResource('curso_asignaturas', Curso_AsignaturasController::class);
    //Definir endpoints para habilitar y deshabilitar curso_asignaturas
    Route::delete('ihabilitar_curso_asignatura/{id}', [Curso_AsignaturasController::class, 'destroy']);
    Route::delete('habilitar_curso_asignatura/{id}', [Curso_AsignaturasController::class, 'habilitar']);
    Route::delete('desasignar_docente_curso_asignatura/{id}', [Curso_AsignaturasController::class, 'desasignarDocente']);
    Route::get('curso_asignaturas/docente/{id}', [Curso_AsignaturasController::class, 'getPorDocente']);
    Route::post('curso_asignaturas_lote/crear', [Curso_AsignaturasController::class, 'procesarAsignaciones']);
    Route::put('curso_asignaturas_lote/actualizar', [Curso_AsignaturasController::class, 'procesarAsignaciones']);
    Route::apiResource('horarios_clases', HorariosController::class);
    Route::post('crearhorario', [HorariosController::class, 'guardarHorario']);
    Route::get('horarios_docente/{id_persona}', [HorariosController::class, 'getHorarioDocente']);
    Route::apiResource('cronograma_matriculas', Cronograma_matriculasController::class);
    Route::post('crearcronograma_matriculas', [Cronograma_matriculasController::class, 'store']);
    Route::get('cronograma_matriculas_activo', [Cronograma_matriculasController::class, 'getCronogramaActivo']);
    Route::get('cursos_por_cronograma/{id_cronograma}', [Cronograma_matriculasController::class, 'getCursosPorCronograma']);
    Route::post('crearmatricula', [Cronograma_matriculasController::class, 'crearmatricula']);
    Route::get('historial/{id_representante}', [Cronograma_matriculasController::class, 'getHistorial']);
    Route::get('matriculas-representante/{id_representante}', [Cronograma_matriculasController::class, 'getCursosMatriculados']);
    Route::get('estudiantes-asignatura/{id_docente}', [Cronograma_matriculasController::class, 'getEstudiantesPorAsignatura']);
    Route::get('buscar-historial-matriculas/{cedula}', [Cronograma_matriculasController::class, 'buscarHistorialMatriculas']);
    Route::post('asistencias-hoy', [AsistenciaController::class, 'storeMasivo']);
    Route::get('asistencia-check/{id_curso_asignatura}', [AsistenciaController::class, 'checkAsistenciaHoy']);
    Route::get('historial-asistencia/{id_docente}', [AsistenciaController::class, 'getHistorialAsistencia']);
    Route::get('datos-tutor/{id_persona}', [AsistenciaController::class, 'getDatosTutor']);
    Route::apiResource('control_subida_notas', Control_SubidaNotasController::class);
    //Definir endpoints para habilitar y deshabilitar control_subida_notas
    Route::delete('habilitar_control_subida_notas/{id}', [Control_SubidaNotasController::class, 'habilitar']);
    Route::get('asignaturas-docente/{id_docente}', [Control_SubidaNotasController::class, 'getAsignaturasDocente']);
    Route::get('estudiantes-asigna/{id_curso_asignatura}', [Control_SubidaNotasController::class, 'getEstudiantesAsignatura']);
    Route::post('calificaciones-guardar', [Control_SubidaNotasController::class, 'guardarCalificaciones']);


    Route::middleware('auth:api')->group(function () {
        Route::get('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});
