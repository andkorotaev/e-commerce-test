<?php

namespace Tests\Feature\Front;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_url_shows_the_branded_404_page(): void
    {
        $response = $this->get('/product/this-slug-does-not-exist');

        $response->assertNotFound();
        $response->assertSee('Сторінку не знайдено');
    }

    public function test_forbidden_request_shows_the_branded_403_page(): void
    {
        $order = Order::factory()->create(['user_id' => null]);

        $response = $this->get(route('front.checkout.thank-you', $order->id));

        $response->assertForbidden();
        $response->assertSee('Доступ заборонено');
    }

    public function test_server_error_shows_the_branded_500_page_when_debug_is_off(): void
    {
        config(['app.debug' => false]);

        Route::get('/__test-throws', function () {
            throw new \RuntimeException('Boom');
        });

        $response = $this->get('/__test-throws');

        $response->assertServerError();
        $response->assertSee('Щось пішло не так');
    }
}
