<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ContactMessageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function __construct(protected ContactMessageService $messages) {}

    public function index(): View
    {
        return view('admin.contact-messages.index', [
            'messages' => $this->messages->all(),
        ]);
    }

    public function destroy(int $messageId): RedirectResponse
    {
        $this->messages->delete($messageId);

        return redirect()->route('admin.contact-messages.index');
    }
}
