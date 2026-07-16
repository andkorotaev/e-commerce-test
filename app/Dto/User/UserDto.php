<?php

namespace App\Dto\User;

use App\Models\User;
use Carbon\Carbon;

final readonly class UserDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public Carbon $createdAt,
        public int $ordersCount,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            createdAt: $user->created_at,
            ordersCount: $user->orders_count ?? 0,
        );
    }
}
