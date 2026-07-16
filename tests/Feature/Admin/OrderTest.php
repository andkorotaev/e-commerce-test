<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create();
    }

    public function test_guest_is_redirected_to_login_for_every_order_route(): void
    {
        $order = Order::factory()->create();

        $this->get(route('admin.orders.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.orders.show', $order->id))->assertRedirect(route('admin.login'));
        $this->put(route('admin.orders.status', $order->id))->assertRedirect(route('admin.login'));
    }

    public function test_index_lists_orders_with_number_customer_total_and_status(): void
    {
        $order = Order::factory()->create([
            'first_name' => 'Тарас',
            'last_name' => 'Шевченко',
            'total' => 1234,
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.orders.index'));

        $response->assertOk();
        $response->assertSee('OCRE-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT));
        $response->assertSee('Тарас');
        $response->assertSee('1,234.00', false);
    }

    public function test_index_shows_an_empty_state_with_no_orders(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.orders.index'));

        $response->assertOk();
        $response->assertSee('No orders yet.');
    }

    public function test_show_displays_customer_delivery_payment_and_line_items(): void
    {
        $order = Order::factory()->create([
            'first_name' => 'Іван',
            'last_name' => 'Франко',
            'delivery_carrier' => 'stara_poshta',
            'delivery_type' => 'branch',
            'delivery_point' => '42',
            'payment_method' => 'stereo_bank',
        ]);
        OrderItem::factory()->create(['order_id' => $order->id, 'name' => 'Куртка кольору індиго', 'quantity' => 2]);

        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.orders.show', $order->id));

        $response->assertOk();
        $response->assertSee('Іван Франко');
        $response->assertSee(config('shop.delivery_carriers.stara_poshta'));
        $response->assertSee('42');
        $response->assertSee(config('shop.payment_methods.stereo_bank'));
        $response->assertSee('Куртка кольору індиго');
    }

    public function test_show_returns_404_for_a_missing_order(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.orders.show', 999999));

        $response->assertNotFound();
    }

    public function test_admin_can_update_an_orders_status(): void
    {
        $order = Order::factory()->create(['status' => 'new']);

        $response = $this->actingAs($this->admin, 'admin')->put(route('admin.orders.status', $order->id), [
            'status' => 'shipped',
        ]);

        $response->assertRedirect(route('admin.orders.show', $order->id));
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'shipped']);
    }

    public function test_updating_status_rejects_an_unknown_value(): void
    {
        $order = Order::factory()->create(['status' => 'new']);

        $response = $this->actingAs($this->admin, 'admin')->put(route('admin.orders.status', $order->id), [
            'status' => 'not-a-real-status',
        ]);

        $response->assertSessionHasErrors('status');
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'new']);
    }
}
