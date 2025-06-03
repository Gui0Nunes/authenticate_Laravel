<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

Route::get('/status', [APIControllerntroller::class, 'status']);

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



