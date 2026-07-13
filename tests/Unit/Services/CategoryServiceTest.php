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

    public function test_roots_only_returns_active_top_level_categories(): void
    {
        $activeRoot = Category::factory()->create();
        $inactiveRoot = Category::factory()->create(['is_active' => false]);
        $child = Category::factory()->create(['parent_id' => $activeRoot->id]);

        $roots = app(CategoryService::class)->roots();

        $this->assertTrue($roots->contains('id', $activeRoot->id));
        $this->assertFalse($roots->contains('id', $inactiveRoot->id));
        $this->assertFalse($roots->contains('id', $child->id));
    }

    public function test_navigation_builds_a_three_level_active_tree(): void
    {
        $root = Category::factory()->create();
        $level2 = Category::factory()->create(['parent_id' => $root->id]);
        $level3 = Category::factory()->create(['parent_id' => $level2->id]);

        $navigation = app(CategoryService::class)->navigation();

        $rootDto = $navigation->firstWhere('id', $root->id);
        $this->assertNotNull($rootDto);

        $level2Dto = $rootDto->children->firstWhere('id', $level2->id);
        $this->assertNotNull($level2Dto);

        $this->assertTrue($level2Dto->children->contains('id', $level3->id));
    }

    public function test_navigation_excludes_inactive_categories_at_any_level(): void
    {
        $root = Category::factory()->create();
        $activeLevel2 = Category::factory()->create(['parent_id' => $root->id]);
        $inactiveLevel2 = Category::factory()->create(['parent_id' => $root->id, 'is_active' => false]);
        $inactiveLevel3 = Category::factory()->create(['parent_id' => $activeLevel2->id, 'is_active' => false]);

        $navigation = app(CategoryService::class)->navigation();

        $rootDto = $navigation->firstWhere('id', $root->id);
        $this->assertFalse($rootDto->children->contains('id', $inactiveLevel2->id));

        $activeLevel2Dto = $rootDto->children->firstWhere('id', $activeLevel2->id);
        $this->assertFalse($activeLevel2Dto->children->contains('id', $inactiveLevel3->id));
    }

    public function test_find_by_slug_returns_the_matching_category_with_its_active_children(): void
    {
        $category = Category::factory()->create();
        $category->translations()->where('locale', 'uk')->update(['slug' => 'my-category-slug']);

        $activeChild = Category::factory()->create(['parent_id' => $category->id]);
        $inactiveChild = Category::factory()->create(['parent_id' => $category->id, 'is_active' => false]);

        $found = app(CategoryService::class)->findBySlug('my-category-slug', 'uk');

        $this->assertNotNull($found);
        $this->assertSame($category->id, $found->id);
        $this->assertTrue($found->children->contains('id', $activeChild->id));
        $this->assertFalse($found->children->contains('id', $inactiveChild->id));
    }

    public function test_find_by_slug_returns_null_for_an_unknown_slug(): void
    {
        $found = app(CategoryService::class)->findBySlug('no-such-slug', 'uk');

        $this->assertNull($found);
    }

    public function test_find_by_slug_returns_null_for_an_inactive_category(): void
    {
        $category = Category::factory()->create(['is_active' => false]);
        $category->translations()->where('locale', 'uk')->update(['slug' => 'inactive-slug']);

        $found = app(CategoryService::class)->findBySlug('inactive-slug', 'uk');

        $this->assertNull($found);
    }
}
