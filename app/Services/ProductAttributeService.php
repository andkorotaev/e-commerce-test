<?php

namespace App\Services;

use App\Dto\ProductAttribute\ProductAttributeDto;
use App\Dto\ProductAttribute\ProductAttributeInputDto;
use App\Dto\ProductAttribute\ProductAttributeValueInputDto;
use App\Repositories\ProductAttributeRepository;
use App\Repositories\ProductAttributeTranslationRepository;
use App\Repositories\ProductAttributeValueRepository;
use App\Repositories\ProductAttributeValueTranslationRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class ProductAttributeService
{
    public function __construct(
        protected ProductAttributeRepository $attributes,
        protected ProductAttributeTranslationRepository $translations,
        protected ProductAttributeValueRepository $values,
        protected ProductAttributeValueTranslationRepository $valueTranslations,
    ) {}

    /**
     * @return Collection<int, ProductAttributeDto>
     */
    public function list(): Collection
    {
        return $this->attributes->all();
    }

    public function find(int $id): ?ProductAttributeDto
    {
        return $this->attributes->find($id);
    }

    /**
     * @throws RuntimeException
     */
    public function create(ProductAttributeInputDto $dto): ProductAttributeDto
    {
        try {
            return DB::transaction(function () use ($dto) {
                $attribute = $this->attributes->create(['slug' => $dto->slug]);

                foreach ($dto->translations as $translation) {
                    $this->translations->upsert($attribute->id, $translation);
                }

                foreach ($dto->values as $value) {
                    $this->createValue($attribute->id, $value);
                }

                return $this->attributes->find($attribute->id);
            });
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException('Failed to create product attribute.', previous: $e);
        }
    }

    /**
     * @throws RuntimeException
     */
    public function update(int $attributeId, ProductAttributeInputDto $dto): ProductAttributeDto
    {
        if (! $this->attributes->find($attributeId)) {
            throw new RuntimeException("Product attribute #{$attributeId} not found.");
        }

        try {
            return DB::transaction(function () use ($attributeId, $dto) {
                $this->attributes->update($attributeId, ['slug' => $dto->slug]);

                foreach ($dto->translations as $translation) {
                    $this->translations->upsert($attributeId, $translation);
                }

                $this->values->deleteExcept($attributeId, $dto->values->pluck('id')->filter()->all());

                foreach ($dto->values as $value) {
                    if ($value->id !== null) {
                        $this->values->update($value->id, $value->slug);

                        foreach ($value->translations as $translation) {
                            $this->valueTranslations->upsert($value->id, $translation);
                        }
                    } else {
                        $this->createValue($attributeId, $value);
                    }
                }

                return $this->attributes->find($attributeId);
            });
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException("Failed to update product attribute #{$attributeId}.", previous: $e);
        }
    }

    /**
     * @throws RuntimeException
     */
    public function delete(int $attributeId): void
    {
        if (! $this->attributes->find($attributeId)) {
            return;
        }

        try {
            // Values and translations cascade-delete at the DB level.
            $this->attributes->delete($attributeId);
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException("Failed to delete product attribute #{$attributeId}.", previous: $e);
        }
    }

    private function createValue(int $attributeId, ProductAttributeValueInputDto $value): void
    {
        $created = $this->values->create($attributeId, $value->slug);

        foreach ($value->translations as $translation) {
            $this->valueTranslations->upsert($created->id, $translation);
        }
    }
}
