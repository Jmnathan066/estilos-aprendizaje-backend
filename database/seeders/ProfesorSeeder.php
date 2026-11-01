<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class ProfesorSeeder extends Seeder
{
    // Credenciales desde variables de entorno (ya no van escritas en el código)
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => env('PROFESOR_USER', 'profesor')],
            ['password' => env('PROFESOR_PASS', 'cambiar-esta-clave')]
        );
    }
}
