<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    protected function validPayload(array $overrides = []): array
    {
        return array_replace_recursive([
            'sort_order' => 1,
            'is_active' => '1',
            'translations' => [
                'uk' => [
                    'name' => 'Чоловічий одяг',
                    'slug' => 'cholovichyy-odyag',
                ],
                'en' => [
                    'name' => 'Menswear',
                    'slug' => 'menswear',
                ],
            ],
        ], $overrides);
    }

    public function test_guest_is_redirected_to_login_for_every_category_route(): void
    {
        $this->get(route('admin.categories.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.categories.create'))->assertRedirect(route('admin.login'));
        $this->post(route('admin.categories.store'))->assertRedirect(route('admin.login'));
    }

    public function test_index_renders_the_category_tree(): void
    {
        $category = Category::factory()->create();
        $ukName = $category->translations->firstWhere('locale', 'uk')->name;

        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.categories.index'));

        $response->assertOk();
        $response->assertSee($ukName);
    }

    public function test_index_shows_an_empty_state_with_no_categories(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.categories.index'));

        $response->assertOk();
        $response->assertSee('No categories yet.');
    }

    public function test_create_form_can_be_rendered(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.categories.create'));

        $response->assertOk();
    }

    public function test_a_category_can_be_created(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.categories.store'), $this->validPayload());

        $response->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', ['sort_order' => 1, 'is_active' => 1]);
        $this->assertDatabaseHas('category_translations', [
            'locale' => 'uk', 'name' => 'Чоловічий одяг', 'slug' => 'cholovichyy-odyag',
        ]);
        $this->assertDatabaseHas('category_translations', [
            'locale' => 'en', 'name' => 'Menswear', 'slug' => 'menswear',
        ]);
    }

    public function test_creating_a_category_stores_the_uploaded_image(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('category.jpg');

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.categories.store'), $this->validPayload(['image' => $file]));

        $category = Category::sole();

        $this->assertNotNull($category->image);
        Storage::disk('public')->assertExists($category->image);
    }

    public function test_a_subcategory_can_be_created_under_a_parent(): void
    {
        $parent = Category::factory()->create();

        $this->actingAs($this->admin, 'admin')->post(
            route('admin.categories.store'),
            $this->validPayload(['parent_id' => $parent->id]),
        );

        $this->assertDatabaseHas('categories', ['parent_id' => $parent->id]);
    }

    public function test_store_requires_a_name_and_slug_for_every_configured_locale(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.categories.store'), $this->validPayload([
            'translations' => [
                'uk' => ['name' => '', 'slug' => ''],
                'en' => ['name' => '', 'slug' => ''],
            ],
        ]));

        $response->assertSessionHasErrors([
            'translations.uk.name',
            'translations.uk.slug',
            'translations.en.name',
            'translations.en.slug',
        ]);
    }

    public function test_store_rejects_a_duplicate_slug_within_the_same_locale(): void
    {
        $this->actingAs($this->admin, 'admin')->post(route('admin.categories.store'), $this->validPayload());

        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.categories.store'), $this->validPayload([
            'translations' => [
                'uk' => ['name' => 'Інша назва', 'slug' => 'cholovichyy-odyag'],
                'en' => ['name' => 'Other', 'slug' => 'other-en'],
            ],
        ]));

        $response->assertSessionHasErrors('translations.uk.slug');
    }

    public function test_edit_form_renders_with_prefilled_values(): void
    {
        $category = Category::factory()->create();
        $ukName = $category->translations->firstWhere('locale', 'uk')->name;

        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.categories.edit', $category->id));

        $response->assertOk();
        $response->assertSee($ukName);
    }

    public function test_edit_returns_404_for_a_missing_category(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.categories.edit', 999999));

        $response->assertNotFound();
    }

    public function test_a_category_can_be_updated(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')->put(
            route('admin.categories.update', $category->id),
            $this->validPayload(['translations' => [
                'uk' => ['name' => 'Оновлена назва', 'slug' => 'updated-slug-uk'],
                'en' => ['name' => 'Updated name', 'slug' => 'updated-slug-en'],
            ]]),
        );

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('category_translations', [
            'category_id' => $category->id, 'locale' => 'uk', 'name' => 'Оновлена назва',
        ]);
    }

    public function test_updating_without_a_new_image_keeps_the_existing_one(): void
    {
        Storage::fake('public');
        $existingPath = 'categories/existing.jpg';
        Storage::disk('public')->put($existingPath, 'fake-content');
        $category = Category::factory()->create(['image' => $existingPath]);

        $this->actingAs($this->admin, 'admin')->put(
            route('admin.categories.update', $category->id),
            $this->validPayload(),
        );

        $this->assertSame($existingPath, $category->fresh()->image);
        Storage::disk('public')->assertExists($existingPath);
    }

    public function test_updating_with_a_new_image_deletes_the_old_one(): void
    {
        Storage::fake('public');
        $oldPath = 'categories/old.jpg';
        Storage::disk('public')->put($oldPath, 'old-content');
        $category = Category::factory()->create(['image' => $oldPath]);

        $newFile = UploadedFile::fake()->image('new.jpg');

        $this->actingAs($this->admin, 'admin')->put(
            route('admin.categories.update', $category->id),
            $this->validPayload(['image' => $newFile]),
        );

        $fresh = $category->fresh();
        $this->assertNotSame($oldPath, $fresh->image);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($fresh->image);
    }

    public function test_a_category_cannot_be_set_as_its_own_parent(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')->put(
            route('admin.categories.update', $category->id),
            $this->validPayload(['parent_id' => $category->id]),
        );

        $response->assertSessionHasErrors('parent_id');
    }

    public function test_a_category_cannot_be_moved_under_its_own_child(): void
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create(['parent_id' => $parent->id]);

        $response = $this->actingAs($this->admin, 'admin')->put(
            route('admin.categories.update', $parent->id),
            $this->validPayload(['parent_id' => $child->id]),
        );

        $response->assertSessionHasErrors('parent_id');
    }

    public function test_updating_a_category_with_its_own_unchanged_slug_does_not_trigger_a_uniqueness_error(): void
    {
        $category = Category::factory()->create();
        $uk = $category->translations->firstWhere('locale', 'uk');
        $en = $category->translations->firstWhere('locale', 'en');

        $response = $this->actingAs($this->admin, 'admin')->put(
            route('admin.categories.update', $category->id),
            $this->validPayload(['translations' => [
                'uk' => ['name' => $uk->name, 'slug' => $uk->slug],
                'en' => ['name' => $en->name, 'slug' => $en->slug],
            ]]),
        );

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.categories.index'));
    }

    public function test_a_category_can_be_deleted(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')->delete(route('admin.categories.destroy', $category->id));

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $this->assertDatabaseMissing('category_translations', ['category_id' => $category->id]);
    }

    public function test_deleting_a_category_removes_its_image_file(): void
    {
        Storage::fake('public');
        $path = 'categories/to-delete.jpg';
        Storage::disk('public')->put($path, 'content');
        $category = Category::factory()->create(['image' => $path]);

        $this->actingAs($this->admin, 'admin')->delete(route('admin.categories.destroy', $category->id));

        Storage::disk('public')->assertMissing($path);
    }
}
