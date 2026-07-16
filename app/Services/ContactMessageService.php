<?php

namespace App\Services;

use App\Dto\Contact\ContactMessageDto;
use App\Dto\Contact\ContactMessageInputDto;
use App\Repositories\ContactMessageRepository;
use Illuminate\Support\Collection;
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

    /**
     * @return Collection<int, ContactMessageDto>
     */
    public function all(): Collection
    {
        return $this->messages->all();
    }

    /**
     * @throws RuntimeException
     */
    public function delete(int $id): void
    {
        try {
            $this->messages->delete($id);
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException("Failed to delete contact message #{$id}.", previous: $e);
        }
    }
}
