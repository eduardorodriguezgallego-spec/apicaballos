<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Caballo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CaballoController extends Controller
{
    public function index()
    {
        $caballos = Caballo::orderBy('nombre')->get();

        return response()->json($caballos);
    }

    public function show($id)
    {
        $caballo = Caballo::find($id);

        if (!$caballo) {
            return response()->json([
                'mensaje' => 'Caballo no encontrado'
            ], 404);
        }

        return response()->json($caballo);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'raza' => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'enfermo' => 'boolean',
            'observaciones' => 'nullable|string',
        ]);

        $rutaFoto = null;

        if ($request->hasFile('foto')) {
            $rutaFoto = $request->file('foto')->store('caballos', 'public');
        }

        $caballo = Caballo::create([
            'nombre' => $request->nombre,
            'raza' => $request->raza,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'foto' => $rutaFoto,
            'enfermo' => $request->enfermo ?? false,
            'observaciones' => $request->observaciones,
        ]);

        return response()->json([
            'mensaje' => 'Caballo creado correctamente',
            'caballo' => $caballo
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $caballo = Caballo::find($id);

        if (!$caballo) {
            return response()->json([
                'mensaje' => 'Caballo no encontrado'
            ], 404);
        }

        $request->validate([
            'nombre' => 'sometimes|string|max:100',
            'raza' => 'sometimes|string|max:100',
            'fecha_nacimiento' => 'sometimes|date',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'enfermo' => 'sometimes|boolean',
            'observaciones' => 'nullable|string',
        ]);

        $datos = $request->except('foto');

        if ($request->hasFile('foto')) {

            // borrar foto anterior
            if ($caballo->foto) {
                Storage::disk('public')->delete($caballo->foto);
            }

            $datos['foto'] = $request->file('foto')->store('caballos', 'public');
        }

        $caballo->update($datos);

        return response()->json([
            'mensaje' => 'Caballo actualizado correctamente',
            'caballo' => $caballo
        ]);
    }

    public function destroy($id)
    {
        $caballo = Caballo::find($id);

        if (!$caballo) {
            return response()->json([
                'mensaje' => 'Caballo no encontrado'
            ], 404);
        }

        // borrar foto
        if ($caballo->foto) {
            Storage::disk('public')->delete($caballo->foto);
        }

        $caballo->delete();

        return response()->json([
            'mensaje' => 'Caballo eliminado correctamente'
        ]);
    }
}