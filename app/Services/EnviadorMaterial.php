<?php

namespace App\Services;

use App\Mail\MaterialEstilosAprendizaje;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

/**
 * Si existe BREVO_API_KEY envía por HTTPS (funciona en Render gratis, que bloquea SMTP).
 * Si no, usa el mailer normal de Laravel (Gmail SMTP en local).
 */
class EnviadorMaterial
{
    private const ARCHIVOS = [
        'Estilos-de-Aprendizaje-Guia-Estudiantes.pdf',
        'reporte-estilos-honey-alonso.pdf',
    ];

    public function enviar(string $destino): void
    {
        $key = env('BREVO_API_KEY');

        if (!$key) {
            Mail::to($destino)->send(new MaterialEstilosAprendizaje());
            return;
        }

        $adjuntos = array_map(fn ($f) => [
            'name' => $f,
            'content' => base64_encode(file_get_contents(storage_path('app/public/documentos/'.$f))),
        ], self::ARCHIVOS);

        Http::withHeaders(['api-key' => $key])->acceptJson()->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => ['email' => env('MAIL_FROM_ADDRESS'), 'name' => env('MAIL_FROM_NAME', 'Estilos de Aprendizaje')],
            'to' => [['email' => $destino]],
            'subject' => 'Material de apoyo - Estilos de Aprendizaje',
            'htmlContent' => view('emails.material-estilos')->render(),
            'attachment' => $adjuntos,
        ])->throw();
    }
}
