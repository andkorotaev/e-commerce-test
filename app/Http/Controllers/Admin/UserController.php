<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Services\UserService;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        protected UserService $users,
        protected OrderService $orders,
    ) {}

    public function index(): View
    {
        return view('admin.users.index', [
            'users' => $this->users->all(),
        ]);
    }

    public function show(int $userId): View
    {
        $user = $this->users->find($userId);

        abort_if($user === null, 404);

        return view('admin.users.show', [
            'user' => $user,
            'orders' => $this->orders->forUser($userId),
        ]);
    }
}
