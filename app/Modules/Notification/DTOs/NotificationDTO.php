<?php

namespace App\Modules\Notification\DTOs;

use App\Shared\DTOs\BaseDTO;

class NotificationDTO extends BaseDTO
{
    public function __construct(
        public readonly string $type,
        public readonly string $subject,
        public readonly string $template,
        public readonly array $data = [],
    ) {
    }

    public static function fromArray(array $data): static
    {
        return new static(
            type: $data['type'],
            subject: $data['subject'],
            template: $data['template'],
            data: $data['data'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'subject' => $this->subject,
            'template' => $this->template,
            'data' => $this->data,
        ];
    }
}
