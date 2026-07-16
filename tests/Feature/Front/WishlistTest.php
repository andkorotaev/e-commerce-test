<?php

namespace Tests\Feature\Front;

use App\Models\Product;
use App\Models\User;
use App\Models\WishlistItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_toggling_wishlist(): void
    {
        $product = Product::factory()->create();

        $response = $this->post(route('front.wishlist.toggle', $product->id));

        $response->assertRedirect(route('front.login'));
        $this->assertDatabaseMissing('wishlist_items', ['product_id' => $product->id]);
    }

    public function test_toggling_adds_a_product_not_yet_saved(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->post(route('front.wishlist.toggle', $product->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('wishlist_items', ['user_id' => $user->id, 'product_id' => $product->id]);
    }

    public function test_toggling_removes_a_product_already_saved(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        WishlistItem::factory()->create(['user_id' => $user->id, 'product_id' => $product->id]);

        $response = $this->actingAs($user)->post(route('front.wishlist.toggle', $product->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('wishlist_items', ['user_id' => $user->id, 'product_id' => $product->id]);
    }

    public function test_wishlist_is_scoped_to_the_current_user(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $product = Product::factory()->create();

        WishlistItem::factory()->create(['user_id' => $userA->id, 'product_id' => $product->id]);

        $response = $this->actingAs($userB)->get(route('front.account.wishlist'));

        $response->assertOk();
        $response->assertSee('Список бажань порожній');
    }

    public function test_product_page_shows_saved_state_for_a_wishlisted_product(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $product->translations()->where('locale', 'uk')->update(['slug' => 'wishlisted-product']);
        WishlistItem::factory()->create(['user_id' => $user->id, 'product_id' => $product->id]);

        $response = $this->actingAs($user)->get(route('front.products.show', 'wishlisted-product'));

        $response->assertOk();
        $response->assertSee('В обраному');
    }

    public function test_ajax_toggle_returns_the_new_state_as_json_without_reloading(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->post(
            route('front.wishlist.toggle', $product->id),
            [],
            ['X-Requested-With' => 'XMLHttpRequest'],
        );

        $response->assertOk();
        $response->assertJson(['isWishlisted' => true]);
        $this->assertDatabaseHas('wishlist_items', ['user_id' => $user->id, 'product_id' => $product->id]);

        $secondResponse = $this->actingAs($user)->post(
            route('front.wishlist.toggle', $product->id),
            [],
            ['X-Requested-With' => 'XMLHttpRequest'],
        );

        $secondResponse->assertJson(['isWishlisted' => false]);
    }

    public function test_ajax_wishlist_show_returns_only_the_contents_partial(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $product->translations()->where('locale', 'uk')->update(['name' => 'Список бажань товар']);
        WishlistItem::factory()->create(['user_id' => $user->id, 'product_id' => $product->id]);

        $response = $this->actingAs($user)->get(route('front.account.wishlist'), ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertOk();
        $response->assertDontSee('<html', false);
        $response->assertSee('Список бажань товар');
    }

    public function test_header_shows_wishlist_count_for_logged_in_user(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        WishlistItem::factory()->create(['user_id' => $user->id, 'product_id' => $product->id]);

        $response = $this->actingAs($user)->get(route('front.home'));

        $response->assertOk();
        $this->assertMatchesRegularExpression('/bg-madder[^>]*>\s*1\s*</', $response->getContent());
    }
}
