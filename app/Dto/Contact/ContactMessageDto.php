<?php

namespace App\Dto\Contact;

use App\Models\ContactMessage;
use Carbon\Carbon;

final readonly class ContactMessageDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public ?string $phone,
        public string $message,
        public Carbon $createdAt,
    ) {}

    public static function fromModel(ContactMessage $contactMessage): self
    {
        return new self(
            id: $contactMessage->id,
            name: $contactMessage->name,
            email: $contactMessage->email,
            phone: $contactMessage->phone,
            message: $contactMessage->message,
            createdAt: $contactMessage->created_at,
        );
    }
}
