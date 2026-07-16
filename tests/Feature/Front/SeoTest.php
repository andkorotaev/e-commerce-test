<?php

namespace Tests\Feature\Front;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_has_meta_description_and_open_graph_tags(): void
    {
        $response = $this->get(route('front.home'));

        $response->assertOk();
        $response->assertSee('<meta name="description"', false);
        $response->assertSee('<meta property="og:title"', false);
        $response->assertSee('<meta property="og:description"', false);
        $response->assertSee('<meta property="og:image"', false);
    }

    public function test_product_page_uses_its_own_meta_description_and_image(): void
    {
        $product = Product::factory()->create();
        $product->translations()->where('locale', 'uk')->update([
            'slug' => 'seo-product',
            'meta_description' => 'Унікальний опис товару для SEO.',
        ]);

        $response = $this->get(route('front.products.show', 'seo-product'));

        $response->assertOk();
        $response->assertSee('Унікальний опис товару для SEO.');
    }

    public function test_category_page_uses_its_own_meta_description(): void
    {
        $category = Category::factory()->create();
        $category->translations()->where('locale', 'uk')->update([
            'slug' => 'seo-category',
            'meta_description' => 'Унікальний опис категорії для SEO.',
        ]);

        $response = $this->get(route('front.categories.show', 'seo-category'));

        $response->assertOk();
        $response->assertSee('Унікальний опис категорії для SEO.');
    }
}
