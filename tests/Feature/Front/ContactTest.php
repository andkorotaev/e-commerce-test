<?php

namespace Tests\Feature\Front;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_can_be_rendered(): void
    {
        $response = $this->get(route('front.contact'));

        $response->assertOk();
    }

    public function test_contact_page_shows_address_phone_email_and_map(): void
    {
        $response = $this->get(route('front.contact'));

        $response->assertOk();
        $response->assertSee(config('shop.contact.address'));
        $response->assertSee(config('shop.contact.phone_display'));
        $response->assertSee(config('shop.contact.email'));
        $response->assertSee('google.com/maps', false);
    }

    public function test_guest_can_submit_the_contact_form(): void
    {
        $response = $this->post(route('front.contact.store'), [
            'name' => 'Іван Іванов',
            'email' => 'ivan@example.com',
            'phone' => '+380501234567',
            'message' => 'Питання щодо замовлення.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'contact-message-sent');
        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Іван Іванов',
            'email' => 'ivan@example.com',
            'message' => 'Питання щодо замовлення.',
        ]);
    }

    public function test_contact_form_requires_name_email_and_message(): void
    {
        $response = $this->post(route('front.contact.store'), []);

        $response->assertSessionHasErrors(['name', 'email', 'message']);
        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_contact_form_is_rate_limited(): void
    {
        $payload = [
            'name' => 'Іван Іванов',
            'email' => 'ivan@example.com',
            'message' => 'Питання щодо замовлення.',
        ];

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('front.contact.store'), $payload);
        }

        $response = $this->post(route('front.contact.store'), $payload);

        $response->assertStatus(429);
    }

    public function test_contact_form_prefills_name_and_email_for_logged_in_user(): void
    {
        $user = User::factory()->create(['name' => 'Марія Коваль', 'email' => 'maria@example.com']);

        $response = $this->actingAs($user)->get(route('front.contact'));

        $response->assertOk();
        $response->assertSee('Марія Коваль');
        $response->assertSee('maria@example.com');
    }
}
