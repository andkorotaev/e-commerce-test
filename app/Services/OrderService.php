<?php

namespace App\Services;

use App\Dto\Cart\CartLineDto;
use App\Dto\Order\OrderDto;
use App\Dto\Order\OrderInputDto;
use App\Models\User;
use App\Repositories\OrderItemRepository;
use App\Repositories\OrderRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Throwable;

class OrderService
{
    public function __construct(
        protected OrderRepository $orders,
        protected OrderItemRepository $orderItems,
        protected CartService $cart,
    ) {}

    /**
     * @return Collection<int, OrderDto>
     */
    public function forUser(int $userId): Collection
    {
        return $this->orders->forUser($userId);
    }

    public function find(int $id): ?OrderDto
    {
        return $this->orders->find($id);
    }

    /**
     * @return Collection<int, OrderDto>
     */
    public function all(): Collection
    {
        return $this->orders->all();
    }

    /**
     * @throws RuntimeException
     */
    public function updateStatus(int $orderId, string $status): void
    {
        try {
            $this->orders->updateStatus($orderId, $status);
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException("Failed to update order #{$orderId} status.", previous: $e);
        }
    }

    /**
     * Places an order from the current cart. For a guest who checked
     * "create an account", a real account is created and logged into first
     * (so the order can be attached to it like any other logged-in
     * checkout) — but only once the whole thing, including the order
     * itself, has committed successfully; a failed order shouldn't leave a
     * dangling account with nothing attached to it.
     *
     * @throws RuntimeException
     */
    public function checkout(OrderInputDto $dto, ?User $user): OrderDto
    {
        $summary = $this->cart->summary();

        if ($summary->lines->isEmpty()) {
            throw new RuntimeException('Cannot check out an empty cart.');
        }

        $newAccount = null;

        try {
            $order = DB::transaction(function () use ($dto, $summary, $user, &$newAccount) {
                if ($user === null && $dto->createAccount && $dto->password !== null) {
                    $newAccount = User::create([
                        'name' => trim("{$dto->firstName} {$dto->lastName}"),
                        'email' => $dto->email,
                        'password' => Hash::make($dto->password),
                    ]);
                }

                $orderDto = $this->orders->create([
                    'user_id' => $user?->id ?? $newAccount?->id,
                    'first_name' => $dto->firstName,
                    'last_name' => $dto->lastName,
                    'phone' => $dto->phone,
                    'email' => $dto->email,
                    'city' => $dto->city,
                    'address' => $dto->address,
                    'comment' => $dto->comment,
                    'delivery_carrier' => $dto->deliveryCarrier,
                    'delivery_type' => $dto->deliveryType,
                    'delivery_point' => $dto->deliveryPoint,
                    'payment_method' => $dto->paymentMethod,
                    'subtotal' => $summary->subtotal,
                    'discount' => $summary->discount,
                    'delivery_fee' => $summary->delivery,
                    'total' => $summary->total,
                    'status' => 'new',
                ]);

                foreach ($summary->lines as $line) {
                    /** @var CartLineDto $line */
                    $this->orderItems->create($orderDto->id, [
                        'product_id' => $line->productId,
                        'product_variant_id' => $line->variantId,
                        'name' => $line->name,
                        'variant_label' => $line->variantLabel,
                        'image' => $line->image,
                        'unit_price' => $line->unitPrice,
                        'quantity' => $line->quantity,
                        'line_total' => $line->lineTotal(),
                    ]);
                }

                return $this->orders->find($orderDto->id);
            });
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException('Failed to place order.', previous: $e);
        }

        // Only clear the cart and log a newly-created account in once the
        // order has actually committed — an order that failed to save
        // shouldn't also cost the visitor their cart contents.
        $this->cart->clear();

        if ($newAccount) {
            Auth::guard('web')->login($newAccount);
        }

        return $order;
    }
}
