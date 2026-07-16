<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\StoreContactMessageRequest;
use App\Services\ContactMessageService;
use Illuminate\Http\RedirectResponse;

class ContactController extends Controller
{
    public function __construct(protected ContactMessageService $messages) {}

    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        $this->messages->submit($request->getDto());

        return back()->with('status', 'contact-message-sent');
    }
}
