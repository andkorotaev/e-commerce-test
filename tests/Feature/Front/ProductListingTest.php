<?php

namespace Tests\Feature\Front;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductListingTest extends TestCase
{
    use RefreshDatabase;

    private function categoryWithSlug(string $slug, ?int $parentId = null): Category
    {
        $category = Category::factory()->create(['parent_id' => $parentId]);
        $category->translations()->where('locale', 'uk')->update(['slug' => $slug]);

        return $category;
    }

    private function productIn(Category $category, array $overrides = []): Product
    {
        $name = $overrides['name'] ?? null;
        unset($overrides['name']);

        $product = Product::factory()->for($category)->create($overrides);

        if ($name !== null) {
            $product->translations()->where('locale', 'uk')->update([
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name),
            ]);
        }

        return $product;
    }

    public function test_category_page_lists_active_products_and_excludes_inactive_ones(): void
    {
        $category = $this->categoryWithSlug('leaf-with-products');

        $this->productIn($category, ['name' => 'Visible Product One']);
        $this->productIn($category, ['name' => 'Visible Product Two']);
        $this->productIn($category, ['name' => 'Hidden Product', 'is_active' => false]);

        $response = $this->get(route('front.categories.show', 'leaf-with-products'));

        $response->assertOk();
        $response->assertSee('Visible Product One');
        $response->assertSee('Visible Product Two');
        $response->assertDontSee('Hidden Product');
    }

    public function test_category_page_includes_products_from_descendant_categories(): void
    {
        $parent = $this->categoryWithSlug('parent-scope');
        $child = Category::factory()->create(['parent_id' => $parent->id]);

        $this->productIn($child, ['name' => 'Deeply Nested Product']);

        $response = $this->get(route('front.categories.show', 'parent-scope'));

        $response->assertOk();
        $response->assertSee('Deeply Nested Product');
    }

    public function test_ajax_products_endpoint_returns_only_the_results_partial(): void
    {
        $category = $this->categoryWithSlug('ajax-scope');
        $this->productIn($category, ['name' => 'Ajax Product']);

        $response = $this->get(route('front.categories.products', 'ajax-scope'), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertOk();
        $response->assertSee('Ajax Product');
        $response->assertDontSee('<html', false);
        $response->assertDontSee('data-component="front/layouts/header"', false);
    }

    public function test_filtering_by_brand_narrows_the_results(): void
    {
        $category = $this->categoryWithSlug('brand-filter-scope');
        $brandA = Brand::factory()->create();
        $brandB = Brand::factory()->create();

        $this->productIn($category, ['name' => 'Brand A Product', 'brand_id' => $brandA->id]);
        $this->productIn($category, ['name' => 'Brand B Product', 'brand_id' => $brandB->id]);

        $response = $this->get(route('front.categories.show', ['slug' => 'brand-filter-scope', 'brand' => [$brandA->id]]));

        $response->assertOk();
        $response->assertSee('Brand A Product');
        $response->assertDontSee('Brand B Product');
    }

    public function test_filtering_by_color_narrows_the_results(): void
    {
        $category = $this->categoryWithSlug('color-filter-scope');
        $attribute = ProductAttribute::factory()->create(['slug' => 'color']);
        $red = ProductAttributeValue::factory()->for($attribute, 'attribute')->create(['slug' => 'red']);
        $blue = ProductAttributeValue::factory()->for($attribute, 'attribute')->create(['slug' => 'blue']);

        $redProduct = $this->productIn($category, ['name' => 'Red Variant Product']);
        ProductVariant::factory()->for($redProduct)->create()->attributeValues()->attach($red->id);

        $blueProduct = $this->productIn($category, ['name' => 'Blue Variant Product']);
        ProductVariant::factory()->for($blueProduct)->create()->attributeValues()->attach($blue->id);

        $response = $this->get(route('front.categories.show', ['slug' => 'color-filter-scope', 'color' => [$red->id]]));

        $response->assertOk();
        $response->assertSee('Red Variant Product');
        $response->assertDontSee('Blue Variant Product');
    }

    public function test_filtering_by_price_range_narrows_the_results(): void
    {
        $category = $this->categoryWithSlug('price-filter-scope');

        $this->productIn($category, ['name' => 'Cheap Product', 'price' => 100]);
        $this->productIn($category, ['name' => 'Expensive Product', 'price' => 900]);

        $response = $this->get(route('front.categories.show', ['slug' => 'price-filter-scope', 'price_min' => 500]));

        $response->assertOk();
        $response->assertSee('Expensive Product');
        $response->assertDontSee('Cheap Product');
    }

    public function test_in_stock_filter_excludes_out_of_stock_products(): void
    {
        $category = $this->categoryWithSlug('stock-filter-scope');

        $this->productIn($category, ['name' => 'In Stock Product', 'stock' => 5]);
        $this->productIn($category, ['name' => 'Out Of Stock Product', 'stock' => 0]);

        $response = $this->get(route('front.categories.show', ['slug' => 'stock-filter-scope', 'in_stock' => 1]));

        $response->assertOk();
        $response->assertSee('In Stock Product');
        $response->assertDontSee('Out Of Stock Product');
    }

    public function test_search_narrows_results_by_name(): void
    {
        $category = $this->categoryWithSlug('search-scope');

        $this->productIn($category, ['name' => 'Zebra Special Jacket']);
        $this->productIn($category, ['name' => 'Ordinary Coat']);

        $response = $this->get(route('front.categories.show', ['slug' => 'search-scope', 'search' => 'Zebra']));

        $response->assertOk();
        $response->assertSee('Zebra Special Jacket');
        $response->assertDontSee('Ordinary Coat');
    }

    public function test_sorting_by_price_ascending_orders_products_correctly(): void
    {
        $category = $this->categoryWithSlug('sort-scope');

        $this->productIn($category, ['name' => 'Pricier Item', 'price' => 800]);
        $this->productIn($category, ['name' => 'Cheaper Item', 'price' => 100]);

        $response = $this->get(route('front.categories.show', ['slug' => 'sort-scope', 'sort' => 'price_asc']));

        $response->assertOk();
        $content = $response->getContent();

        $this->assertLessThan(
            strpos($content, 'Pricier Item'),
            strpos($content, 'Cheaper Item'),
        );
    }

    public function test_pagination_splits_results_across_pages(): void
    {
        $category = $this->categoryWithSlug('pagination-scope');

        Product::factory()->for($category)->count(13)->create();

        $firstPage = $this->get(route('front.categories.show', 'pagination-scope'));
        $secondPage = $this->get(route('front.categories.show', ['slug' => 'pagination-scope', 'page' => 2]));

        $firstPage->assertOk();
        $secondPage->assertOk();
        $firstPage->assertSee('data-products-pagination', false);
        $this->assertNotSame($firstPage->getContent(), $secondPage->getContent());
    }
}
