<?php

namespace Tests\Feature\Front;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_can_be_rendered(): void
    {
        $this->get(route('front.register'))->assertOk();
    }

    public function test_a_user_can_register(): void
    {
        $response = $this->post(route('front.register.store'), [
            'first_name' => 'Нова',
            'last_name' => 'Людина',
            'email' => 'new-user@example.com',
            'phone' => '+380501234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('front.account.profile'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'new-user@example.com',
            'first_name' => 'Нова',
            'last_name' => 'Людина',
            'phone' => '+380501234567',
            'name' => 'Нова Людина',
        ]);
    }

    public function test_a_user_can_register_without_a_last_name(): void
    {
        $response = $this->post(route('front.register.store'), [
            'first_name' => 'Нова',
            'email' => 'no-last-name@example.com',
            'phone' => '+380501234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('front.account.profile'));
        $this->assertDatabaseHas('users', [
            'email' => 'no-last-name@example.com',
            'last_name' => null,
            'name' => 'Нова',
        ]);
    }

    public function test_registration_requires_a_unique_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->post(route('front.register.store'), [
            'first_name' => 'Дублікат',
            'email' => 'taken@example.com',
            'phone' => '+380501234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_registration_is_rate_limited(): void
    {
        $payload = [
            'first_name' => 'Тест',
            'phone' => '+380501234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('front.register.store'), [...$payload, 'email' => "user{$i}@example.com"]);
            // Registering immediately logs the new user in, and the route is
            // behind `guest` middleware — without logging back out, the next
            // attempt would be redirected by `guest` before ever reaching the
            // throttle check, and the limit would never actually get hit.
            $this->post(route('front.logout'));
        }

        $response = $this->post(route('front.register.store'), [...$payload, 'email' => 'oneMore@example.com']);

        $response->assertStatus(429);
    }

    public function test_login_page_can_be_rendered(): void
    {
        $this->get(route('front.login'))->assertOk();
    }

    public function test_a_user_can_log_in_with_correct_credentials(): void
    {
        $user = User::factory()->create(['password' => Hash::make('correct-password')]);

        $response = $this->post(route('front.login.store'), [
            'email' => $user->email,
            'password' => 'correct-password',
        ]);

        $response->assertRedirect(route('front.account.profile'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_incorrect_credentials(): void
    {
        $user = User::factory()->create(['password' => Hash::make('correct-password')]);

        $response = $this->post(route('front.login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_a_logged_in_user_can_log_out(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('front.logout'));

        $response->assertRedirect(route('front.home'));
        $this->assertGuest();
    }

    public function test_guest_is_redirected_to_login_for_account_routes(): void
    {
        $this->get(route('front.account.profile'))->assertRedirect(route('front.login'));
        $this->get(route('front.account.orders'))->assertRedirect(route('front.login'));
        $this->get(route('front.account.wishlist'))->assertRedirect(route('front.login'));
    }

    public function test_an_authenticated_user_is_redirected_away_from_login(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('front.login'));

        $response->assertRedirect(route('front.account.profile'));
    }

    public function test_forgot_password_sends_a_reset_link_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->post(route('front.password.email'), ['email' => $user->email]);

        $response->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_a_user_can_reset_their_password_with_a_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->post(route('front.password.email'), ['email' => $user->email]);

        $token = null;
        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use (&$token) {
            $token = $notification->token;

            return true;
        });

        $response = $this->post(route('front.password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'brand-new-password',
            'password_confirmation' => 'brand-new-password',
        ]);

        $response->assertRedirect(route('front.login'));
        $this->assertTrue(Hash::check('brand-new-password', $user->fresh()->password));
    }
}
