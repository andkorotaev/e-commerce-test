<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactMessageTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create();
    }

    public function test_guest_is_redirected_to_login_for_every_contact_message_route(): void
    {
        $message = ContactMessage::factory()->create();

        $this->get(route('admin.contact-messages.index'))->assertRedirect(route('admin.login'));
        $this->delete(route('admin.contact-messages.destroy', $message->id))->assertRedirect(route('admin.login'));
    }

    public function test_index_lists_messages(): void
    {
        ContactMessage::factory()->create(['name' => 'Іван Петренко', 'message' => 'Питання про доставку']);

        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.contact-messages.index'));

        $response->assertOk();
        $response->assertSee('Іван Петренко');
        $response->assertSee('Питання про доставку');
    }

    public function test_index_shows_empty_state_with_no_messages(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.contact-messages.index'));

        $response->assertOk();
        $response->assertSee('No messages yet.');
    }

    public function test_deleting_a_message_removes_it(): void
    {
        $message = ContactMessage::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')->delete(route('admin.contact-messages.destroy', $message->id));

        $response->assertRedirect(route('admin.contact-messages.index'));
        $this->assertDatabaseMissing('contact_messages', ['id' => $message->id]);
    }
}
