<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create();
    }

    public function test_guest_is_redirected_to_login_for_every_review_route(): void
    {
        $review = Review::factory()->create();

        $this->get(route('admin.reviews.index'))->assertRedirect(route('admin.login'));
        $this->post(route('admin.reviews.approve', $review->id))->assertRedirect(route('admin.login'));
        $this->delete(route('admin.reviews.destroy', $review->id))->assertRedirect(route('admin.login'));
    }

    public function test_index_lists_reviews_with_their_product_name(): void
    {
        $product = Product::factory()->create();
        $product->translations()->where('locale', 'uk')->update(['name' => 'Тестовий товар']);

        Review::factory()->for($product)->create(['author_name' => 'Іван']);

        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.reviews.index'));

        $response->assertOk();
        $response->assertSee('Тестовий товар');
        $response->assertSee('Іван');
    }

    public function test_approving_a_review_marks_it_approved(): void
    {
        $review = Review::factory()->create(['is_approved' => false]);

        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.reviews.approve', $review->id));

        $response->assertRedirect(route('admin.reviews.index'));
        $this->assertDatabaseHas('reviews', ['id' => $review->id, 'is_approved' => true]);
    }

    public function test_approving_a_review_makes_it_visible_on_the_product_page(): void
    {
        $product = Product::factory()->create();
        $product->translations()->where('locale', 'uk')->update(['slug' => 'approve-flow-product']);

        $review = Review::factory()->for($product)->create(['author_name' => 'Марія', 'is_approved' => false]);

        $before = $this->get(route('front.products.show', 'approve-flow-product'));
        $before->assertDontSee('Марія');

        $this->actingAs($this->admin, 'admin')->post(route('admin.reviews.approve', $review->id));

        $after = $this->get(route('front.products.show', 'approve-flow-product'));
        $after->assertSee('Марія');
    }

    public function test_deleting_a_review_removes_it(): void
    {
        $review = Review::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')->delete(route('admin.reviews.destroy', $review->id));

        $response->assertRedirect(route('admin.reviews.index'));
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }
}
