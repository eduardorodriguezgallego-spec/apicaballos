<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function registro(Request $request)
    {
        // 1. Validación
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email', // Cambiado a 'users' si esa es tu tabla
            'password' => 'required|min:6',
            'telefono' => 'required|string|max:20'
        ]);

        // 2. Creación del usuario
        // Nota: Asegúrate de que en tu base de datos la columna se llame 'name' o 'nombre'
        $usuario = User::create([
            'name'     => $request->nombre, 
            'email'    => $request->email,
            'password' => Hash::make($request->password), // Hash::make es más seguro/estándar que bcrypt
            'telefono' => $request->telefono,
            'rol'      => 'usuario' // Si tienes esta columna en tu tabla
        ]);

        // 3. Generar Token
        $token = $usuario->createToken('api-token')->plainTextToken;

        return response()->json([
            'mensaje' => 'Usuario registrado correctamente',
            'token'   => $token,
            'usuario' => $usuario
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $usuario = User::where('email', $request->email)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            return response()->json([
                'mensaje' => 'Credenciales incorrectas'
            ], 401);
        }

        $token = $usuario->createToken('api-token')->plainTextToken;

        return response()->json([
            'mensaje' => 'Login correcto',
            'token'   => $token,
            'usuario' => $usuario
        ]);
    }

    public function logout(Request $request)
    {
        // Revoca el token que se está usando
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'mensaje' => 'Logout correcto'
        ]);
    }

    public function usuario(Request $request)
    {
        return response()->json($request->user());
    }
}