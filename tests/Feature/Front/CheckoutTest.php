<?php

namespace Tests\Feature\Front;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The checkout page's delivery section calls CityLookupService live,
        // which otherwise hits the real Overpass API on every test run —
        // faked so these tests stay fast, deterministic, and don't depend on
        // outbound network access (e.g. in CI).
        Http::fake([
            'overpass-api.de/*' => Http::response([
                'elements' => [['tags' => ['name' => 'Київ']]],
            ], 200),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_replace([
            'first_name' => 'Тарас',
            'last_name' => 'Шевченко',
            'phone' => '+380501234567',
            'email' => 'taras@example.com',
            'city' => 'Київ',
            'address' => 'вул. Хрещатик, 1',
            'comment' => null,
            'delivery_carrier' => 'stara_poshta',
            'delivery_type' => 'branch',
            'delivery_point' => '25',
            'payment_method' => 'stereo_bank',
        ], $overrides);
    }

    /**
     * Adds a product to a GUEST cart. Laravel's test client doesn't carry
     * cookies (or, therefore, the session) between separate calls unless
     * explicitly attached — this grabs every cookie the add response
     * queued (guest_cart AND the session cookie) and attaches them via
     * withUnencryptedCookie(), which, once set, applies to every
     * subsequent request the calling test makes, not just the next one.
     * Without carrying the session cookie too, a later request in the same
     * test would silently get a brand new session.
     */
    private function addProductToCart(?float $price = 500): Product
    {
        $product = Product::factory()->create(['price' => $price, 'stock' => 10]);
        $response = $this->post(route('front.cart.add'), ['product_id' => $product->id, 'quantity' => 2]);

        foreach ($response->headers->getCookies() as $cookie) {
            $this->withUnencryptedCookie($cookie->getName(), $cookie->getValue());
        }

        return $product;
    }

    public function test_checkout_page_redirects_to_cart_when_it_is_empty(): void
    {
        $response = $this->get(route('front.checkout'));

        $response->assertRedirect(route('front.cart.show'));
    }

    public function test_checkout_page_shows_the_cart_review_when_not_empty(): void
    {
        $product = $this->addProductToCart();
        $product->translations()->where('locale', 'uk')->update(['name' => 'Товар для чекауту']);

        $response = $this->get(route('front.checkout'));

        $response->assertOk();
        $response->assertSee('Товар для чекауту');
    }

    public function test_checkout_page_prefills_name_and_email_from_the_authenticated_user(): void
    {
        $user = User::factory()->create(['name' => 'Іван Франко', 'email' => 'ivan@example.com', 'phone' => '+380671112233']);
        $this->actingAs($user)->post(route('front.cart.add'), [
            'product_id' => Product::factory()->create(['price' => 300, 'stock' => 5])->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('front.checkout'));

        $response->assertOk();
        $response->assertSee('value="Іван"', false);
        $response->assertSee('value="Франко"', false);
        $response->assertSee('value="ivan@example.com"', false);
        $response->assertSee('value="+380671112233"', false);
    }

    public function test_guest_can_place_an_order(): void
    {
        $product = $this->addProductToCart(price: 500);

        $response = $this->post(route('front.checkout.store'), $this->validPayload());

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'first_name' => 'Тарас',
            'last_name' => 'Шевченко',
            'email' => 'taras@example.com',
            'user_id' => null,
            'total' => 1000 + 150, // 2 * 500 + flat shipping fee (below free-shipping threshold)
        ]);

        $order = Order::first();
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 500,
        ]);
    }

    public function test_placing_an_order_clears_the_cart(): void
    {
        $this->addProductToCart();

        $response = $this->post(route('front.checkout.store'), $this->validPayload());

        // The test client re-sends the same fixed cookie value on every
        // subsequent call regardless of what the response tells the
        // browser to do (unlike a real browser's cookie jar), so the real
        // signal that the cart was cleared is the response itself queuing
        // an expired guest_cart cookie — not a follow-up "is the cart page
        // empty" request, which would just keep resending the stale value.
        $clearedCookie = collect($response->headers->getCookies())->first(fn ($cookie) => $cookie->getName() === 'guest_cart');

        $this->assertNotNull($clearedCookie, 'Expected the guest_cart cookie to be cleared on the response.');
        $this->assertLessThan(time(), $clearedCookie->getExpiresTime());
    }

    public function test_logged_in_user_can_place_an_order_tied_to_their_account(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('front.cart.add'), [
            'product_id' => Product::factory()->create(['price' => 400, 'stock' => 5])->id,
            'quantity' => 1,
        ]);

        $this->actingAs($user)->post(route('front.checkout.store'), $this->validPayload());

        $this->assertDatabaseHas('orders', ['user_id' => $user->id]);
    }

    public function test_delivery_point_is_required_for_branch_delivery(): void
    {
        $this->addProductToCart();

        $response = $this->post(route('front.checkout.store'), $this->validPayload(['delivery_point' => null]));

        $response->assertSessionHasErrors('delivery_point');
    }

    public function test_delivery_point_is_not_required_for_address_delivery(): void
    {
        $this->addProductToCart();

        $response = $this->post(route('front.checkout.store'), $this->validPayload([
            'delivery_type' => 'address',
            'delivery_point' => null,
        ]));

        $response->assertSessionDoesntHaveErrors('delivery_point');
        $this->assertDatabaseHas('orders', ['delivery_type' => 'address', 'delivery_point' => null]);
    }

    public function test_address_is_required_for_courier_delivery(): void
    {
        $this->addProductToCart();

        $response = $this->post(route('front.checkout.store'), $this->validPayload([
            'delivery_type' => 'address',
            'delivery_point' => null,
            'address' => null,
        ]));

        $response->assertSessionHasErrors('address');
    }

    public function test_address_is_not_required_for_branch_delivery(): void
    {
        $this->addProductToCart();

        $response = $this->post(route('front.checkout.store'), $this->validPayload(['address' => null]));

        $response->assertSessionDoesntHaveErrors('address');
        $this->assertDatabaseHas('orders', ['delivery_type' => 'branch', 'address' => null]);
    }

    public function test_guest_checking_create_account_creates_and_logs_in_a_new_user(): void
    {
        $this->addProductToCart();

        $response = $this->post(route('front.checkout.store'), $this->validPayload([
            'email' => 'brand-new@example.com',
            'create_account' => '1',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]));

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'brand-new@example.com']);
        $this->assertAuthenticated();

        $newUser = User::where('email', 'brand-new@example.com')->firstOrFail();
        $this->assertDatabaseHas('orders', ['user_id' => $newUser->id, 'email' => 'brand-new@example.com']);
    }

    public function test_create_account_requires_a_password(): void
    {
        $this->addProductToCart();

        $response = $this->post(route('front.checkout.store'), $this->validPayload([
            'create_account' => '1',
        ]));

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_thank_you_page_is_accessible_to_the_guest_who_placed_the_order(): void
    {
        $this->addProductToCart();
        $this->post(route('front.checkout.store'), $this->validPayload());

        $order = Order::first();
        $response = $this->get(route('front.checkout.thank-you', $order->id));

        $response->assertOk();
        $response->assertSee('Дякуємо за замовлення!');
        $response->assertSee('OCRE-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT));
    }

    public function test_thank_you_page_returns_403_for_a_guest_who_never_placed_it(): void
    {
        // Created directly, bypassing the checkout flow entirely — this
        // test's session was never involved in placing it, which is the
        // scenario the guard exists for (a visitor guessing/sharing an
        // order URL rather than genuinely having just placed it).
        $order = Order::factory()->create(['user_id' => null]);

        $response = $this->get(route('front.checkout.thank-you', $order->id));

        $response->assertForbidden();
    }

    public function test_thank_you_page_returns_403_for_a_logged_in_user_who_does_not_own_it(): void
    {
        $owner = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $owner->id]);

        $intruder = User::factory()->create();
        $response = $this->actingAs($intruder)->get(route('front.checkout.thank-you', $order->id));

        $response->assertForbidden();
    }

    public function test_thank_you_page_is_accessible_to_the_owning_logged_in_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('front.cart.add'), [
            'product_id' => Product::factory()->create(['price' => 200, 'stock' => 5])->id,
            'quantity' => 1,
        ]);
        $this->actingAs($user)->post(route('front.checkout.store'), $this->validPayload());
        $order = Order::first();

        // A fresh request cycle, still authenticated as the same user.
        $response = $this->actingAs($user)->get(route('front.checkout.thank-you', $order->id));

        $response->assertOk();
    }

    public function test_placed_order_appears_in_the_users_order_history(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('front.cart.add'), [
            'product_id' => Product::factory()->create(['price' => 250, 'stock' => 5])->id,
            'quantity' => 1,
        ]);
        $this->actingAs($user)->post(route('front.checkout.store'), $this->validPayload());

        $response = $this->actingAs($user)->get(route('front.account.orders'));

        $response->assertOk();
        $order = Order::first();
        $response->assertSee('OCRE-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT));
    }
}
