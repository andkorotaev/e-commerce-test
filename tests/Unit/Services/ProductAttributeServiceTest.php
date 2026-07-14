<?php

namespace Tests\Unit\Services;

use App\Dto\ProductAttribute\ProductAttributeInputDto;
use App\Dto\ProductAttribute\ProductAttributeTranslationDto;
use App\Dto\ProductAttribute\ProductAttributeValueInputDto;
use App\Dto\ProductAttribute\ProductAttributeValueTranslationDto;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Services\ProductAttributeService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class ProductAttributeServiceTest extends TestCase
{
    use RefreshDatabase;

    private function attributeDto(): ProductAttributeInputDto
    {
        return new ProductAttributeInputDto(
            slug: 'color',
            translations: collect([
                new ProductAttributeTranslationDto('uk', 'Колір'),
                new ProductAttributeTranslationDto('en', 'Color'),
            ]),
            values: collect([
                // Two values sharing the same slug violate the DB's
                // unique(attribute_id, slug) constraint — the FormRequest's
                // "distinct" rule would normally catch this before it ever
                // reaches the service, so this only exercises the rollback.
                new ProductAttributeValueInputDto(null, 'red', collect([
                    new ProductAttributeValueTranslationDto('uk', 'Червоний'),
                    new ProductAttributeValueTranslationDto('en', 'Red'),
                ])),
                new ProductAttributeValueInputDto(null, 'red', collect([
                    new ProductAttributeValueTranslationDto('uk', 'Інший'),
                    new ProductAttributeValueTranslationDto('en', 'Other'),
                ])),
            ]),
        );
    }

    public function test_create_rolls_back_the_attribute_and_translations_when_a_value_write_fails(): void
    {
        $countBefore = ProductAttribute::count();

        try {
            app(ProductAttributeService::class)->create($this->attributeDto());
            $this->fail('Expected a RuntimeException to be thrown.');
        } catch (RuntimeException $e) {
            $this->assertInstanceOf(QueryException::class, $e->getPrevious());
        }

        $this->assertSame($countBefore, ProductAttribute::count());
        $this->assertDatabaseMissing('product_attribute_translations', ['name' => 'Колір']);
    }

    public function test_delete_cascades_to_values_and_translations(): void
    {
        $attribute = ProductAttribute::factory()
            ->has(ProductAttributeValue::factory(), 'values')
            ->create();
        $value = $attribute->values->first();

        app(ProductAttributeService::class)->delete($attribute->id);

        $this->assertDatabaseMissing('product_attributes', ['id' => $attribute->id]);
        $this->assertDatabaseMissing('product_attribute_translations', ['product_attribute_id' => $attribute->id]);
        $this->assertDatabaseMissing('product_attribute_values', ['id' => $value->id]);
    }

    public function test_find_returns_null_for_an_unknown_id(): void
    {
        $found = app(ProductAttributeService::class)->find(999999);

        $this->assertNull($found);
    }
}
