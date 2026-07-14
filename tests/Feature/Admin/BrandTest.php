<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BrandTest extends TestCase
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
            'slug' => 'ocre-basics',
            'name' => 'OCRE Basics',
            'is_active' => '1',
        ], $overrides);
    }

    public function test_guest_is_redirected_to_login_for_every_brand_route(): void
    {
        $this->get(route('admin.brands.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.brands.create'))->assertRedirect(route('admin.login'));
        $this->post(route('admin.brands.store'))->assertRedirect(route('admin.login'));
    }

    public function test_index_renders_the_brand_list(): void
    {
        $brand = Brand::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.brands.index'));

        $response->assertOk();
        $response->assertSee($brand->name);
    }

    public function test_index_shows_an_empty_state_with_no_brands(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.brands.index'));

        $response->assertOk();
        $response->assertSee('No brands yet.');
    }

    public function test_create_form_can_be_rendered(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.brands.create'));

        $response->assertOk();
    }

    public function test_a_brand_can_be_created(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.brands.store'), $this->validPayload());

        $response->assertRedirect(route('admin.brands.index'));
        $this->assertDatabaseHas('brands', ['slug' => 'ocre-basics', 'name' => 'OCRE Basics', 'is_active' => 1]);
    }

    public function test_creating_a_brand_stores_the_uploaded_logo(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('logo.jpg');

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.brands.store'), $this->validPayload(['logo' => $file]));

        $brand = Brand::sole();

        $this->assertNotNull($brand->logo);
        Storage::disk('public')->assertExists($brand->logo);
    }

    public function test_store_requires_a_slug_and_name(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.brands.store'), $this->validPayload([
            'slug' => '', 'name' => '',
        ]));

        $response->assertSessionHasErrors(['slug', 'name']);
    }

    public function test_store_rejects_a_duplicate_slug(): void
    {
        $this->actingAs($this->admin, 'admin')->post(route('admin.brands.store'), $this->validPayload());

        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.brands.store'), $this->validPayload([
            'name' => 'Another brand',
        ]));

        $response->assertSessionHasErrors('slug');
    }

    public function test_edit_form_renders_with_prefilled_values(): void
    {
        $brand = Brand::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.brands.edit', $brand->id));

        $response->assertOk();
        $response->assertSee($brand->name);
    }

    public function test_edit_returns_404_for_a_missing_brand(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.brands.edit', 999999));

        $response->assertNotFound();
    }

    public function test_a_brand_can_be_updated(): void
    {
        $brand = Brand::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')->put(
            route('admin.brands.update', $brand->id),
            $this->validPayload(['name' => 'Updated name']),
        );

        $response->assertRedirect(route('admin.brands.index'));
        $this->assertDatabaseHas('brands', ['id' => $brand->id, 'name' => 'Updated name']);
    }

    public function test_updating_without_a_new_logo_keeps_the_existing_one(): void
    {
        Storage::fake('public');
        $existingPath = 'brands/existing.jpg';
        Storage::disk('public')->put($existingPath, 'fake-content');
        $brand = Brand::factory()->create(['logo' => $existingPath]);

        $this->actingAs($this->admin, 'admin')->put(
            route('admin.brands.update', $brand->id),
            $this->validPayload(),
        );

        $this->assertSame($existingPath, $brand->fresh()->logo);
        Storage::disk('public')->assertExists($existingPath);
    }

    public function test_updating_with_a_new_logo_deletes_the_old_one(): void
    {
        Storage::fake('public');
        $oldPath = 'brands/old.jpg';
        Storage::disk('public')->put($oldPath, 'old-content');
        $brand = Brand::factory()->create(['logo' => $oldPath]);

        $newFile = UploadedFile::fake()->image('new.jpg');

        $this->actingAs($this->admin, 'admin')->put(
            route('admin.brands.update', $brand->id),
            $this->validPayload(['logo' => $newFile]),
        );

        $fresh = $brand->fresh();
        $this->assertNotSame($oldPath, $fresh->logo);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($fresh->logo);
    }

    public function test_updating_a_brand_with_its_own_unchanged_slug_does_not_trigger_a_uniqueness_error(): void
    {
        $brand = Brand::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')->put(
            route('admin.brands.update', $brand->id),
            $this->validPayload(['slug' => $brand->slug, 'name' => $brand->name]),
        );

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.brands.index'));
    }

    public function test_a_brand_can_be_deleted(): void
    {
        $brand = Brand::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')->delete(route('admin.brands.destroy', $brand->id));

        $response->assertRedirect(route('admin.brands.index'));
        $this->assertDatabaseMissing('brands', ['id' => $brand->id]);
    }

    public function test_deleting_a_brand_removes_its_logo_file(): void
    {
        Storage::fake('public');
        $path = 'brands/to-delete.jpg';
        Storage::disk('public')->put($path, 'content');
        $brand = Brand::factory()->create(['logo' => $path]);

        $this->actingAs($this->admin, 'admin')->delete(route('admin.brands.destroy', $brand->id));

        Storage::disk('public')->assertMissing($path);
    }
}
