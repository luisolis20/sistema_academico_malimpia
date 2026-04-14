<?php

namespace App\Http\Controllers;

use App\Models\Personas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perPage = min($request->input('per_page', 10), 20);
            $searchQuery = $request->input('search_query');
            $status = $request->input('status'); // <-- Nuevo parámetro
            $filterAge = $request->input('filter_age');

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
            )
                ->leftJoin('usuarios', 'usuarios.id_persona', '=', 'personas.id_persona')
                ->leftJoin('roles', 'roles.id_rol', '=', 'usuarios.id_rol');

            if (! empty($searchQuery)) {
                $query->where(function ($q) use ($searchQuery) {
                    $q->where('personas.cedula', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('personas.nombres', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('personas.apellidos', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('roles.nombre', 'LIKE', "%{$searchQuery}%");
                });
            }

            if (! empty($status)) {
                $query->where('usuarios.estado', 'LIKE', "{$status}");
            }
            if ($filterAge === 'under_20') {
                $query->whereRaw('TIMESTAMPDIFF(YEAR, personas.fecha_nacimiento, CURDATE()) < 20');
            } elseif ($filterAge === 'over_20') {
                $query->whereRaw('TIMESTAMPDIFF(YEAR, personas.fecha_nacimiento, CURDATE()) >= 20');
            }


            $data = $query->paginate($perPage);

            if ($data->isEmpty()) {
                return response()->json(['data' => [], 'message' => 'No se encontraron datos'], 200);
            }

            $data->getCollection()->transform(function ($item) {
                $attributes = $item->getAttributes();
                foreach ($attributes as $key => $value) {
                    if ($key === 'foto' && ! empty($value)) {
                        $attributes[$key] = base64_encode($value);
                    } elseif (is_string($value) && $key !== 'foto') {
                        $attributes[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                    }
                }

                return $attributes;
            });

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
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function getPendientesMasivo(Request $request)
    {
        try {
            // Traemos a TODOS los que no tienen usuario y son menores de 20
            $personas = Personas::select('personas.id_persona as personID', 'personas.cedula')
                ->leftJoin('usuarios', 'usuarios.id_persona', '=', 'personas.id_persona')
                ->whereNull('usuarios.id_usuario')
                ->whereRaw('TIMESTAMPDIFF(YEAR, personas.fecha_nacimiento, CURDATE()) < 20')
                ->get();

            return response()->json(['data' => $personas], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    public function getPendientesMayoresMasivo(Request $request)
    {
        try {
            // Traemos a TODOS los que no tienen usuario y son menores de 20
            $personas = Personas::select('personas.id_persona as personID', 'personas.cedula')
                ->leftJoin('usuarios', 'usuarios.id_persona', '=', 'personas.id_persona')
                ->whereNull('usuarios.id_usuario')
                ->whereRaw('TIMESTAMPDIFF(YEAR, personas.fecha_nacimiento, CURDATE()) >= 20')
                ->get();

            return response()->json(['data' => $personas], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }


    // 2. NUEVO ENDPOINT: Crear un solo usuario (desde el Modal)
    public function storeUsuario(Request $request)
    {
        try {
            $existe = DB::table('usuarios')->where('id_persona', $request->id_persona)->exists();
            if ($existe) {
                return response()->json(['error' => 'Esta persona ya tiene un usuario asignado.'], 400);
            }

            $usuario = DB::table('usuarios')->insert([
                'id_persona' => $request->id_persona,
                'id_rol' => $request->id_rol,
                'username' => $request->username,
                'clave' => Hash::make($request->username), // Clave por defecto = cedula
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['message' => 'Usuario creado exitosamente.'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear usuario: ' . $e->getMessage()], 500);
        }
    }
    public function updateUsuario(Request $request, $id)
    {
        try {
            // 1. Verificamos que el usuario que intentan editar realmente exista
            $usuarioExiste = DB::table('usuarios')->where('id_usuario', $id)->exists();
            if (!$usuarioExiste) {
                return response()->json(['error' => 'Usuario no encontrado.'], 404);
            }

            // 2. (Opcional pero recomendado) Verificar que el nuevo username no lo tenga otro usuario
            $usernameOcupado = DB::table('usuarios')
                ->where('username', $request->username)
                ->where('id_usuario', '!=', $id) // Excluimos al usuario actual
                ->exists();

            if ($usernameOcupado) {
                return response()->json(['error' => 'Este nombre de usuario ya está en uso por otra persona.'], 400);
            }

            // 3. Ejecutamos la actualización
            DB::table('usuarios')
                ->where('id_usuario', $id)
                ->update([
                    'id_persona' => $request->id_persona,
                    'id_rol' => $request->id_rol,
                    'username' => $request->username,
                    'clave' => Hash::make($request->username), // Clave por defecto = cedula
                    'estado' => 1,
                    'updated_at' => now(),
                ]);

            return response()->json(['message' => 'Usuario actualizado exitosamente.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar usuario: ' . $e->getMessage()], 500);
        }
    }

    // 3. NUEVO ENDPOINT: Registro Masivo por lotes (Chunks)
    public function storeMasivo(Request $request)
    {
        $personas = $request->input('personas'); // Array de [personID, cedula]
        $errores = [];
        $registrados = 0;

        // Buscar el id_rol para 'estudiante'. Si no existe, pon un ID por defecto (ej: 3)
        $rolEstudiante = DB::table('roles')->where('nombre', 'LIKE', '%estudiante%')
            ->where('estado', 1)->first();
        $idRol = $rolEstudiante ? $rolEstudiante->id_rol : 3;

        foreach ($personas as $p) {
            try {
                $existe = DB::table('usuarios')->where('id_persona', $p['personID'])->exists();
                if (! $existe) {
                    DB::table('usuarios')->insert([
                        'id_persona' => $p['personID'],
                        'id_rol' => $idRol,
                        'username' => $p['cedula'],
                        'clave' => Hash::make($p['cedula']),
                        'estado' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $registrados++;
                }
            } catch (\Exception $e) {
                $errores[] = $p['cedula'];
            }
        }

        return response()->json([
            'registrados' => $registrados,
            'errores' => $errores,
        ], 200);
    }
    public function resetearClave(Request $request, string $id)
    {
        try {
            $request->validate([
                'nueva_clave' => 'required|string'
            ]);

            // Cambia 'Usuarios' por el nombre de tu modelo si es diferente (ej. User)
            $usuario = User::findOrFail($id);
            $usuario->clave = Hash::make($request->nueva_clave); // Hasheamos la cédula

            if ($usuario->save()) {
                return response()->json([
                    'data' => $usuario,
                    'mensaje' => 'Clave reseteada correctamente',
                ]);
            } else {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error al resetear clave',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $inputs = $request->input();
        $inputs['clave'] = Hash::make($inputs['clave']);
        $res = User::create($inputs);

        return response()->json([
            'data' => $res,
            'mensaje' => 'Agregado con Éxito!!',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $res = User::find($id);
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
                'mensaje' => "El Usuario con id: $id no Existe",
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $res = User::find($id);
        if (isset($res)) {
            $res->id_persona = $request->id_persona;
            $res->id_rol = $request->id_rol;
            $res->username = $request->username;
            $res->clave = Hash::make($request->clave);
            $res->estado = $request->estado;
            if ($res->save()) {
                return response()->json([
                    'data' => $res,
                    'mensaje' => 'Actualizado con Éxito!!',
                ]);
            } else {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error al Actualizar',
                ]);
            }
        } else {
            return response()->json([
                'error' => true,
                'mensaje' => "El Usuario con id: $id no Existe",
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $res = User::find($id);
        if (isset($res)) {

            $res->estado = 0;
            if ($res->save()) {
                return response()->json([
                    'data' => $res,
                    'mensaje' => 'Usuario Deshabilitado',
                ]);
            } else {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error al Eliminar',
                ]);
            }
        } else {
            return response()->json([
                'error' => true,
                'mensaje' => "El Usuario con id: $id no Existe",
            ]);
        }
    }

    public function habilitar(string $id)
    {
        $res = User::find($id);
        if (isset($res)) {

            $res->estado = 1;
            if ($res->save()) {
                return response()->json([
                    'data' => $res,
                    'mensaje' => 'Usuario Habilitado',
                ]);
            } else {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error al Eliminar',
                ]);
            }
        } else {
            return response()->json([
                'error' => true,
                'mensaje' => "El Usuario con id: $id no Existe",
            ]);
        }
    }
}
