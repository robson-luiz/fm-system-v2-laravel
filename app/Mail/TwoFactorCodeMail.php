<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TwoFactorCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $code;
    public $expiresInMinutes;
    public $ipAddress;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $code, int $expiresInMinutes = 5, string $ipAddress = null)
    {
        $this->user = $user;
        $this->code = $code;
        $this->expiresInMinutes = $expiresInMinutes;
        $this->ipAddress = $ipAddress ?: request()->ip();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Código de Verificação - FM System',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'emails.two-factor-code',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}