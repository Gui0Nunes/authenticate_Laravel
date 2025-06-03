<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
     public function run(): void
    {

        // Validar variáveis de ambiente
        $adminData = [
            'name' => env('ADMIN_NAME'),
            'email' => env('ADMIN_EMAIL'),
            'password' => env('ADMIN_PASSWORD'),
            'setor' => env('ADMIN_SETOR'),
        ];

        $validator = Validator::make($adminData, [
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => ['required', 'string', 'min:8'], // força senha forte
            'setor' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new \Exception('Erro ao criar admin: variáveis de ambiente inválidas ou ausentes.');
        }

        User::create([
            'name' => $adminData['name'],
            'email' => $adminData['email'],
            'password' => Hash::make($adminData['password']),
            'setor' => $adminData['setor'],
            'is_admin' => true,
        ]);

    }
}
