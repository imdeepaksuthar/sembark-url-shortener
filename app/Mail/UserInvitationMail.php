<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $companyName,
        public string $token
    ) {
    }

    public function build(): static
    {
        return $this->subject(
            'Invitation to join ' . $this->companyName
        )->view('emails.invitation', [
                    'companyName' => $this->companyName,
                    'url' => route('invitations.accept.form', [
                        'token' => $this->token,
                    ]),
                ]);
    }
}
