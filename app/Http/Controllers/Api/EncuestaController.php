<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use App\Services\EnviadorMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EncuestaController extends Controller
{
    private const CLAVE = [
        'activo' => [3, 5, 7, 9, 13, 20, 26, 27, 35, 37, 41, 43, 46, 48, 51, 61, 67, 74, 75, 77],
        'reflexivo' => [10, 16, 18, 19, 28, 31, 32, 34, 36, 39, 42, 44, 49, 55, 58, 63, 65, 69, 70, 79],
        'teorico' => [2, 4, 6, 11, 15, 17, 21, 23, 25, 29, 33, 45, 50, 54, 60, 64, 66, 71, 78, 80],
        'pragmatico' => [1, 8, 12, 14, 22, 24, 30, 38, 40, 47, 52, 53, 56, 57, 59, 62, 68, 72, 73, 76],
    ];

    private const NOMBRES = ['activo' => 'Activo', 'reflexivo' => 'Reflexivo', 'teorico' => 'Teórico', 'pragmatico' => 'Pragmático'];

    // Un solo POST: datos del estudiante + 80 respuestas (sin sesiones)
    public function guardar(Request $request)
    {
        $reglas = [
            'nombre' => 'required|string|max:255',
            'segundo_nombre' => 'nullable|string|max:255',
            'apellido' => 'required|string|max:255',
            'segundo_apellido' => 'nullable|string|max:255',
            'correo' => 'nullable|email|max:255',
            'curso' => 'required|string|max:50',
        ];
        for ($i = 1; $i <= 80; $i++) {
            $reglas["respuestas.$i"] = 'required|in:0,1';
        }
        $datos = $request->validate($reglas);

        $puntajes = [];
        foreach (self::CLAVE as $estilo => $preguntas) {
            $puntajes[$estilo] = array_sum(array_map(fn ($p) => (int) $datos['respuestas'][$p], $preguntas));
        }

        $max = max($puntajes);
        $ganadores = array_map(fn ($e) => self::NOMBRES[$e], array_keys($puntajes, $max));

        $estudiante = Estudiante::create([
            'nombre' => $datos['nombre'],
            'segundo_nombre' => $datos['segundo_nombre'] ?? null,
            'apellido' => $datos['apellido'],
            'segundo_apellido' => $datos['segundo_apellido'] ?? null,
            'correo' => $datos['correo'] ?? null,
            'curso' => $datos['curso'],
            'estilo_aprendizaje' => implode(' y ', $ganadores),
            'puntaje_activo' => $puntajes['activo'],
            'puntaje_reflexivo' => $puntajes['reflexivo'],
            'puntaje_teorico' => $puntajes['teorico'],
            'puntaje_pragmatico' => $puntajes['pragmatico'],
        ]);
        $estudiante->forceFill(['token' => (string) Str::uuid()])->save();

        return response()->json($this->payload($estudiante), 201);
    }

    public function resultado(string $token)
    {
        return response()->json($this->payload(Estudiante::where('token', $token)->firstOrFail()));
    }

    public function correo(Request $request, string $token, EnviadorMaterial $enviador)
    {
        $estudiante = Estudiante::where('token', $token)->firstOrFail();
        $request->validate(['correo' => 'required|email']);

        $estudiante->update(['correo' => $request->correo]);

        try {
            $enviador->enviar($estudiante->correo);
            $estudiante->update(['correo_enviado' => true]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => 'No se pudo enviar el correo. Intenta de nuevo más tarde.'], 502);
        }

        return response()->json(['message' => 'El material fue enviado correctamente a tu correo.']);
    }

    private function payload(Estudiante $e): array
    {
        return [
            'token' => $e->token,
            'nombre_completo' => trim("$e->nombre $e->segundo_nombre $e->apellido $e->segundo_apellido"),
            'curso' => $e->curso,
            'correo' => $e->correo,
            'estilo_aprendizaje' => $e->estilo_aprendizaje,
            'puntajes' => [
                'Activo' => $e->puntaje_activo,
                'Reflexivo' => $e->puntaje_reflexivo,
                'Teórico' => $e->puntaje_teorico,
                'Pragmático' => $e->puntaje_pragmatico,
            ],
        ];
    }
}
