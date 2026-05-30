<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Markdown\Components\Button;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $otp)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre code de vérification (OTP) - EduManager'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-otp',
            with: ['otp' => $this->otp]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

