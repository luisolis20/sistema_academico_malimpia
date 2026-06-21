<?php
//Importación de clases necesarias para el controlador API
use Illuminate\Http\Request; //Importación de la clase Request para manejar las solicitudes HTTP
use Illuminate\Support\Facades\Route; //Importación de la clase Route para definir las rutas de la API
use App\Http\Controllers\AuthController; //Importación de la clase AuthController para definir las rutas de autenticación
use App\Http\Controllers\RolController; //Importación de la clase RolController para definir las rutas de rols
use App\Http\Controllers\PersonaController; //Importación de la clase PersonaController para definir las rutas de personas
use App\Http\Controllers\UserController; //Importación de la clase UserController para definir las rutas de usuarios
use App\Http\Controllers\FamiliaController; //Importación de la clase FamiliaController para definir las rutas de familias
use App\Http\Controllers\Niveles_academicosController; //Importación de la clase Niveles_academicosController para definir las rutas de niveles académicos
use App\Http\Controllers\EspecialidadesController; //Importación de la clase EspecialidadesController para definir las rutas de especialidades
use App\Http\Controllers\AsignaturasController; //Importación de la clase AsignaturasController para definir las rutas de asignaturas
use App\Http\Controllers\Periodos_lectivosController; //Importación de la clase Periodos_lectivosController para definir las rutas de periodos lectivos
use App\Http\Controllers\CursosController; //Importación de la clase CursosController para definir las rutas de cursos
use App\Http\Controllers\Curso_AsignaturasController; //Importación de la clase Curso_AsignaturasController para definir las rutas de curso_asignaturas
use App\Http\Controllers\HorariosController; //Importación de la clase HorariosController para definir las rutas de horarios
use App\Http\Controllers\Cronograma_matriculasController; //Importación de la clase Cronograma_matriculasController para definir las rutas de cronograma_matriculas
use App\Http\Controllers\AsistenciaController; //Importación de la clase AsistenciaController para definir las rutas de asistencia
use App\Http\Controllers\Control_SubidaNotasController; //Importación de la clase Control_SubidaNotasController para definir las rutas de control_subida_notas
use App\Http\Controllers\LandingController; //Importación de la clase LandingController para definir las rutas de landing
use App\Http\Controllers\EstudianteNotasController; //Importación de la clase EstudianteNotasController para definir las rutas de estudiantes_notas
use App\Http\Controllers\EstudianteMatriculaHistorialController; //Importación de la clase EstudianteMatriculaHistorialController para definir las rutas de estudiantes_matriculas

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Aqui se definen las rutas de la API del sistema,
| las rutas se cargan mediante el proveedor de rutas y todas ellas se cargarán en el archivo app/Http/routes.php
  Cada ruta se define mediante la función Route::get(), Route::post(), Route::put(), Route::delete(), Route::patch(), Route::any(), Route::prefix(), Route::group(), y Route::resource().
  Cada ruta define una ruta específica para un recurso, un controlador, o una acción de un controlador.
  Las rutas se definen en el archivo app/Http/routes.php, donde se definen las rutas de la API del sistema.
  Estas rutas se encapsulan con el prefix 'sistma', lo que significa que las rutas de la API del sistema se encuentran en la ruta '/sistma'.
| Para proporciconar seguridad, se utiliza el middleware 'auth:api', que verifica que el usuario esté autenticado y autorizado para acceder a la ruta.
| Para definir rutas de la API, se utiliza la función Route::apiResource(), que se encarga de definir una ruta de recurso para un controlador específico.
| 
| 
|
*/
//Este middleware se encarga de verificar que el usuario esté autenticado y autorizado para acceder a la ruta (actualmente no se utiliza)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Definición de las rutas para el sistema
 * Las rutas definidas aquí se encapsulan con el prefix 'sistma', lo que significa que las rutas de la API del sistema se encuentran en la ruta '/sistma'.  
 * Para usarla se debe agregar en el frontend la ruta debe ser 'http://backmilenio.test/api/sistma', por defecto la url es 'http://backmilenio.test'.
 * Si compras un dominio personalizado y subes el backend al servidor, la url debe ser 'http://nombre-del-dominio.com/api/sistma'.
 * Si la ruta api dice post, significa que se va a enviar datos en el body de la petición, 
 * si dice get, significa que se va a obtener datos desde el body de la petición, 
 * si dice put, significa que se va a actualizar datos en el body de la petición, 
 * si dice delete, significa que se va a eliminar datos en el body de la petición,
 * Si una ruta usa por ejemplo imagenpersona/{ci}, el {ci} es un parámetro que se va a usar en la ruta.
 */
