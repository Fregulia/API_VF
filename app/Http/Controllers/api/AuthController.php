<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json(['message' => 'Logout realizado com sucesso']);
    }

    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();
        
        return response()->json(['message' => 'Logout de todos os dispositivos realizado com sucesso']);
    }

    public function revokeToken(Request $request)
    {
        $request->validate([
            'revoke_all' => 'boolean'
        ]);

        if ($request->revoke_all) {
            $request->user()->tokens()->delete();
            return response()->json(['message' => 'Todos os tokens foram revogados']);
        }

        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Token atual revogado']);
    }
}