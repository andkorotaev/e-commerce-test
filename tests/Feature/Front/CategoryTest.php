<?php

namespace Tests\Feature\Front;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_page_can_be_rendered_for_a_valid_slug(): void
    {
        $category = Category::factory()->create();
        $category->translations()->where('locale', 'uk')->update(['slug' => 'valid-category']);

        $response = $this->get(route('front.categories.show', 'valid-category'));

        $response->assertOk();
    }

    public function test_category_page_shows_its_own_name_image_and_description(): void
    {
        $category = Category::factory()->create(['image' => 'categories/hero.jpg']);
        $category->translations()->where('locale', 'uk')->update([
            'slug' => 'category-with-details',
            'name' => 'Взуття',
            'description' => 'Взуття на кожен сезон',
        ]);

        $response = $this->get(route('front.categories.show', 'category-with-details'));

        $response->assertOk();
        $response->assertSee('Взуття');
        $response->assertSee('Взуття на кожен сезон');
        $response->assertSee('categories/hero.jpg', false);
    }

    public function test_category_page_shows_its_active_children(): void
    {
        $parent = Category::factory()->create();
        $parent->translations()->where('locale', 'uk')->update(['slug' => 'parent-with-children']);

        $child = Category::factory()->create(['parent_id' => $parent->id]);
        $child->translations()->where('locale', 'uk')->update(['name' => 'Кросівки']);

        $response = $this->get(route('front.categories.show', 'parent-with-children'));

        $response->assertOk();
        $response->assertSee('Кросівки');
    }

    public function test_category_page_does_not_show_inactive_children(): void
    {
        $parent = Category::factory()->create();
        $parent->translations()->where('locale', 'uk')->update(['slug' => 'parent-with-inactive-child']);

        $child = Category::factory()->create(['parent_id' => $parent->id, 'is_active' => false]);
        $child->translations()->where('locale', 'uk')->update(['name' => 'Прихована підкатегорія']);

        $response = $this->get(route('front.categories.show', 'parent-with-inactive-child'));

        $response->assertOk();
        $response->assertDontSee('Прихована підкатегорія');
    }

    public function test_category_page_shows_coming_soon_message_for_a_leaf_category(): void
    {
        $category = Category::factory()->create();
        $category->translations()->where('locale', 'uk')->update(['slug' => 'leaf-category']);

        $response = $this->get(route('front.categories.show', 'leaf-category'));

        $response->assertOk();
        $response->assertSee('Товари незабаром');
    }

    public function test_category_page_returns_404_for_an_unknown_slug(): void
    {
        $response = $this->get(route('front.categories.show', 'this-slug-does-not-exist'));

        $response->assertNotFound();
    }

    public function test_category_page_returns_404_for_an_inactive_category(): void
    {
        $category = Category::factory()->create(['is_active' => false]);
        $category->translations()->where('locale', 'uk')->update(['slug' => 'inactive-category']);

        $response = $this->get(route('front.categories.show', 'inactive-category'));

        $response->assertNotFound();
    }
}
