<?php

namespace App\Dto\Order;

final readonly class OrderInputDto
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $phone,
        public string $email,
        public string $city,
        public string $address,
        public ?string $comment,
        public string $deliveryCarrier,
        public string $deliveryType,
        public ?string $deliveryPoint,
        public string $paymentMethod,
        public bool $createAccount,
        public ?string $password,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            phone: $data['phone'],
            email: $data['email'],
            city: $data['city'],
            address: $data['address'],
            comment: isset($data['comment']) && $data['comment'] !== '' ? $data['comment'] : null,
            deliveryCarrier: $data['delivery_carrier'],
            deliveryType: $data['delivery_type'],
            deliveryPoint: $data['delivery_point'] ?? null,
            paymentMethod: $data['payment_method'],
            createAccount: (bool) ($data['create_account'] ?? false),
            password: $data['password'] ?? null,
        );
    }
}
