<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create();
        $this->category = Category::factory()->create();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    protected function validPayload(array $overrides = []): array
    {
        return array_replace_recursive([
            'category_id' => $this->category->id,
            'sku' => 'TEST-SKU-001',
            'price' => 999.99,
            'stock' => 10,
            'sort_order' => 1,
            'is_active' => '1',
            'translations' => [
                'uk' => [
                    'name' => 'Тестовий товар',
                    'slug' => 'test-product',
                ],
                'en' => [
                    'name' => 'Test Product',
                    'slug' => 'test-product-en',
                ],
            ],
        ], $overrides);
    }

    public function test_guest_is_redirected_to_login_for_every_product_route(): void
    {
        $this->get(route('admin.products.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.products.create'))->assertRedirect(route('admin.login'));
        $this->post(route('admin.products.store'))->assertRedirect(route('admin.login'));
    }

    public function test_index_renders_the_product_list(): void
    {
        $product = Product::factory()->create();
        $ukName = $product->translations->firstWhere('locale', 'uk')->name;

        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.products.index'));

        $response->assertOk();
        $response->assertSee($ukName);
    }

    public function test_index_shows_an_empty_state_with_no_products(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.products.index'));

        $response->assertOk();
        $response->assertSee('No products yet.');
    }

    public function test_create_form_can_be_rendered(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.products.create'));

        $response->assertOk();
    }

    public function test_a_product_can_be_created(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.products.store'), $this->validPayload());

        $response->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', ['sku' => 'TEST-SKU-001', 'price' => 999.99]);
        $this->assertDatabaseHas('product_translations', [
            'locale' => 'uk', 'name' => 'Тестовий товар', 'slug' => 'test-product',
        ]);
    }

    public function test_creating_a_product_stores_multiple_uploaded_images_in_order(): void
    {
        Storage::fake('public');

        $first = UploadedFile::fake()->image('first.jpg');
        $second = UploadedFile::fake()->image('second.jpg');

        $this->actingAs($this->admin, 'admin')->post(route('admin.products.store'), $this->validPayload([
            'images' => [$first, $second],
        ]));

        $product = Product::sole();
        $images = $product->images()->orderBy('sort_order')->get();

        $this->assertCount(2, $images);
        Storage::disk('public')->assertExists($images[0]->path);
        Storage::disk('public')->assertExists($images[1]->path);
        $this->assertTrue($images[0]->sort_order < $images[1]->sort_order);
    }

    public function test_store_requires_a_name_and_slug_for_every_configured_locale(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.products.store'), $this->validPayload([
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

    public function test_store_requires_a_price(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->post(
            route('admin.products.store'),
            $this->validPayload(['price' => ''])
        );

        $response->assertSessionHasErrors('price');
    }

    public function test_store_rejects_a_duplicate_slug_within_the_same_locale(): void
    {
        $this->actingAs($this->admin, 'admin')->post(route('admin.products.store'), $this->validPayload());

        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.products.store'), $this->validPayload([
            'sku' => 'OTHER-SKU',
            'translations' => [
                'uk' => ['name' => 'Інший товар', 'slug' => 'test-product'],
                'en' => ['name' => 'Other', 'slug' => 'other-en'],
            ],
        ]));

        $response->assertSessionHasErrors('translations.uk.slug');
    }

    public function test_store_rejects_a_duplicate_sku(): void
    {
        $this->actingAs($this->admin, 'admin')->post(route('admin.products.store'), $this->validPayload());

        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.products.store'), $this->validPayload([
            'translations' => [
                'uk' => ['name' => 'Інший товар', 'slug' => 'other-product'],
                'en' => ['name' => 'Other', 'slug' => 'other-product-en'],
            ],
        ]));

        $response->assertSessionHasErrors('sku');
    }

    public function test_edit_form_renders_with_prefilled_values(): void
    {
        $product = Product::factory()->create();
        $ukName = $product->translations->firstWhere('locale', 'uk')->name;

        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.products.edit', $product->id));

        $response->assertOk();
        $response->assertSee($ukName);
    }

    public function test_edit_returns_404_for_a_missing_product(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.products.edit', 999999));

        $response->assertNotFound();
    }

    public function test_a_product_can_be_updated(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $response = $this->actingAs($this->admin, 'admin')->put(
            route('admin.products.update', $product->id),
            $this->validPayload(['price' => 1500, 'translations' => [
                'uk' => ['name' => 'Оновлений товар', 'slug' => 'updated-slug-uk'],
                'en' => ['name' => 'Updated product', 'slug' => 'updated-slug-en'],
            ]])
        );

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', ['id' => $product->id, 'price' => 1500]);
        $this->assertDatabaseHas('product_translations', [
            'product_id' => $product->id, 'locale' => 'uk', 'name' => 'Оновлений товар',
        ]);
    }

    public function test_updating_without_touching_images_keeps_them(): void
    {
        Storage::fake('public');
        $product = Product::factory()->create(['category_id' => $this->category->id]);
        $image = $product->images()->create(['path' => 'products/keep-me.jpg', 'sort_order' => 0]);
        Storage::disk('public')->put('products/keep-me.jpg', 'content');

        $this->actingAs($this->admin, 'admin')->put(
            route('admin.products.update', $product->id),
            $this->validPayload()
        );

        $this->assertDatabaseHas('product_images', ['id' => $image->id]);
        Storage::disk('public')->assertExists('products/keep-me.jpg');
    }

    public function test_updating_can_delete_a_specific_image_and_add_a_new_one(): void
    {
        Storage::fake('public');
        $product = Product::factory()->create(['category_id' => $this->category->id]);
        $oldImage = $product->images()->create(['path' => 'products/old.jpg', 'sort_order' => 0]);
        Storage::disk('public')->put('products/old.jpg', 'old-content');

        $newFile = UploadedFile::fake()->image('new.jpg');

        $this->actingAs($this->admin, 'admin')->put(
            route('admin.products.update', $product->id),
            $this->validPayload([
                'delete_images' => [$oldImage->id],
                'images' => [$newFile],
            ])
        );

        $this->assertDatabaseMissing('product_images', ['id' => $oldImage->id]);
        Storage::disk('public')->assertMissing('products/old.jpg');

        $remaining = $product->images()->first();
        $this->assertNotNull($remaining);
        Storage::disk('public')->assertExists($remaining->path);
    }

    public function test_updating_a_product_with_its_own_unchanged_slug_and_sku_does_not_trigger_uniqueness_errors(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id, 'sku' => 'KEEP-SKU']);
        $uk = $product->translations->firstWhere('locale', 'uk');
        $en = $product->translations->firstWhere('locale', 'en');

        $response = $this->actingAs($this->admin, 'admin')->put(
            route('admin.products.update', $product->id),
            $this->validPayload([
                'sku' => 'KEEP-SKU',
                'translations' => [
                    'uk' => ['name' => $uk->name, 'slug' => $uk->slug],
                    'en' => ['name' => $en->name, 'slug' => $en->slug],
                ],
            ])
        );

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.products.index'));
    }

    public function test_a_product_can_be_deleted(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')->delete(route('admin.products.destroy', $product->id));

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('product_translations', ['product_id' => $product->id]);
    }

    public function test_deleting_a_product_removes_its_image_files(): void
    {
        Storage::fake('public');
        $product = Product::factory()->create();
        $product->images()->create(['path' => 'products/to-delete.jpg', 'sort_order' => 0]);
        Storage::disk('public')->put('products/to-delete.jpg', 'content');

        $this->actingAs($this->admin, 'admin')->delete(route('admin.products.destroy', $product->id));

        Storage::disk('public')->assertMissing('products/to-delete.jpg');
        $this->assertDatabaseMissing('product_images', ['product_id' => $product->id]);
    }

    public function test_a_product_can_be_created_with_a_brand(): void
    {
        $brand = Brand::factory()->create();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.products.store'), $this->validPayload(['brand_id' => $brand->id]));

        $this->assertDatabaseHas('products', ['sku' => 'TEST-SKU-001', 'brand_id' => $brand->id]);
    }

    public function test_a_product_can_be_created_with_variants(): void
    {
        $colorValue = ProductAttributeValue::factory()->create();

        $this->actingAs($this->admin, 'admin')->post(route('admin.products.store'), $this->validPayload([
            'variants' => [
                [
                    'sku' => 'VARIANT-RED',
                    'price' => 199.99,
                    'stock' => 4,
                    'is_active' => '1',
                    'attribute_value_ids' => [$colorValue->id],
                ],
            ],
        ]));

        $product = Product::sole();
        $variant = $product->variants()->sole();

        $this->assertSame('VARIANT-RED', $variant->sku);
        $this->assertSame(4, $variant->stock);
        $this->assertTrue($variant->attributeValues->contains($colorValue->id));
    }

    public function test_store_rejects_a_variant_with_two_values_for_the_same_attribute(): void
    {
        $attribute = ProductAttribute::factory()->create();
        $red = ProductAttributeValue::factory()->for($attribute, 'attribute')->create(['product_attribute_id' => $attribute->id]);
        $blue = ProductAttributeValue::factory()->for($attribute, 'attribute')->create(['product_attribute_id' => $attribute->id]);

        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.products.store'), $this->validPayload([
            'variants' => [
                [
                    'sku' => 'VARIANT-BAD',
                    'stock' => 1,
                    'attribute_value_ids' => [$red->id, $blue->id],
                ],
            ],
        ]));

        $response->assertSessionHasErrors('variants.0.attribute_value_ids');
    }

    public function test_updating_can_keep_one_variant_drop_another_and_add_a_new_one(): void
    {
        $value = ProductAttributeValue::factory()->create();
        $product = Product::factory()->create(['category_id' => $this->category->id]);
        $keptVariant = ProductVariant::factory()->for($product)->create();
        $keptVariant->attributeValues()->attach($value->id);
        $droppedVariant = ProductVariant::factory()->for($product)->create();
        $droppedVariant->attributeValues()->attach($value->id);

        $this->actingAs($this->admin, 'admin')->put(route('admin.products.update', $product->id), $this->validPayload([
            'variants' => [
                [
                    'id' => $keptVariant->id,
                    'sku' => $keptVariant->sku,
                    'stock' => 99,
                    'attribute_value_ids' => [$value->id],
                ],
                [
                    'sku' => 'BRAND-NEW-VARIANT',
                    'stock' => 2,
                    'attribute_value_ids' => [$value->id],
                ],
            ],
        ]));

        $this->assertDatabaseHas('product_variants', ['id' => $keptVariant->id, 'stock' => 99]);
        $this->assertDatabaseMissing('product_variants', ['id' => $droppedVariant->id]);
        $this->assertDatabaseHas('product_variants', ['product_id' => $product->id, 'sku' => 'BRAND-NEW-VARIANT']);
    }

    public function test_deleting_a_product_removes_its_variants_and_their_image_files(): void
    {
        Storage::fake('public');
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->for($product)->create(['image' => 'products/variants/to-delete.jpg']);
        Storage::disk('public')->put('products/variants/to-delete.jpg', 'content');

        $this->actingAs($this->admin, 'admin')->delete(route('admin.products.destroy', $product->id));

        Storage::disk('public')->assertMissing('products/variants/to-delete.jpg');
        $this->assertDatabaseMissing('product_variants', ['id' => $variant->id]);
    }
}
