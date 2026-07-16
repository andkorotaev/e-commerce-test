<?php

namespace Tests\Feature\Front;

use Tests\TestCase;

class DeliveryPointTest extends TestCase
{
    public function test_returns_branches_for_a_known_city(): void
    {
        $response = $this->get(route('front.checkout.delivery-points', ['city' => 'Київ', 'type' => 'branch']));

        $response->assertOk();
        $this->assertNotEmpty($response->json());
    }

    public function test_falls_back_to_default_points_for_an_unknown_city(): void
    {
        $response = $this->get(route('front.checkout.delivery-points', ['city' => 'Мукачево', 'type' => 'postomat']));

        $response->assertOk();
        $this->assertSame(config('delivery_points.postomat.default'), $response->json());
    }

    public function test_type_must_be_branch_or_postomat(): void
    {
        $response = $this->getJson(route('front.checkout.delivery-points', ['city' => 'Київ', 'type' => 'courier']));

        $response->assertUnprocessable();
    }
}
