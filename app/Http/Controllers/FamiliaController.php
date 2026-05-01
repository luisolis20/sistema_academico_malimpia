<?php

namespace App\Http\Controllers;

use App\Models\Personas;
use App\Models\User;
use App\Models\Familia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FamiliaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perPage = min($request->input('per_page', 10), 20);
            $searchQuery = $request->input('search_query');

            // 1. Consulta Principal: Obtenemos SOLO a las personas (sin duplicados)
            $query = Personas::select(
                'personas.id_persona as personID',
                'personas.cedula',
                'personas.nombres',
                'personas.apellidos',
                'personas.foto',
                'personas.fecha_nacimiento',
                'personas.sexo'
            )
                ->join('usuarios', 'usuarios.id_persona', '=', 'personas.id_persona')
                ->whereRaw('TIMESTAMPDIFF(YEAR, personas.fecha_nacimiento, CURDATE()) >= 20')
                ->where('usuarios.estado', 1);

            if (! empty($searchQuery)) {
                $query->where(function ($q) use ($searchQuery) {
                    $q->where('personas.cedula', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('personas.nombres', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('personas.apellidos', 'LIKE', "%{$searchQuery}%");
                });
            }

            // Paginamos limpio, sin filas duplicadas
            $data = $query->paginate($perPage);

            if ($data->isEmpty()) {
                return response()->json(['data' => [], 'message' => 'No se encontraron datos'], 200);
            }

            // 2. Extraemos los IDs de las personas de esta página para buscar a sus familiares
            $personIds = collect($data->items())->pluck('personID')->toArray();

            // 3. Buscamos los familiares de estas personas e incluimos sus datos personales (nombres, cédula)
            $familiaresRaw = \Illuminate\Support\Facades\DB::table('familia')
                ->join('personas', 'familia.id_estudiante', '=', 'personas.id_persona')
                ->whereIn('familia.id_representante', $personIds)
                ->select(
                    'familia.id_representante',
                    'familia.id_estudiante',
                    'familia.parentesco',
                    'personas.cedula',
                    'personas.nombres',
                    'personas.apellidos'
                )
                ->get();

            // Agrupamos la lista de familiares usando el ID del representante
            $familiaresAgrupados = $familiaresRaw->groupBy('id_representante');

            // 4. Transformamos la data para inyectar el arreglo de familiares a cada persona
            $data->getCollection()->transform(function ($item) use ($familiaresAgrupados) {
                // Dependiendo de cómo devuelva paginate, extraemos los atributos
                $attributes = is_array($item) ? $item : $item->getAttributes();

                // Procesamiento de fotos y codificación
                foreach ($attributes as $key => $value) {
                    if ($key === 'foto' && ! empty($value)) {
                        $attributes[$key] = base64_encode($value);
                    } elseif (is_string($value) && $key !== 'foto') {
                        $attributes[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                    }
                }

                // Inyectamos el arreglo de familiares. Si no tiene, se envía un arreglo vacío []
                $idPersona = $attributes['personID'];
                $attributes['familiares'] = isset($familiaresAgrupados[$idPersona])
                    ? $familiaresAgrupados[$idPersona]->toArray()
                    : [];

                return $attributes;
            });

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
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }



    /**
     * Store a newly created resource in storage.
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
        DB::beginTransaction();

        try {
            $idRepresentante = $request->input('id_representante');
            $familiares = $request->input('familiares');

            // Extraer todos los IDs de los familiares que vienen del Frontend
            $idsFamiliaresNuevos = collect($familiares)->pluck('personID')->toArray();

            // PASO CLAVE: Eliminar de la BD los familiares que ya no están en la lista del Frontend
            Familia::where('id_representante', $idRepresentante)
                ->whereNotIn('id_estudiante', $idsFamiliaresNuevos)
                ->delete();

            // 3. Crear o actualizar los que sí vienen
            foreach ($familiares as $familiar) {
                // Evitamos que una persona sea representante de sí misma
                if ($idRepresentante == $familiar['personID']) {
                    continue;
                }

                Familia::updateOrCreate(
                    [
                        'id_representante' => $idRepresentante,
                        'id_estudiante'    => $familiar['personID'],
                    ],
                    [
                        'parentesco'       => $familiar['parentesco'],
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Familia guardada correctamente.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al asignar la familia.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    public function getFamiliaresByPersona($id_persona)
    {
        // Buscamos los familiares donde el ID enviado sea el representante
        $familiares = Familia::with(['familiapersona2']) // Cargamos la relación del familiar
            ->where('id_representante', $id_persona)
            ->get();

        if ($familiares->isEmpty()) {
            return response()->json([
                'error' => false,
                'data' => [],
                'mensaje' => 'No posee familia asignada'
            ]);
        }

        // Transformamos los datos para incluir la foto en base64
        $data = $familiares->map(function ($f) {
            $persona = $f->familiapersona2;
            return [
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

        return response()->json([
            'error' => false,
            'data' => $data
        ]);
    }
    public function getFamiliaresEstByPersona($id_persona)
    {
        // Buscamos los familiares donde el ID enviado sea el representante
        $familiares = Familia::with(['familiapersona1']) // Cargamos la relación del familiar
            ->where('id_estudiante', $id_persona)
            ->get();

        if ($familiares->isEmpty()) {
            return response()->json([
                'error' => false,
                'data' => [],
                'mensaje' => 'No posee familia asignada'
            ]);
        }

        // Transformamos los datos para incluir la foto en base64
        $data = $familiares->map(function ($f) {
            $persona = $f->familiapersona1;
            if($f->parentesco == 'Hijo/a' && $persona->sexo == 'Femenino'){
                $nuevoparentesco = 'Mamá';
            }
            else if($f->parentesco == 'Hijo/a' && $persona->sexo == 'Masculino'){
                $nuevoparentesco = 'Padre';
            }else if($f->parentesco == 'Sobrino/a' && $persona->sexo == 'Femenino'){
                $nuevoparentesco = 'Tía';
            }else if($f->parentesco == 'Sobrino/a' && $persona->sexo == 'Masculino'){
                $nuevoparentesco = 'Tío';
            }else if($f->parentesco == 'Nieto/a' && $persona->sexo == 'Femenino'){
                $nuevoparentesco = 'Abuela';
            }else if($f->parentesco == 'Nieto/a' && $persona->sexo == 'Masculino'){
                $nuevoparentesco = 'Abuelo';
            }else{
                $nuevoparentesco = $f->parentesco;
            }
            return [
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

        return response()->json([
            'error' => false,
            'data' => $data
        ]);
    }

    /**
     * Display the specified resource.
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
        )
            ->join('usuarios', 'usuarios.id_persona', '=', 'personas.id_persona')
            // CORRECCIÓN 1: Menores de 20 años (< 20)
            ->whereRaw('TIMESTAMPDIFF(YEAR, personas.fecha_nacimiento, CURDATE()) < 20')
            ->where('usuarios.estado', 1)
            ->where('personas.cedula', $id);

        $data = $query->first(); // Obtenemos el objeto directamente

        // CORRECCIÓN 2: Evaluamos si es nulo (no usamos isEmpty)
        if (!$data) {
            return response()->json(['data' => [], 'message' => 'No se encontraron datos'], 200);
        }

        // CORRECCIÓN 3: Limpieza de datos directamente sobre el objeto
        $attributes = $data->getAttributes();
        foreach ($attributes as $key => $value) {
            if ($key === 'foto' && !empty($value)) {
                $attributes[$key] = base64_encode($value);
            } elseif (is_string($value) && $key !== 'foto') {
                $attributes[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            }
        }

        // Reemplazamos los atributos limpios
        $data->setRawAttributes($attributes);

        return response()->json([
            'data' => $data,
            'mensaje' => 'Encontrado con Éxito!!',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {}

    /**
     * Remove the specified resource from storage.
     */
}
