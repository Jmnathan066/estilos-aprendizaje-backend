<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MaterialEstilosAprendizaje extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Material de apoyo - Estilos de Aprendizaje',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.material-estilos',
        );
    }

    public function attachments(): array
    {
        return [
            \Illuminate\Mail\Mailables\Attachment::fromPath(
                storage_path('app/public/documentos/Estilos-de-Aprendizaje-Guia-Estudiantes.pdf')
            )->as('Estilos-de-Aprendizaje-Guia-Estudiantes.pdf')
             ->withMime('application/pdf'),

            \Illuminate\Mail\Mailables\Attachment::fromPath(
                storage_path('app/public/documentos/reporte-estilos-honey-alonso.pdf')
            )->as('reporte-estilos-honey-alonso.pdf')
             ->withMime('application/pdf'),
        ];
    }
}