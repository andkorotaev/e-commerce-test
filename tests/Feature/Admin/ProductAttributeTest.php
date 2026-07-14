<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductAttributeTest extends TestCase
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
            'slug' => 'color',
            'translations' => [
                'uk' => ['name' => 'Колір'],
                'en' => ['name' => 'Color'],
            ],
            'values' => [
                [
                    'slug' => 'red',
                    'translations' => [
                        'uk' => ['value' => 'Червоний'],
                        'en' => ['value' => 'Red'],
                    ],
                ],
            ],
        ], $overrides);
    }

    public function test_guest_is_redirected_to_login_for_every_attribute_route(): void
    {
        $this->get(route('admin.product-attributes.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.product-attributes.create'))->assertRedirect(route('admin.login'));
        $this->post(route('admin.product-attributes.store'))->assertRedirect(route('admin.login'));
    }

    public function test_index_renders_the_attribute_list(): void
    {
        $attribute = ProductAttribute::factory()->create();
        $ukName = $attribute->translations->firstWhere('locale', 'uk')->name;

        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.product-attributes.index'));

        $response->assertOk();
        $response->assertSee($ukName);
    }

    public function test_create_form_can_be_rendered(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.product-attributes.create'));

        $response->assertOk();
    }

    public function test_an_attribute_can_be_created_with_nested_values(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.product-attributes.store'), $this->validPayload());

        $response->assertRedirect(route('admin.product-attributes.index'));

        $this->assertDatabaseHas('product_attributes', ['slug' => 'color']);
        $this->assertDatabaseHas('product_attribute_translations', ['locale' => 'uk', 'name' => 'Колір']);
        $this->assertDatabaseHas('product_attribute_values', ['slug' => 'red']);
        $this->assertDatabaseHas('product_attribute_value_translations', ['locale' => 'en', 'value' => 'Red']);
    }

    public function test_store_requires_a_slug_and_a_name_for_every_configured_locale(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.product-attributes.store'), $this->validPayload([
            'slug' => '',
            'translations' => ['uk' => ['name' => ''], 'en' => ['name' => '']],
        ]));

        $response->assertSessionHasErrors(['slug', 'translations.uk.name', 'translations.en.name']);
    }

    public function test_store_rejects_a_duplicate_attribute_slug(): void
    {
        $this->actingAs($this->admin, 'admin')->post(route('admin.product-attributes.store'), $this->validPayload());

        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.product-attributes.store'), $this->validPayload());

        $response->assertSessionHasErrors('slug');
    }

    public function test_store_rejects_two_values_sharing_the_same_slug_in_one_submission(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.product-attributes.store'), $this->validPayload([
            'values' => [
                ['slug' => 'red', 'translations' => ['uk' => ['value' => 'Червоний'], 'en' => ['value' => 'Red']]],
                ['slug' => 'red', 'translations' => ['uk' => ['value' => 'Ще один'], 'en' => ['value' => 'Another']]],
            ],
        ]));

        $response->assertSessionHasErrors('values.0.slug');
    }

    public function test_edit_form_renders_with_prefilled_values(): void
    {
        $attribute = ProductAttribute::factory()
            ->has(ProductAttributeValue::factory(), 'values')
            ->create();

        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.product-attributes.edit', $attribute->id));

        $response->assertOk();
    }

    public function test_edit_returns_404_for_a_missing_attribute(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.product-attributes.edit', 999999));

        $response->assertNotFound();
    }

    public function test_updating_can_keep_one_value_drop_another_and_add_a_new_one(): void
    {
        $this->actingAs($this->admin, 'admin')->post(route('admin.product-attributes.store'), $this->validPayload([
            'values' => [
                ['slug' => 'red', 'translations' => ['uk' => ['value' => 'Червоний'], 'en' => ['value' => 'Red']]],
                ['slug' => 'blue', 'translations' => ['uk' => ['value' => 'Синій'], 'en' => ['value' => 'Blue']]],
            ],
        ]));

        $attribute = ProductAttribute::sole();
        $redValue = ProductAttributeValue::where('slug', 'red')->sole();
        $blueValue = ProductAttributeValue::where('slug', 'blue')->sole();

        $this->actingAs($this->admin, 'admin')->put(route('admin.product-attributes.update', $attribute->id), $this->validPayload([
            'values' => [
                ['id' => $redValue->id, 'slug' => 'red', 'translations' => ['uk' => ['value' => 'Червоний'], 'en' => ['value' => 'Red']]],
                ['slug' => 'green', 'translations' => ['uk' => ['value' => 'Зелений'], 'en' => ['value' => 'Green']]],
            ],
        ]));

        $this->assertDatabaseHas('product_attribute_values', ['id' => $redValue->id]);
        $this->assertDatabaseMissing('product_attribute_values', ['id' => $blueValue->id]);
        $this->assertDatabaseHas('product_attribute_values', ['product_attribute_id' => $attribute->id, 'slug' => 'green']);
    }

    public function test_an_attribute_can_be_deleted(): void
    {
        $attribute = ProductAttribute::factory()
            ->has(ProductAttributeValue::factory(), 'values')
            ->create();
        $value = $attribute->values->first();

        $response = $this->actingAs($this->admin, 'admin')->delete(route('admin.product-attributes.destroy', $attribute->id));

        $response->assertRedirect(route('admin.product-attributes.index'));
        $this->assertDatabaseMissing('product_attributes', ['id' => $attribute->id]);
        $this->assertDatabaseMissing('product_attribute_values', ['id' => $value->id]);
    }
}
