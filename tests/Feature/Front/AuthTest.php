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
            'name' => 'Нова Людина',
            'email' => 'new-user@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('front.account.profile'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'new-user@example.com']);
    }

    public function test_registration_requires_a_unique_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->post(route('front.register.store'), [
            'name' => 'Дублікат',
            'email' => 'taken@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
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
