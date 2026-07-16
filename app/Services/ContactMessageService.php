<?php

namespace App\Services;

use App\Dto\Contact\ContactMessageDto;
use App\Dto\Contact\ContactMessageInputDto;
use App\Repositories\ContactMessageRepository;
use RuntimeException;
use Throwable;

class ContactMessageService
{
    public function __construct(protected ContactMessageRepository $messages) {}

    /**
     * @throws RuntimeException
     */
    public function submit(ContactMessageInputDto $dto): ContactMessageDto
    {
        try {
            return $this->messages->create([
                'name' => $dto->name,
                'email' => $dto->email,
                'phone' => $dto->phone,
                'message' => $dto->message,
            ]);
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException('Failed to submit contact message.', previous: $e);
        }
    }
}
