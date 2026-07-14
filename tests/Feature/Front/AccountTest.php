<?php

namespace Tests\Feature\Front;

use App\Models\Product;
use App\Models\User;
use App\Models\WishlistItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_shows_the_users_current_data(): void
    {
        $user = User::factory()->create(['name' => 'Марія Коваленко', 'email' => 'maria@example.com']);

        $response = $this->actingAs($user)->get(route('front.account.profile'));

        $response->assertOk();
        $response->assertSee('Марія Коваленко');
        $response->assertSee('maria@example.com');
    }

    public function test_a_user_can_update_their_name_and_email(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('front.account.profile.update'), [
            'name' => 'Оновлене Ім\'я',
            'email' => 'updated@example.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Оновлене Ім\'я',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_a_user_can_change_their_password_by_confirming_the_current_one(): void
    {
        $user = User::factory()->create(['password' => Hash::make('old-password')]);

        $response = $this->actingAs($user)->put(route('front.account.profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'old-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertRedirect();
        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));
    }

    public function test_changing_password_requires_the_correct_current_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('old-password')]);

        $response = $this->actingAs($user)->put(route('front.account.profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'wrong-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }

    public function test_orders_page_shows_an_empty_state(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('front.account.orders'));

        $response->assertOk();
        $response->assertSee('У вас поки немає замовлень');
    }

    public function test_wishlist_page_shows_saved_products(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $product->translations()->where('locale', 'uk')->update(['name' => 'Збережений товар']);

        WishlistItem::factory()->create(['user_id' => $user->id, 'product_id' => $product->id]);

        $response = $this->actingAs($user)->get(route('front.account.wishlist'));

        $response->assertOk();
        $response->assertSee('Збережений товар');
    }

    public function test_wishlist_page_shows_an_empty_state(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('front.account.wishlist'));

        $response->assertOk();
        $response->assertSee('Список бажань порожній');
    }
}
