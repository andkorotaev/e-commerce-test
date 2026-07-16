<?php

namespace App\Services;

use App\Dto\User\UserDto;
use App\Repositories\UserRepository;
use Illuminate\Support\Collection;

class UserService
{
    public function __construct(protected UserRepository $users) {}

    /**
     * @return Collection<int, UserDto>
     */
    public function all(): Collection
    {
        return $this->users->all();
    }

    public function find(int $id): ?UserDto
    {
        return $this->users->find($id);
    }
}
