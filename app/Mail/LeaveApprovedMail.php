<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeaveApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $detail;

    public function __construct($detail)
    {
        $this->detail = $detail;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Leave Application Status',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.leave-approved',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
