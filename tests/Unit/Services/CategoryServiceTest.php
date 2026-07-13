<?php

namespace Tests\Unit\Services;

use App\Dto\Category\CategoryInputDto;
use App\Dto\Category\CategoryTranslationDto;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class CategoryServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_rolls_back_the_category_row_when_a_translation_write_fails(): void
    {
        $existing = Category::factory()->create();
        $existing->translations()->where('locale', 'uk')->update(['slug' => 'taken-slug']);

        $countBefore = Category::count();

        // Bypasses HTTP validation on purpose — this is exactly the kind of
        // failure validation can't always prevent (e.g. a race condition),
        // and what the transaction in CategoryService::create() exists for.
        $dto = new CategoryInputDto(
            parentId: null,
            image: null,
            isActive: true,
            sortOrder: 1,
            translations: collect([
                new CategoryTranslationDto('uk', 'Duplicate', 'taken-slug', null, null, null, null),
                new CategoryTranslationDto('en', 'Duplicate', 'duplicate-en', null, null, null, null),
            ]),
        );

        try {
            app(CategoryService::class)->create($dto);
            $this->fail('Expected a RuntimeException to be thrown.');
        } catch (RuntimeException $e) {
            $this->assertInstanceOf(UniqueConstraintViolationException::class, $e->getPrevious());
        }

        $this->assertSame($countBefore, Category::count());
    }

    public function test_options_excludes_the_given_category_and_all_of_its_descendants(): void
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create(['parent_id' => $parent->id]);
        $grandchild = Category::factory()->create(['parent_id' => $child->id]);
        $unrelated = Category::factory()->create();

        $options = app(CategoryService::class)->options(excludeId: $parent->id);

        $this->assertArrayNotHasKey($parent->id, $options);
        $this->assertArrayNotHasKey($child->id, $options);
        $this->assertArrayNotHasKey($grandchild->id, $options);
        $this->assertArrayHasKey($unrelated->id, $options);
    }

    public function test_tree_nests_a_category_under_its_parent(): void
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create(['parent_id' => $parent->id]);

        $tree = app(CategoryService::class)->tree();

        $parentDto = $tree->firstWhere('id', $parent->id);

        $this->assertNotNull($parentDto);
        $this->assertTrue($parentDto->children->contains('id', $child->id));
    }
}
