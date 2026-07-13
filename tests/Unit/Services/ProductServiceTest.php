<?php

namespace Tests\Unit\Services;

use App\Dto\Product\ProductInputDto;
use App\Dto\Product\ProductTranslationDto;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_rolls_back_the_product_row_and_cleans_up_uploaded_images_when_a_translation_write_fails(): void
    {
        Storage::fake('public');

        $existing = Product::factory()->create();
        $existing->translations()->where('locale', 'uk')->update(['slug' => 'taken-slug']);

        $countBefore = Product::count();

        $dto = new ProductInputDto(
            categoryId: Category::factory()->create()->id,
            sku: null,
            price: 100.0,
            oldPrice: null,
            stock: 5,
            isActive: true,
            sortOrder: 1,
            translations: collect([
                new ProductTranslationDto('uk', 'Duplicate', 'taken-slug', null, null, null, null),
                new ProductTranslationDto('en', 'Duplicate', 'duplicate-en', null, null, null, null),
            ]),
            newImages: collect([UploadedFile::fake()->image('test.jpg')]),
            deleteImageIds: collect(),
        );

        try {
            app(ProductService::class)->create($dto);
            $this->fail('Expected a RuntimeException to be thrown.');
        } catch (RuntimeException $e) {
            $this->assertInstanceOf(UniqueConstraintViolationException::class, $e->getPrevious());
        }

        $this->assertSame($countBefore, Product::count());

        // The image was uploaded before the transaction (same reasoning as
        // categories), so a failed transaction has to clean it up by hand —
        // assert the disk doesn't end up with an orphaned file.
        $files = Storage::disk('public')->allFiles('products');
        $this->assertEmpty($files);
    }

    public function test_delete_removes_the_product_its_translations_and_its_image_files(): void
    {
        Storage::fake('public');

        $product = Product::factory()->create();
        $image = $product->images()->create(['path' => 'products/to-delete.jpg', 'sort_order' => 0]);
        Storage::disk('public')->put('products/to-delete.jpg', 'content');

        app(ProductService::class)->delete($product->id);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('product_translations', ['product_id' => $product->id]);
        $this->assertDatabaseMissing('product_images', ['id' => $image->id]);
        Storage::disk('public')->assertMissing('products/to-delete.jpg');
    }

    public function test_find_returns_null_for_an_unknown_id(): void
    {
        $found = app(ProductService::class)->find(999999);

        $this->assertNull($found);
    }
}
