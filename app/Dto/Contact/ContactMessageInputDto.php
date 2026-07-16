<?php

namespace App\Dto\Contact;

final readonly class ContactMessageInputDto
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone,
        public string $message,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'] ?? null,
            message: $data['message'],
        );
    }
}
