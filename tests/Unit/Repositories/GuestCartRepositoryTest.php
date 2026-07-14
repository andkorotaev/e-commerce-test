<?php

namespace Tests\Unit\Repositories;

use App\Repositories\GuestCartRepository;
use Illuminate\Support\Facades\Cookie;
use Tests\TestCase;

class GuestCartRepositoryTest extends TestCase
{
    public function test_add_creates_a_new_line_when_the_cart_is_empty(): void
    {
        $repository = new GuestCartRepository;
        $repository->add(5, null, 2);

        $lines = $repository->rawLines();

        $this->assertCount(1, $lines);
        $this->assertSame(['product_id' => 5, 'variant_id' => null, 'quantity' => 2], $lines->first());
        $this->assertTrue(Cookie::hasQueued('guest_cart'));
    }

    public function test_add_increments_an_existing_lines_quantity(): void
    {
        $repository = new GuestCartRepository;
        $repository->add(5, null, 2);
        $repository->add(5, null, 3);

        $lines = $repository->rawLines();

        $this->assertCount(1, $lines);
        $this->assertSame(5, $lines->first()['quantity']);
    }

    public function test_add_keeps_the_same_product_with_different_variants_as_separate_lines(): void
    {
        $repository = new GuestCartRepository;
        $repository->add(5, 10, 1);
        $repository->add(5, 11, 1);

        $this->assertCount(2, $repository->rawLines());
    }

    public function test_update_quantity_sets_the_quantity_outright(): void
    {
        $repository = new GuestCartRepository;
        $repository->add(5, null, 2);
        $repository->updateQuantity(5, null, 9);

        $this->assertSame(9, $repository->rawLines()->first()['quantity']);
    }

    public function test_remove_deletes_the_matching_line(): void
    {
        $repository = new GuestCartRepository;
        $repository->add(5, null, 1);
        $repository->add(6, null, 1);
        $repository->remove(5, null);

        $lines = $repository->rawLines();

        $this->assertCount(1, $lines);
        $this->assertSame(6, $lines->first()['product_id']);
    }

    public function test_clear_empties_the_cart(): void
    {
        $repository = new GuestCartRepository;
        $repository->add(5, null, 1);
        $repository->clear();

        $this->assertCount(0, $repository->rawLines());
    }
}
