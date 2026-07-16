<?php

namespace Tests\Feature\Front;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    private function productWithPrice(float $price, int $stock = 10, ?float $oldPrice = null): Product
    {
        return Product::factory()->create(['price' => $price, 'stock' => $stock, 'old_price' => $oldPrice]);
    }

    public function test_guest_can_view_an_empty_cart_page(): void
    {
        $response = $this->get(route('front.cart.show'));

        $response->assertOk();
        $response->assertSee('Кошик порожній');
    }

    public function test_a_logged_in_user_can_add_a_product_to_their_cart(): void
    {
        $user = User::factory()->create();
        $product = $this->productWithPrice(500);

        $response = $this->actingAs($user)->post(route('front.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_adding_more_than_available_stock_clamps_to_available_stock(): void
    {
        $user = User::factory()->create();
        $product = $this->productWithPrice(500, stock: 3);

        $this->actingAs($user)->post(route('front.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 50,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);
    }

    public function test_cart_page_shows_added_product_and_correct_subtotal(): void
    {
        $user = User::factory()->create();
        $product = $this->productWithPrice(500);
        $product->translations()->where('locale', 'uk')->update(['name' => 'Тестовий светр']);

        $this->actingAs($user)->post(route('front.cart.add'), ['product_id' => $product->id, 'quantity' => 3]);

        $response = $this->actingAs($user)->get(route('front.cart.show'));

        $response->assertOk();
        $response->assertSee('Тестовий светр');
        $response->assertSee('1 500', false);
    }

    public function test_updating_quantity_recalculates_the_subtotal(): void
    {
        $user = User::factory()->create();
        $product = $this->productWithPrice(200);

        $this->actingAs($user)->post(route('front.cart.add'), ['product_id' => $product->id, 'quantity' => 1]);
        $this->actingAs($user)->post(route('front.cart.update'), ['product_id' => $product->id, 'quantity' => 4]);

        $this->assertDatabaseHas('cart_items', ['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 4]);

        $response = $this->actingAs($user)->get(route('front.cart.show'));
        $response->assertSee('800', false);
    }

    public function test_setting_quantity_to_zero_removes_the_item(): void
    {
        $user = User::factory()->create();
        $product = $this->productWithPrice(200);

        $this->actingAs($user)->post(route('front.cart.add'), ['product_id' => $product->id, 'quantity' => 1]);
        $this->actingAs($user)->post(route('front.cart.update'), ['product_id' => $product->id, 'quantity' => 0]);

        $this->assertDatabaseMissing('cart_items', ['user_id' => $user->id, 'product_id' => $product->id]);
    }

    public function test_removing_an_item_deletes_it_from_the_cart(): void
    {
        $user = User::factory()->create();
        $product = $this->productWithPrice(200);

        $this->actingAs($user)->post(route('front.cart.add'), ['product_id' => $product->id, 'quantity' => 1]);
        $response = $this->actingAs($user)->post(route('front.cart.remove'), ['product_id' => $product->id]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('cart_items', ['user_id' => $user->id, 'product_id' => $product->id]);
    }

    public function test_ajax_update_returns_only_the_cart_contents_partial(): void
    {
        $user = User::factory()->create();
        $product = $this->productWithPrice(200);
        $this->actingAs($user)->post(route('front.cart.add'), ['product_id' => $product->id, 'quantity' => 1]);

        $response = $this->actingAs($user)->post(route('front.cart.update'), [
            'product_id' => $product->id,
            'quantity' => 5,
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertOk();
        $response->assertDontSee('<html', false);
        $response->assertSee('1 000', false);
    }

    public function test_ajax_cart_show_returns_only_the_contents_partial(): void
    {
        $user = User::factory()->create();
        $product = $this->productWithPrice(200);
        $this->actingAs($user)->post(route('front.cart.add'), ['product_id' => $product->id, 'quantity' => 2]);

        $response = $this->actingAs($user)->get(route('front.cart.show'), ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertOk();
        $response->assertDontSee('<html', false);
        $response->assertSee('400', false);
    }

    public function test_discount_reflects_the_difference_between_old_price_and_price(): void
    {
        $user = User::factory()->create();
        $product = $this->productWithPrice(price: 400, oldPrice: 500);

        $this->actingAs($user)->post(route('front.cart.add'), ['product_id' => $product->id, 'quantity' => 2]);

        $response = $this->actingAs($user)->get(route('front.cart.show'));

        $response->assertOk();
        // subtotal: 400*2=800, discount: (500-400)*2=200
        $response->assertSee('800', false);
        $response->assertSee('200', false);
    }

    public function test_delivery_is_free_above_the_threshold_and_charged_below_it(): void
    {
        $user = User::factory()->create();
        $cheapProduct = $this->productWithPrice(100);
        $expensiveProduct = $this->productWithPrice((float) config('shop.free_shipping_threshold'));

        $this->actingAs($user)->post(route('front.cart.add'), ['product_id' => $cheapProduct->id, 'quantity' => 1]);
        $belowThreshold = $this->actingAs($user)->get(route('front.cart.show'));
        $belowThreshold->assertSee((string) config('shop.flat_shipping_fee'));

        $this->actingAs($user)->post(route('front.cart.add'), ['product_id' => $expensiveProduct->id, 'quantity' => 1]);
        $aboveThreshold = $this->actingAs($user)->get(route('front.cart.show'));
        $aboveThreshold->assertSee('Безкоштовно');
    }

    public function test_guest_cart_persists_via_cookie_across_requests(): void
    {
        $product = $this->productWithPrice(300);
        $product->translations()->where('locale', 'uk')->update(['name' => 'Гостьовий товар']);

        $addResponse = $this->post(route('front.cart.add'), ['product_id' => $product->id, 'quantity' => 2]);
        $addResponse->assertRedirect();

        $cartCookie = collect($addResponse->headers->getCookies())->first(fn ($cookie) => $cookie->getName() === 'guest_cart');
        $this->assertNotNull($cartCookie, 'Expected a guest_cart cookie to be queued on the response.');

        $pageResponse = $this->withUnencryptedCookie($cartCookie->getName(), $cartCookie->getValue())
            ->get(route('front.cart.show'));

        $pageResponse->assertOk();
        $pageResponse->assertSee('Гостьовий товар');
        $pageResponse->assertSee('600', false);
    }

    public function test_guest_cart_merges_into_the_users_cart_on_login(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);
        $product = $this->productWithPrice(150);

        $addResponse = $this->post(route('front.cart.add'), ['product_id' => $product->id, 'quantity' => 2]);
        $cartCookie = collect($addResponse->headers->getCookies())->first(fn ($cookie) => $cookie->getName() === 'guest_cart');

        $this->withUnencryptedCookie($cartCookie->getName(), $cartCookie->getValue())
            ->post(route('front.login.store'), [
                'email' => $user->email,
                'password' => 'password123',
            ]);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }
}
