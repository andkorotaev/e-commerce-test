<?php

namespace Tests\Feature\Front;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_page_prompts_for_a_query_when_none_given(): void
    {
        $response = $this->get(route('front.search'));

        $response->assertOk();
        $response->assertSee('Введіть пошуковий запит');
    }

    public function test_search_finds_active_products_matching_the_query(): void
    {
        $product = Product::factory()->create(['is_active' => true]);
        $product->translations()->where('locale', 'uk')->update(['name' => 'Вовняний светр']);

        $response = $this->get(route('front.search', ['q' => 'светр']));

        $response->assertOk();
        $response->assertSee('Вовняний светр');
    }

    public function test_search_excludes_inactive_products(): void
    {
        $product = Product::factory()->create(['is_active' => false]);
        $product->translations()->where('locale', 'uk')->update(['name' => 'Прихований светр']);

        $response = $this->get(route('front.search', ['q' => 'светр']));

        $response->assertOk();
        $response->assertDontSee('Прихований светр');
    }

    public function test_search_shows_a_no_results_message_when_nothing_matches(): void
    {
        $response = $this->get(route('front.search', ['q' => 'nonexistentquery12345']));

        $response->assertOk();
        $response->assertSee('нічого не знайдено');
    }
}
