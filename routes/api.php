<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

//verificar se a api está executando
Route::get('/status', [APIControllerntroller::class, 'status']);

// autenticar no sistema
Route::post('/login', function (Request $request) {
    try {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Login realizado com sucesso.',
            'token' => $token,
            'user' => $user,
        ]);
    } catch (\Throwable $e) {
        \Log::error('Erro no login API: ' . $e->getMessage());
        return response()->json(['error' => 'Erro interno no servidor'], 500);
    }
});

//sair do sistema
Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Logout realizado com sucesso.']);
});

//informações sobre o login autenticado no momento (minhas infos)
Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return response()->json($request->user());
});

// informação importante, usuario root padrão do sistema criado no arquivo .env

Route::middleware('auth:sanctum')->post('/register-users', function (Request $request) {
    try {
        $user = auth()->user();

        if (!$user || !$user->is_admin) {
            return response()->json([
                'message' => 'Acesso negado. Apenas administradores podem cadastrar usuários.'
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'setor' => 'nullable|string|max:255',
            'password' => 'required|string|min:6',
            'is_admin' => 'boolean',
        ]);

        $newUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'setor' => $request->setor,
            'password' => Hash::make($request->password),
            'is_admin' => $request->boolean('is_admin'),
        ]);

        return response()->json([
            'message' => 'Usuário criado com sucesso.',
            'user' => $newUser,
        ], 201);
    } catch (\Throwable $e) {
        return response()->json([
            'message' => 'Erro interno no servidor.',
            'error' => $e->getMessage(), // você pode remover isso em produção
        ], 500);
    }
});
