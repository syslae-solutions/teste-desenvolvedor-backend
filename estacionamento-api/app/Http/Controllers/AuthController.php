<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User; // importar o User Model
use Illuminate\Support\Facades\Hash; // Para hashing de senhas

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Use Hash::make
        ]);

        $token = $user->createToken('auth_token', ['*']); // Nome do token e permissões

        return response()->json([
            'message' => 'Usuário registrado com sucesso!',
            'token' => $token->plainTextToken,
            'user' => $user,
        ], 201); // 201 Created
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        $user = $request->user();

        // revogar tokens antigos para garantir um único token ativo por vez
        $user->tokens()->delete();

        $token = $user->createToken('auth_token', ['*']); // Nome do token e permissões

        return response()->json([
            'message' => 'Login bem-sucedido!',
            'token' => $token->plainTextToken,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        // Revoga o token atual do usuário
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout bem-sucedido!'], 200);
    }
}