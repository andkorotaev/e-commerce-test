<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create();
    }

    public function test_guest_is_redirected_to_admin_login_for_user_routes(): void
    {
        $user = User::factory()->create();

        $this->get(route('admin.users.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.users.show', $user->id))->assertRedirect(route('admin.login'));
    }

    public function test_index_lists_registered_users(): void
    {
        $user = User::factory()->create(['name' => 'Тест Користувач']);

        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertSee('Тест Користувач');
        $response->assertSee($user->email);
    }

    public function test_show_displays_profile_and_order_history(): void
    {
        $user = User::factory()->create(['name' => 'Тест Користувач']);
        $order = Order::factory()->create(['user_id' => $user->id, 'total' => 1234]);

        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.users.show', $user->id));

        $response->assertOk();
        $response->assertSee('Тест Користувач');
        $response->assertSee($user->email);
        $response->assertSee('1,234.00', false);
    }

    public function test_show_returns_404_for_an_unknown_user(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.users.show', 999999));

        $response->assertNotFound();
    }
}
