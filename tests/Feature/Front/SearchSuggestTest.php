<?php

namespace Tests\Feature\Front;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchSuggestTest extends TestCase
{
    use RefreshDatabase;

    public function test_suggest_returns_empty_results_for_a_blank_query(): void
    {
        $response = $this->get(route('front.search.suggest'));

        $response->assertOk();
        $response->assertExactJson(['results' => []]);
    }

    public function test_suggest_returns_matching_active_products(): void
    {
        $product = Product::factory()->create(['is_active' => true, 'price' => 999]);
        $product->translations()->where('locale', 'uk')->update(['name' => 'Светр вовняний', 'slug' => 'sweater-suggest']);

        $response = $this->get(route('front.search.suggest', ['q' => 'светр']));

        $response->assertOk();
        $response->assertJsonCount(1, 'results');
        $response->assertJsonFragment(['name' => 'Светр вовняний']);
    }

    public function test_suggest_excludes_inactive_products(): void
    {
        $product = Product::factory()->create(['is_active' => false]);
        $product->translations()->where('locale', 'uk')->update(['name' => 'Прихований светр']);

        $response = $this->get(route('front.search.suggest', ['q' => 'светр']));

        $response->assertOk();
        $response->assertJsonCount(0, 'results');
    }

    public function test_suggest_caps_results_to_the_limit(): void
    {
        Product::factory()->count(10)->create(['is_active' => true])->each(function (Product $product) {
            $product->translations()->where('locale', 'uk')->update(['name' => 'Куртка тестова']);
        });

        $response = $this->get(route('front.search.suggest', ['q' => 'куртка']));

        $response->assertOk();
        $response->assertJsonCount(6, 'results');
    }
}
