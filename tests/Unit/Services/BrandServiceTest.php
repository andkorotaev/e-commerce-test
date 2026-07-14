<?php

namespace Tests\Unit\Services;

use App\Dto\Brand\BrandInputDto;
use App\Models\Brand;
use App\Services\BrandService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class BrandServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_cleans_up_the_uploaded_logo_when_the_slug_is_already_taken(): void
    {
        Storage::fake('public');

        Brand::factory()->create(['slug' => 'taken-slug']);

        $dto = new BrandInputDto(
            slug: 'taken-slug',
            name: 'Duplicate',
            logo: UploadedFile::fake()->image('logo.jpg'),
            isActive: true,
        );

        try {
            app(BrandService::class)->create($dto);
            $this->fail('Expected a RuntimeException to be thrown.');
        } catch (RuntimeException $e) {
            $this->assertInstanceOf(UniqueConstraintViolationException::class, $e->getPrevious());
        }

        $files = Storage::disk('public')->allFiles('brands');
        $this->assertEmpty($files);
    }

    public function test_delete_removes_the_brand_and_its_logo_file(): void
    {
        Storage::fake('public');
        $path = 'brands/to-delete.jpg';
        Storage::disk('public')->put($path, 'content');
        $brand = Brand::factory()->create(['logo' => $path]);

        app(BrandService::class)->delete($brand->id);

        $this->assertDatabaseMissing('brands', ['id' => $brand->id]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_find_returns_null_for_an_unknown_id(): void
    {
        $found = app(BrandService::class)->find(999999);

        $this->assertNull($found);
    }
}
