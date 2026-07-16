<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Order\UpdateOrderStatusRequest;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orders) {}

    public function index(): View
    {
        return view('admin.orders.index', [
            'orders' => $this->orders->all(),
        ]);
    }

    public function show(int $orderId): View
    {
        $order = $this->orders->find($orderId);

        abort_if($order === null, 404);

        return view('admin.orders.show', [
            'order' => $order,
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, int $orderId): RedirectResponse
    {
        $this->orders->updateStatus($orderId, $request->string('status')->value());

        return redirect()->route('admin.orders.show', $orderId);
    }
}
