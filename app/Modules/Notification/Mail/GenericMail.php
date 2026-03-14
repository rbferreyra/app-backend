<?php

namespace App\Modules\Notification\Mail;

use App\Modules\Notification\DTOs\NotificationDTO;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GenericMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly NotificationDTO $dto,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->dto->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: $this->dto->template,
            with: $this->dto->data,
        );
    }
}
