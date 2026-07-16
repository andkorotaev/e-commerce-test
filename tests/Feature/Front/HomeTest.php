<?php

namespace Tests\Feature\Front;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_can_be_rendered(): void
    {
        $response = $this->get(route('front.home'));

        $response->assertOk();
    }

    public function test_homepage_shows_the_hero_banner_with_a_catalog_link(): void
    {
        $response = $this->get(route('front.home'));

        $response->assertOk();
        $response->assertSee('Перейти до каталогу');
        $response->assertSee('#categories', false);
    }

    public function test_homepage_shows_new_arrivals_with_photo_name_price_and_link(): void
    {
        $product = Product::factory()->create(['price' => 777, 'stock' => 5]);
        $product->translations()->where('locale', 'uk')->update(['name' => 'Новинка сезону', 'slug' => 'novynka-sezonu']);

        $response = $this->get(route('front.home'));

        $response->assertOk();
        $response->assertSee('Новинки');
        $response->assertSee('Новинка сезону');
        $response->assertSee('777', false);
        $response->assertSee(route('front.products.show', 'novynka-sezonu'), false);
    }

    public function test_homepage_shows_popular_products_with_rating_and_add_to_cart(): void
    {
        $product = Product::factory()->create(['price' => 555, 'stock' => 5]);
        $product->translations()->where('locale', 'uk')->update(['name' => 'Улюблений товар']);
        Review::factory()->for($product)->create(['rating' => 5, 'is_approved' => true]);

        $response = $this->get(route('front.home'));

        $response->assertOk();
        $response->assertSee('Популярні товари');
        $response->assertSee('Улюблений товар');
        $response->assertSee('555', false);
        $response->assertSee('name="product_id" value="'.$product->id.'"', false);
    }

    public function test_adding_a_popular_product_to_cart_from_the_homepage_works(): void
    {
        $product = Product::factory()->create(['price' => 300, 'stock' => 5]);

        $response = $this->post(route('front.cart.add'), ['product_id' => $product->id, 'quantity' => 1]);

        $response->assertRedirect();
        $cartCookie = collect($response->headers->getCookies())->first(fn ($cookie) => $cookie->getName() === 'guest_cart');
        $this->assertNotNull($cartCookie, 'Expected the add-to-cart form to queue a guest_cart cookie.');
    }

    public function test_homepage_shows_store_benefits(): void
    {
        $response = $this->get(route('front.home'));

        $response->assertOk();
        $response->assertSee('Швидка доставка');
        $response->assertSee('Повернення протягом 14 днів');
        $response->assertSee('Гарантія якості');
        $response->assertSee('Онлайн підтримка');
    }

    public function test_footer_shows_contacts_navigation_and_copyright(): void
    {
        $response = $this->get(route('front.home'));

        $response->assertOk();
        $response->assertSee('hello@ocre.ua');
        $response->assertSee((string) date('Y').' OCRE');
    }

    public function test_homepage_shows_active_root_categories_with_image_name_and_description(): void
    {
        $category = Category::factory()->create(['image' => 'categories/menswear.jpg']);
        $category->translations()->where('locale', 'uk')->update([
            'name' => 'Чоловічий одяг',
            'description' => 'Стриманий гардероб з натуральних тканин',
        ]);

        $response = $this->get(route('front.home'));

        $response->assertOk();
        $response->assertSee('Чоловічий одяг');
        $response->assertSee('Стриманий гардероб з натуральних тканин');
        $response->assertSee('categories/menswear.jpg', false);
    }

    public function test_homepage_does_not_show_inactive_root_categories(): void
    {
        $category = Category::factory()->create(['is_active' => false]);
        $category->translations()->where('locale', 'uk')->update(['name' => 'Прихована категорія']);

        $response = $this->get(route('front.home'));

        $response->assertOk();
        $response->assertDontSee('Прихована категорія');
    }

    // Deliberately no "homepage does not show non-root categories" test here:
    // the header's mega-menu legitimately renders the full 3-level category
    // tree in the raw HTML (just visually hidden via CSS until hover), so a
    // child category's name genuinely IS present in the page source via the
    // nav dropdown — that's correct, not a bug. The actual business rule
    // ("the homepage's OWN category grid only includes roots") is already
    // precisely covered at the service level by
    // CategoryServiceTest::test_roots_only_returns_active_top_level_categories,
    // without the nav's legitimate deeper-level markup confusing the assertion.

    public function test_header_nav_shows_active_root_categories(): void
    {
        $category = Category::factory()->create();
        $category->translations()->where('locale', 'uk')->update(['name' => 'Взуття', 'slug' => 'footwear-nav-test']);

        $response = $this->get(route('front.home'));

        $response->assertOk();
        $response->assertSee(route('front.categories.show', 'footwear-nav-test'), false);
    }

    public function test_header_nav_does_not_show_inactive_root_categories(): void
    {
        $category = Category::factory()->create(['is_active' => false]);
        $category->translations()->where('locale', 'uk')->update(['name' => 'Прихований пункт меню']);

        $response = $this->get(route('front.home'));

        $response->assertOk();
        $response->assertDontSee('Прихований пункт меню');
    }

    public function test_header_nav_shows_subcategories_in_the_dropdown(): void
    {
        $root = Category::factory()->create();
        $level2 = Category::factory()->create(['parent_id' => $root->id]);
        $level2->translations()->where('locale', 'uk')->update(['name' => 'Верхній одяг', 'slug' => 'outerwear-nav-test']);

        $response = $this->get(route('front.home'));

        $response->assertOk();
        $response->assertSee('Верхній одяг');
    }
}
