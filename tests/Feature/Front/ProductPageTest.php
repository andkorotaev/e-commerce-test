<?php

namespace Tests\Feature\Front;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPageTest extends TestCase
{
    use RefreshDatabase;

    private function productWithSlug(string $slug, array $overrides = []): Product
    {
        $product = Product::factory()->create($overrides);
        $product->translations()->where('locale', 'uk')->update([
            'name' => 'Тестовий товар',
            'slug' => $slug,
        ]);

        return $product;
    }

    public function test_product_page_can_be_rendered_for_a_valid_slug(): void
    {
        $this->productWithSlug('valid-product');

        $response = $this->get(route('front.products.show', 'valid-product'));

        $response->assertOk();
    }

    public function test_product_page_shows_name_sku_and_price(): void
    {
        $this->productWithSlug('detailed-product', ['sku' => 'SKU-777', 'price' => 1234]);

        $response = $this->get(route('front.products.show', 'detailed-product'));

        $response->assertOk();
        $response->assertSee('Тестовий товар');
        $response->assertSee('SKU-777');
        $response->assertSee('1 234', false);
    }

    public function test_product_page_shows_gallery_images(): void
    {
        $product = $this->productWithSlug('gallery-product');
        ProductImage::factory()->for($product)->create(['path' => 'products/one.jpg', 'sort_order' => 0]);
        ProductImage::factory()->for($product)->create(['path' => 'products/two.jpg', 'sort_order' => 1]);

        $response = $this->get(route('front.products.show', 'gallery-product'));

        $response->assertOk();
        $response->assertSee('products/one.jpg', false);
        $response->assertSee('products/two.jpg', false);
        $response->assertSee('data-gallery-thumb', false);
    }

    public function test_product_page_shows_similar_products_from_the_same_category(): void
    {
        $category = Category::factory()->create();
        $product = $this->productWithSlug('main-product', ['category_id' => $category->id]);

        $similar = Product::factory()->for($category)->create();
        $similar->translations()->where('locale', 'uk')->update(['name' => 'Схожий товар']);

        $otherCategoryProduct = Product::factory()->create();
        $otherCategoryProduct->translations()->where('locale', 'uk')->update(['name' => 'Товар з іншої категорії']);

        $response = $this->get(route('front.products.show', 'main-product'));

        $response->assertOk();
        $response->assertSee('Схожий товар');
        $response->assertDontSee('Товар з іншої категорії');
    }

    public function test_product_page_returns_404_for_an_unknown_slug(): void
    {
        $response = $this->get(route('front.products.show', 'does-not-exist'));

        $response->assertNotFound();
    }

    public function test_product_page_returns_404_for_an_inactive_product(): void
    {
        $this->productWithSlug('inactive-product', ['is_active' => false]);

        $response = $this->get(route('front.products.show', 'inactive-product'));

        $response->assertNotFound();
    }

    public function test_guest_cannot_submit_a_review(): void
    {
        $this->productWithSlug('guest-review-product');

        $response = $this->post(route('front.reviews.store', 'guest-review-product'), [
            'rating' => 5,
            'comment' => 'Дуже якісний товар!',
        ]);

        $response->assertRedirect(route('front.login'));
        $this->assertDatabaseMissing('reviews', ['comment' => 'Дуже якісний товар!']);
    }

    public function test_submitting_a_review_creates_an_unapproved_review_not_shown_on_the_page(): void
    {
        $this->productWithSlug('review-product');
        $user = User::factory()->create(['name' => 'Іван Петренко']);

        $response = $this->actingAs($user)->post(route('front.reviews.store', 'review-product'), [
            'rating' => 5,
            'comment' => 'Дуже якісний товар!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'author_name' => 'Іван Петренко',
            'is_approved' => false,
        ]);

        // The logged-in author's name legitimately still appears in the
        // "you're posting as ..." form prompt — the real signal that the
        // review itself isn't public yet is that its own comment text,
        // which only ever renders inside an approved review entry, is absent.
        $page = $this->get(route('front.products.show', 'review-product'));
        $page->assertDontSee('Дуже якісний товар!');
    }

    public function test_review_submission_requires_valid_fields(): void
    {
        $this->productWithSlug('validation-product');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('front.reviews.store', 'validation-product'), [
            'rating' => 9,
            'comment' => '',
        ]);

        $response->assertSessionHasErrors(['rating', 'comment']);
    }

    public function test_review_submission_is_rate_limited(): void
    {
        $this->productWithSlug('throttle-product');
        $user = User::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            $this->actingAs($user)->post(route('front.reviews.store', 'throttle-product'), [
                'rating' => 5,
                'comment' => "Comment {$i}",
            ]);
        }

        $response = $this->actingAs($user)->post(route('front.reviews.store', 'throttle-product'), [
            'rating' => 5,
            'comment' => 'One more comment',
        ]);

        $response->assertStatus(429);
    }

    public function test_approved_reviews_are_shown_and_rating_stats_are_correct(): void
    {
        $product = $this->productWithSlug('rated-product');

        Review::factory()->for($product)->create(['rating' => 5, 'author_name' => 'Approved Author', 'is_approved' => true]);
        Review::factory()->for($product)->create(['rating' => 3, 'author_name' => 'Hidden Author', 'is_approved' => false]);

        $response = $this->get(route('front.products.show', 'rated-product'));

        $response->assertOk();
        $response->assertSee('Approved Author');
        $response->assertDontSee('Hidden Author');
        $response->assertSee('5.0', false);
    }
}
