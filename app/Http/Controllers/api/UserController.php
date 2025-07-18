<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role === 'admin') {
            return response()->json(User::all());
        }
        
        return response()->json([$request->user()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'sometimes|in:user,manager,admin'
        ]);

        // Verifica se é uma requisição autenticada
        if ($request->user()) {
            // Se autenticado, verifica permissões para roles especiais
            if (isset($validated['role']) && $validated['role'] !== 'user' && $request->user()->role !== 'admin') {
                return response()->json(['error' => 'Apenas admins podem criar usuários com roles especiais'], 403);
            }
        } else {
            // Se for registro público, força role como 'user'
            $validated['role'] = 'user';
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = $validated['role'] ?? 'user';

        $user = User::create($validated);
        
        // Se for registro público, gera um token de acesso
        if (!$request->user()) {
            $token = $user->createToken('auth-token')->plainTextToken;
            return response()->json([
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ], 201);
        }
        
        return response()->json($user, 201);
    }

    public function show(Request $request, User $user)
    {
        if ($request->user()->role !== 'admin' && $request->user()->id !== $user->id) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        if ($request->user()->role !== 'admin' && $request->user()->id !== $user->id) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8',
            'role' => 'sometimes|in:user,manager,admin'
        ]);

        if (isset($validated['role']) && $request->user()->role !== 'admin') {
            return response()->json(['error' => 'Apenas admins podem alterar roles'], 403);
        }

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);
        return response()->json($user);
    }

    public function destroy(Request $request, User $user)
    {
        if ($request->user()->role !== 'admin' && $request->user()->id !== $user->id) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'Usuário excluído com sucesso']);
    }
}