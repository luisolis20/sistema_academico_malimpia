<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AsistenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {}
    public function checkAsistenciaHoy($id_curso_asignatura)
    {
        $existe = Asistencia::where('id_curso_asignatura', $id_curso_asignatura)
            ->where('fecha', date('Y-m-d'))
            ->exists();

        return response()->json(['registrada' => $existe]);
    }

    public function storeMasivo(Request $request)
    {
        $asistencias = $request->input('asistencias'); // Array de objetos
        $fechaHoy = date('Y-m-d');
        $id_curso_asignatura = $request->input('id_curso_asignatura');

        // Validación de seguridad: No duplicar si ya se tomó hoy
        $yaExiste = Asistencia::where('id_curso_asignatura', $id_curso_asignatura)
            ->where('fecha', $fechaHoy)
            ->exists();

        if ($yaExiste) {
            return response()->json(['error' => 'La asistencia para esta asignatura ya fue registrada hoy.'], 422);
        }

        foreach ($asistencias as $asig) {
            Asistencia::create([
                'id_matricula' => $asig['id_matricula'],
                'id_curso_asignatura' => $id_curso_asignatura,
                'fecha' => $fechaHoy,
                'estado' => $asig['estado'],
            ]);
        }

        return response()->json(['msj' => 'Asistencia registrada correctamente']);
    }



    /**
     * Store a newly created resource in storage.
     */


    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {}

    public function habilitar(string $id) {}
}
