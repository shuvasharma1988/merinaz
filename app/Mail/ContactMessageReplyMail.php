<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessageReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactMessage $contactMessage
    ) {}

    public function envelope(): Envelope
    {
        $topic = $this->contactMessage->topic ?: 'Your Inquiry';

        return new Envelope(
            subject: "Re: {$topic} - Merinaz Salon",
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: nl2br(e($this->contactMessage->reply_message))
            .'<hr><p style="color:#666;font-size:12px;">Original message:<br>'
            .nl2br(e($this->contactMessage->message)).'</p>',
        );
    }
}
