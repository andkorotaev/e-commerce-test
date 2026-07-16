<?php

namespace App\Repositories;

use App\Dto\Contact\ContactMessageDto;
use App\Models\ContactMessage;

class ContactMessageRepository
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): ContactMessageDto
    {
        return ContactMessageDto::fromModel(ContactMessage::create($attributes));
    }
}
