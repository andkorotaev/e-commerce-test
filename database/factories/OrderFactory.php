<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 200, 5000);

        return [
            'user_id' => null,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone' => '+380'.fake()->numerify('#########'),
            'email' => fake()->safeEmail(),
            'city' => fake()->randomElement(['Київ', 'Харків', 'Одеса', 'Дніпро', 'Львів']),
            'address' => fake()->streetAddress(),
            'comment' => null,
            'delivery_carrier' => fake()->randomElement(array_keys(config('shop.delivery_carriers'))),
            'delivery_type' => fake()->randomElement(array_keys(config('shop.delivery_types'))),
            'delivery_point' => fake()->numberBetween(1, 100),
            'payment_method' => fake()->randomElement(array_keys(config('shop.payment_methods'))),
            'subtotal' => $subtotal,
            'discount' => 0,
            'delivery_fee' => 0,
            'total' => $subtotal,
            'status' => 'new',
        ];
    }
}
