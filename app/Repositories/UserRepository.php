<?php

namespace App\Repositories;

use App\Dto\User\UserDto;
use App\Models\User;
use Illuminate\Support\Collection;

class UserRepository
{
    /**
     * Every registered customer, newest first — the admin user list.
     * withCount avoids an N+1 for the orders-count column shown per row.
     *
     * @return Collection<int, UserDto>
     */
    public function all(): Collection
    {
        return User::withCount('orders')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (User $user) => UserDto::fromModel($user));
    }

    public function find(int $id): ?UserDto
    {
        $user = User::withCount('orders')->find($id);

        return $user ? UserDto::fromModel($user) : null;
    }
}