Route::prefix('sistma')->group(function () {//Definición de la ruta 'sistma' que se encapsula con el middleware 'auth:sanctum'
    /**
     * Rutas sin autenticación
     * Las rutas de esta sección no requieren autenticación y no requieren autorización.
     * Estas rutas servirán para obtener la foto de la persona y mandar la solicitud para el inicio de sesión.
     */
    Route::get('imagenpersona/{ci}', [PersonaController::class, 'getFotografia'])->middleware('throttle:10000,1'); //Obtener la foto de la persona, se usa el controlador PersonaController y la acción getFotografia, con un límite de peticiones de 10000 por minuto
    Route::post('/login', [AuthController::class, 'login']);//Iniciar sesión, se usa el controlador AuthController y la acción login

    /**
     * Rutas con autenticación
     * Las rutas de esta sección requieren autenticación y no requieren autorización.
     * Estas rutas son más delicadas y por ende requieren autenticación y autorización.
     * Estas rutas se encapsulan con el middleware 'auth:api', que verifica que el usuario esté autenticado y autorizado para acceder a la ruta.
     * Estas rutas serán usadas por diferentes tipos de usuarios, como administradores, docentes, estudiantes, etc.
     */
    Route::middleware('auth:api')->group(function () { //Definición de la ruta 'sistma' que se encapsula con el middleware 'auth:api'
        //Cerrar sesión, se usa el controlador AuthController y la acción logout
        Route::get('/logout', [AuthController::class, 'logout']);
        //Actualizar la sesión, se usa el controlador AuthController y la acción refresh
        Route::post('/refresh', [AuthController::class, 'refresh']);
        //Obtener la información del usuario actual, se usa el controlador AuthController y la acción me
        Route::get('/me', [AuthController::class, 'me']);
        /**
         * Definición de las rutas para los roles, permitiendo crear, leer, actualizar roles, eliminar roles y habilitar roles, se usa el controlador RolController
         * Te encontrarás con prefijos como ApiResource, get, post, put, delete, patch, any, prefix, group, resource, etc.
         * Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar roles  
         * Las rutas con get, post, put, delete, patch, any, prefix, group, resource, etc. permiten crear, leer, actualizar y eliminar roles
         */
        Route::apiResource('roles', RolController::class); //Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar roles  
        //Ihabilitar un rol, se usa el controlador RolController y la acción destroy
        Route::delete('ihabilitar_role/{id}', [RolController::class, 'destroy']);
        //Habilitar un rol, se usa el controlador RolController y la acción habilitar
        Route::delete('habilitar_role/{id}', [RolController::class, 'habilitar']);
        //Obtener los roles habilitados, se usa el controlador RolController y la acción Roleshabilitados
        Route::get('roleshabilitados', [RolController::class, 'Roleshabilitados']);
        /**
         * Definición de las rutas para las personas, permitiendo crear, leer, actualizar personas, eliminar personas y habilitar personas, se usa el controlador PersonaController
         * Te encontrarás con prefijos como ApiResource, get, post, put, delete, patch, any, prefix, group, resource, etc.
         * Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar personas
         * Las rutas con get, post, put, delete, patch, any, prefix, group, resource, etc. permiten crear, leer, actualizar y eliminar personas
         */
        Route::apiResource('personas', PersonaController::class)->middleware('throttle:10000,1'); //Las rutas con API Resource se encapsulan con el middleware 'throttle:10000,1', lo que significa que se limitan a 10000 peticiones por minuto, permitiendo por defecto crear, leer, actualizar y eliminar personas
        //Ihabilitar una persona, se usa el controlador PersonaController y la acción destroy
        Route::delete('ihabilitar_persona/{id}', [PersonaController::class, 'destroy']);
        //Habilitar una persona, se usa el controlador PersonaController y la acción habilitar
        Route::delete('habilitar_persona/{id}', [PersonaController::class, 'habilitar']);
        /**
         * Definición de las rutas para los usuarios, permitiendo crear, leer, actualizar usuarios, eliminar usuarios y habilitar usuarios, se usa el controlador UserController   
         * Te encontrarás con prefijos como ApiResource, get, post, put, delete, patch, any, prefix, group, resource, etc.
         * Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar usuarios
         * Las rutas con get, post, put, delete, patch, any, prefix, group, resource, etc. permiten crear, leer, actualizar y eliminar usuarios
         */
        Route::apiResource('usuarios', UserController::class); //Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar usuarios
        //Ihabilitar un usuario, se usa el controlador UserController y la acción destroy
        Route::delete('ihabilitar_usuario/{id}', [UserController::class, 'destroy']);
        //Habilitar un usuario, se usa el controlador UserController y la acción habilitar
        Route::delete('habilitar_usuario/{id}', [UserController::class, 'habilitar']);
        //Crear un usuario, se usa el controlador UserController y la acción storeUsuario
        Route::post('usuarios/store', [UserController::class, 'storeUsuario']);
        //Actualizar un usuario, se usa el controlador UserController y la acción updateUsuario
        Route::put('usuarios/update/{id}', [UserController::class, 'updateUsuario']);
        //Crear un usuarios masivos, se usa el controlador UserController y la acción storeMasivo 
        Route::post('usuarios/store_masivo', [UserController::class, 'storeMasivo']);
        //Resetear la clave de un usuario, se usa el controlador UserController y la acción resetearClave
        Route::post('resetear_clave/{id}', [UserController::class, 'resetearClave']);
        //Obtener a todos los usuarios que no tienen un registro en la tabla 'usuarios' y que tienen una edad inferior a 20, se usa el controlador UserController y la acción getPendientesMasivo
        Route::get('pendientes_masivo', [UserController::class, 'getPendientesMasivo']);
        //Obtener a todos los usuarios que no tienen un registro en la tabla 'usuarios' y que tienen una edad mayor a 20, se usa el controlador UserController y la acción getPendientesMayoresMasivo
        Route::get('pendientes_mayores_masivo', [UserController::class, 'getPendientesMayoresMasivo']);

        /**
         * Definición de las rutas para la familia, permitiendo crear, leer, actualizar familia, eliminar familia y asignar familia, se usa el controlador FamiliaController
         * Te encontrarás con prefijos como ApiResource, get, post, put, delete, patch, any, prefix, group, resource, etc.
         * Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar familia
         * Las rutas con get, post, put, delete, patch, any, prefix, group, resource, etc. permiten crear, leer, actualizar y eliminar familia  
         */
        Route::apiResource('familia', FamiliaController::class); //Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar familia
        //Obtener la familia de una persona, se usa el controlador FamiliaController y la acción show
        Route::get('familiar/{ci}', [FamiliaController::class, 'show']);
        //Asignar una familia a una persona, se usa el controlador FamiliaController y la acción store
        Route::post('familia/asignar', [FamiliaController::class, 'store']);
        //Obtener las familias de una persona, se usa el controlador FamiliaController y la acción getFamiliaresByPersona   
        Route::get('familiares-de/{id_persona}', [FamiliaController::class, 'getFamiliaresByPersona']);
        //Obtener las familias de una persona que tienen un registro en la tabla 'cursos', se usa el controlador FamiliaController y la acción getFamiliaresEstByPersona
        Route::get('familiares-est-de/{id_persona}', [FamiliaController::class, 'getFamiliaresEstByPersona']);
        /**
         * Definición de las rutas para los niveles académicos, permitiendo crear, leer, actualizar niveles académicos, eliminar niveles académicos y habilitar niveles académicos, se usa el controlador Niveles_academicosController
         * Te encontrarás con prefijos como ApiResource, get, post, put, delete, patch, any, prefix, group, resource, etc.
         * Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar niveles académicos
         * Las rutas con get, post, put, delete, patch, any, prefix, group, resource, etc. permiten crear, leer, actualizar y eliminar niveles académicos
         */
        Route::apiResource('niveles_academicos', Niveles_academicosController::class); //Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar niveles académicos
        //Obtener los niveles académicos habilitados, se usa el controlador Niveles_academicosController y la acción getActivados
        Route::get('niveles_academicos_activos', [Niveles_academicosController::class, 'getActivados']);
        //Ihabilitar un nivel, se usa el controlador Niveles_academicosController y la acción destroy
        Route::delete('ihabilitar_nivel/{id}', [Niveles_academicosController::class, 'destroy']);
        //Habilitar un nivel, se usa el controlador Niveles_academicosController y la acción habilitar
        Route::delete('habilitar_nivel/{id}', [Niveles_academicosController::class, 'habilitar']);
        /**
         * Definición de las rutas para los especialidades, permitiendo crear, leer, actualizar especialidades, eliminar especialidades y habilitar especialidades, se usa el controlador EspecialidadesController
         * Te encontrarás con prefijos como ApiResource, get, post, put, delete, patch, any, prefix, group, resource, etc.
         * Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar especialidades
         * Las rutas con get, post, put, delete, patch, any, prefix, group, resource, etc. permiten crear, leer, actualizar y eliminar especialidades
         */
        Route::apiResource('especialidades', EspecialidadesController::class); //Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar especialidades
        //Obtener los especialidades habilitados, se usa el controlador EspecialidadesController y la acción getActivados
        Route::get('especialidades_activos', [EspecialidadesController::class, 'getActivados']);
        //Ihabilitar una especialidad, se usa el controlador EspecialidadesController y la acción destroy   
        Route::delete('ihabilitar_especialidad/{id}', [EspecialidadesController::class, 'destroy']);
        //Habilitar una especialidad, se usa el controlador EspecialidadesController y la acción habilitar
        Route::delete('habilitar_especialidad/{id}', [EspecialidadesController::class, 'habilitar']);
        /**
         * Definición de las rutas para los asignaturas, permitiendo crear, leer, actualizar asignaturas, eliminar asignaturas y habilitar asignaturas, se usa el controlador AsignaturasController
         * Te encontrarás con prefijos como ApiResource, get, post, put, delete, patch, any, prefix, group, resource, etc.
         * Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar asignaturas
         * Las rutas con get, post, put, delete, patch, any, prefix, group, resource, etc. permiten crear, leer, actualizar y eliminar asignaturas
         */
        Route::apiResource('asignaturas', AsignaturasController::class); //Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar asignaturas
        //Obtener los asignaturas habilitados, se usa el controlador AsignaturasController y la acción getActivados
        Route::get('asignaturas_activos', [AsignaturasController::class, 'getActivados']);
        //Ihabilitar una asignatura, se usa el controlador AsignaturasController y la acción destroy
        Route::delete('ihabilitar_asignatura/{id}', [AsignaturasController::class, 'destroy']);
        //Habilitar una asignatura, se usa el controlador AsignaturasController y la acción habilitar   
        Route::delete('habilitar_asignatura/{id}', [AsignaturasController::class, 'habilitar']);
        /**
         * Definición de las rutas para los periodos lectivos, permitiendo crear, leer, actualizar periodos lectivos, eliminar periodos lectivos y habilitar periodos lectivos, se usa el controlador Periodos_lectivosController
         * Te encontrarás con prefijos como ApiResource, get, post, put, delete, patch, any, prefix, group, resource, etc.
         * Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar periodos lectivos
         * Las rutas con get, post, put, delete, patch, any, prefix, group, resource, etc. permiten crear, leer, actualizar y eliminar periodos lectivos
         */
        Route::apiResource('periodos_lectivos', Periodos_lectivosController::class); //Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar periodos lectivos
        //Obtener los periodos lectivos habilitados, se usa el controlador Periodos_lectivosController y la acción getActivados
        Route::get('periodos_lectivos_activos', [Periodos_lectivosController::class, 'getActivados']);
        //Ihabilitar un periodo, se usa el controlador Periodos_lectivosController y la acción destroy
        Route::delete('ihabilitar_periodo_lectivo/{id}', [Periodos_lectivosController::class, 'destroy']);
        //Habilitar un periodo, se usa el controlador Periodos_lectivosController y la acción habilitar
        Route::delete('habilitar_periodo_lectivo/{id}', [Periodos_lectivosController::class, 'habilitar']);
        /**
         * Definición de las rutas para los cursos, permitiendo crear, leer, actualizar cursos, eliminar cursos y habilitar cursos, se usa el controlador CursosController
         * Te encontrarás con prefijos como ApiResource, get, post, put, delete, patch, any, prefix, group, resource, etc.
         * Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar cursos
         * Las rutas con get, post, put, delete, patch, any, prefix, group, resource, etc. permiten crear, leer, actualizar y eliminar cursos
         */
        Route::apiResource('cursos', CursosController::class); //Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar cursos
        //Obtener los cursos habilitados, se usa el controlador CursosController y la acción getActivados
        Route::get('cursos_activos', [CursosController::class, 'getActivados']);
        //Obtener los cursos de un docente, se usa el controlador CursosController y la acción getCursosDocente
        Route::get('cursos_docente/{id}', [CursosController::class, 'getCursosDocente']);
        //Ihabilitar un curso, se usa el controlador CursosController y la acción destroy
        Route::delete('ihabilitar_curso/{id}', [CursosController::class, 'destroy']);
        //Habilitar un curso, se usa el controlador CursosController y la acción habilitar
        Route::delete('habilitar_curso/{id}', [CursosController::class, 'habilitar']);
        //Desasignar un docente de un curso, se usa el controlador CursosController y la acción desasignarDocente
        Route::delete('desasignar_docente_curso/{id}', [CursosController::class, 'desasignarDocente']);
        //Obtener la carga academica de un docente, se usa el controlador CursosController y la acción getCargaAcademica
        Route::get('docente/carga-academica/{id_persona}', [CursosController::class, 'getCargaAcademica']);
        //Verificar si un docente tiene familia, se usa el controlador CursosController y la acción verificarTutor
        Route::get('verificar-tutor', [CursosController::class, 'verificarTutor']);
        //Verificar si un docente tiene familia, se usa el controlador CursosController y la acción TieneFamilia
        Route::get('tiene-familia', [CursosController::class, 'TieneFamilia']);
        //Reasignacion masiva de asignaturas, se usa el controlador CursosController y la acción reasignacionMasiva
        Route::post('reasignacion_masiva', [CursosController::class, 'reasignacionMasiva']);
        /**
         * Definición de las rutas para los curso_asignaturas, permitiendo crear, leer, actualizar curso_asignaturas, eliminar curso_asignaturas y habilitar curso_asignaturas, se usa el controlador Curso_AsignaturasController
         * Te encontrarás con prefijos como ApiResource, get, post, put, delete, patch, any, prefix, group, resource, etc.
         * Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar curso_asignaturas
         * Las rutas con get, post, put, delete, patch, any, prefix, group, resource, etc. permiten crear, leer, actualizar y eliminar curso_asignaturas
         */
        Route::apiResource('curso_asignaturas', Curso_AsignaturasController::class); //Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar curso_asignaturas
        //Inhabilitar un curso_asignatura, se usa el controlador Curso_AsignaturasController y la acción destroy
        Route::delete('ihabilitar_curso_asignatura/{id}', [Curso_AsignaturasController::class, 'destroy']);
        //Habilitar un curso_asignatura, se usa el controlador Curso_AsignaturasController y la acción habilitar
        Route::delete('habilitar_curso_asignatura/{id}', [Curso_AsignaturasController::class, 'habilitar']);
        //Desasignar un docente de un curso_asignatura, se usa el controlador Curso_AsignaturasController y la acción desasignarDocente
        Route::delete('desasignar_docente_curso_asignatura/{id}', [Curso_AsignaturasController::class, 'desasignarDocente']);
        //Obtener los curso_asignaturas de un docente, se usa el controlador Curso_AsignaturasController y la acción getPorDocente
        Route::get('curso_asignaturas/docente/{id}', [Curso_AsignaturasController::class, 'getPorDocente']);
        //Crear un horario de un docente, se usa el controlador HorariosController y la acción guardarHorario
        Route::post('curso_asignaturas_lote/crear', [Curso_AsignaturasController::class, 'procesarAsignaciones']);
        //Actualizar un horario de un docente, se usa el controlador HorariosController y la acción guardarHorario
        Route::put('curso_asignaturas_lote/actualizar', [Curso_AsignaturasController::class, 'procesarAsignaciones']);
        /**
         * Definición de las rutas para los horarios_clases, permitiendo crear, leer, actualizar horarios_clases, eliminar horarios_clases y habilitar horarios_clases, se usa el controlador HorariosController
         * Te encontrarás con prefijos como ApiResource, get, post, put, delete, patch, any, prefix, group, resource, etc.
         * Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar horarios_clases    
         * Las rutas con get, post, put, delete, patch, any, prefix, group, resource, etc. permiten crear, leer, actualizar y eliminar horarios_clases
         */
        Route::apiResource('horarios_clases', HorariosController::class); //Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar horarios_clases
        //Crear un horario de un docente, se usa el controlador HorariosController y la acción guardarHorario
        Route::post('crearhorario', [HorariosController::class, 'guardarHorario']);
        //Obtener los horarios de un docente, se usa el controlador HorariosController y la acción getHorarioDocente
        Route::get('horarios_docente/{id_persona}', [HorariosController::class, 'getHorarioDocente']);
        /**
         * Definición de las rutas para los cronograma_matriculas, permitiendo crear, leer, actualizar cronograma_matriculas, eliminar cronograma_matriculas y habilitar cronograma_matriculas, se usa el controlador Cronograma_matriculasController
         * Te encontrarás con prefijos como ApiResource, get, post, put, delete, patch, any, prefix, group, resource, etc.
         * Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar cronograma_matriculas
         * Las rutas con get, post, put, delete, patch, any, prefix, group, resource, etc. permiten crear, leer, actualizar y eliminar cronograma_matriculas
         */
        Route::apiResource('cronograma_matriculas', Cronograma_matriculasController::class); //Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar cronograma_matriculas
        //Crear un cronograma de matriculas, se usa el controlador Cronograma_matriculasController y la acción store
        Route::post('crearcronograma_matriculas', [Cronograma_matriculasController::class, 'store']);
        //Obtener los cronogramas de matriculas activos, se usa el controlador Cronograma_matriculasController y la acción getCronogramaActivo
        Route::get('cronograma_matriculas_activo/{id_estudiante}', [Cronograma_matriculasController::class, 'getCronogramaActivo']);
        //Obtener los cursos de un cronograma, se usa el controlador Cronograma_matriculasController y la acción getCursosPorCronograma
        Route::get('cursos_por_cronograma/{id_cronograma}', [Cronograma_matriculasController::class, 'getCursosPorCronograma']);
        //Crear una matricula, se usa el controlador Cronograma_matriculasController y la acción crearmatricula
        Route::post('crearmatricula', [Cronograma_matriculasController::class, 'crearmatricula']);
        //Obtener los historiales de un representante, se usa el controlador Cronograma_matriculasController y la acción getHistorial
        Route::get('historial/{id_representante}', [Cronograma_matriculasController::class, 'getHistorial']);
        //Obtener los cursos de un representante, se usa el controlador Cronograma_matriculasController y la acción getCursosMatriculados
        Route::get('matriculas-representante/{id_representante}', [Cronograma_matriculasController::class, 'getCursosMatriculados']);
        //Obtener los estudiantes de una asignatura, se usa el controlador Cronograma_matriculasController y la acción getEstudiantesPorAsignatura
        Route::get('estudiantes-asignatura/{id_docente}', [Cronograma_matriculasController::class, 'getEstudiantesPorAsignatura']);
        //Obtener los historiales de una asignatura, se usa el controlador Cronograma_matriculasController y la acción buscarHistorialMatriculas
        Route::get('buscar-historial-matriculas/{cedula}', [Cronograma_matriculasController::class, 'buscarHistorialMatriculas']);
        //Crear el historial externo de un estudiante nuevo, se usa el controlador Cronograma_matriculasController y la acción storeHistorialExterno
        Route::post('historial_externo', [Cronograma_matriculasController::class, 'storeHistorialExterno']);
        //Obtener los niveles exteriores, se usa el controlador Cronograma_matriculasController y la acción listadoNivelesExteriores
        Route::get('niveles_exteriores', [Cronograma_matriculasController::class, 'listadoNivelesExteriores']);
        //Anular una matricula, se usa el controlador Cronograma_matriculasController y la acción anularMatricula
        Route::put('anular_matricula/{id}', [Cronograma_matriculasController::class, 'anularMatricula']);
        /**
         * Definición de las rutas para los asistencias, permitiendo crear, leer, actualizar asistencias, eliminar asistencias y habilitar asistencias, se usa el controlador AsistenciaController
         * Te encontrarás con prefijos como ApiResource, get, post, put, delete, patch, any, prefix, group, resource, etc.
         * Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar asistencias
         * Las rutas con get, post, put, delete, patch, any, prefix, group, resource, etc. permiten crear, leer, actualizar y eliminar asistencias
         */
        //Crear asistencias masivas hoy, se usa el controlador AsistenciaController y la acción guardarConducta
        Route::post('asistencias-hoy', [AsistenciaController::class, 'storeMasivo']);
        //Verificar si una asistencia de hoy existe, se usa el controlador AsistenciaController y la acción checkAsistenciaHoy
        Route::get('asistencia-check/{id_curso_asignatura}', [AsistenciaController::class, 'checkAsistenciaHoy']);
        //Obtener los historiales de una asistencia, se usa el controlador AsistenciaController y la acción getHistorialAsistencia
        Route::get('historial-asistencia/{id_docente}', [AsistenciaController::class, 'getHistorialAsistencia']);
        //Obtener los datos de un docente, se usa el controlador AsistenciaController y la acción getDatosTutor 
        Route::get('datos-tutor/{id_persona}', [AsistenciaController::class, 'getDatosTutor']);
        //Obtener los datos de las notas de un alumno, se usa el controlador AsistenciaController y la acción getDatosNotasAlumnoTutor  
        Route::get('datos-notas-alumno-tutor/{id_persona}', [AsistenciaController::class, 'getDatosNotasAlumnoTutor']);
        //Calificar la conducta de un estudiante, se usa el controlador AsistenciaController y la acción guardarConducta
        Route::post('guardar_conducta', [AsistenciaController::class, 'guardarConducta']);
        //Obtener los datos de un docente, se usa el controlador AsistenciaController y la acción getDatosConduta
        Route::get('datos-tutor-conducta/{id_persona}', [AsistenciaController::class, 'getDatosConduta']);
        /**
         * Definición de las rutas para los control_subida_notas, permitiendo crear, leer, actualizar control_subida_notas, eliminar control_subida_notas y habilitar control_subida_notas, se usa el controlador Control_SubidaNotasController
         * Te encontrarás con prefijos como ApiResource, get, post, put, delete, patch, any, prefix, group, resource, etc.
         * Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar control_subida_notas
         * Las rutas con get, post, put, delete, patch, any, prefix, group, resource, etc. permiten crear, leer, actualizar y eliminar control_subida_notas
         */
        Route::apiResource('control_subida_notas', Control_SubidaNotasController::class); //Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar control_subida_notas
        //Habilitar un control_subida_notas, se usa el controlador Control_SubidaNotasController y la acción habilitar
        Route::delete('habilitar_control_subida_notas/{id}', [Control_SubidaNotasController::class, 'habilitar']);
        //Obtener las asignaturas de un docente, se usa el controlador Control_SubidaNotasController y la acción getAsignaturasDocente
        Route::get('asignaturas-docente/{id_docente}', [Control_SubidaNotasController::class, 'getAsignaturasDocente']);
        //Obtener los estudiantes de una asignatura, se usa el controlador Control_SubidaNotasController y la acción getEstudiantesAsignatura   
        Route::get('estudiantes-asigna/{id_curso_asignatura}', [Control_SubidaNotasController::class, 'getEstudiantesAsignatura']);
        //Crear calificaciones, se usa el controlador Control_SubidaNotasController y la acción guardarCalificaciones
        Route::post('calificaciones-guardar', [Control_SubidaNotasController::class, 'guardarCalificaciones']);
        //Obtener las calificaciones de un estudiante, se usa el controlador Control_SubidaNotasController y la acción getCalificacionesActuales
        Route::get('calificaciones-actuales/{id_estudiante}', [Control_SubidaNotasController::class, 'getCalificacionesActuales']);
        //Obtener los historiales de notas de un estudiante, se usa el controlador Control_SubidaNotasController y la acción buscarPorCedula    
        Route::get('historico_notas/{cedula}', [Control_SubidaNotasController::class, 'buscarPorCedula']);
        /**
         * Definición de las rutas para el landing, permitiendo crear, leer, actualizar landing, eliminar landing y habilitar landing, se usa el controlador LandingController
         * Te encontrarás con prefijos como ApiResource, get, post, put, delete, patch, any, prefix, group, resource, etc.
         * Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar landing
         * Las rutas con get, post, put, delete, patch, any, prefix, group, resource, etc. permiten crear, leer, actualizar y eliminar landing
         */
        //Obtener la informacion inicial, se usa el controlador LandingController y la acción getInformacionInicio
        Route::get('informacion-inicio', [LandingController::class, 'getInformacionInicio']);
        /**
         * Definición de las rutas para los estudiantes_notas, permitiendo crear, leer, actualizar estudiantes_notas, eliminar estudiantes_notas y habilitar estudiantes_notas, se usa el controlador EstudianteNotasController
         * Te encontrarás con prefijos como ApiResource, get, post, put, delete, patch, any, prefix, group, resource, etc.
         * Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar estudiantes_notas
         * Las rutas con get, post, put, delete, patch, any, prefix, group, resource, etc. permiten crear, leer, actualizar y eliminar estudiantes_notas
         */
        //Obtener las notas de un estudiante, se usa el controlador EstudianteNotasController y la acción getNotasHistorial
        Route::get('mis-notas-est/{id_persona}', [EstudianteNotasController::class, 'getNotasHistorial']);
        //Obtener los historiales completos de notas de un estudiante, se usa el controlador EstudianteNotasController y la acción getHistorialCompleto
        Route::get('historial-completo-est/{id_persona}', [EstudianteNotasController::class, 'getHistorialCompleto']);
        /**
         * Definición de las rutas para los estudiantes_matriculas, permitiendo crear, leer, actualizar estudiantes_matriculas, eliminar estudiantes_matriculas y habilitar estudiantes_matriculas, se usa el controlador EstudianteMatriculaHistorialController
         * Te encontrarás con prefijos como ApiResource, get, post, put, delete, patch, any, prefix, group, resource, etc.
         * Las rutas con API Resource permiten por defecto crear, leer, actualizar y eliminar estudiantes_matriculas
         * Las rutas con get, post, put, delete, patch, any, prefix, group, resource, etc. permiten crear, leer, actualizar y eliminar estudiantes_matriculas
         */
        //Obtener los historiales de matriculas de un estudiante, se usa el controlador EstudianteMatriculaHistorialController y la acción getHistorialMatriculas
        Route::get('historial-matriculas-est/{id_persona}', [EstudianteMatriculaHistorialController::class, 'getHistorialMatriculas']);
    });
});
