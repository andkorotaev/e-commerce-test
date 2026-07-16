<?php

namespace App\Repositories;

use App\Dto\Contact\ContactMessageDto;
use App\Models\ContactMessage;
use Illuminate\Support\Collection;

class ContactMessageRepository
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): ContactMessageDto
    {
        return ContactMessageDto::fromModel(ContactMessage::create($attributes));
    }

    /**
     * Every submitted message, newest first — the admin inbox.
     *
     * @return Collection<int, ContactMessageDto>
     */
    public function all(): Collection
    {
        return ContactMessage::orderByDesc('created_at')
            ->get()
            ->map(fn (ContactMessage $message) => ContactMessageDto::fromModel($message));
    }

    public function delete(int $id): void
    {
        ContactMessage::whereKey($id)->delete();
    }
}
