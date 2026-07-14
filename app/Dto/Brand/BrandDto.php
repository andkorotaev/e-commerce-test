<?php

namespace App\Dto\Brand;

use App\Models\Brand;

final readonly class BrandDto
{
    public function __construct(
        public int $id,
        public string $slug,
        public string $name,
        public ?string $logo,
        public bool $isActive,
    ) {}

    public static function fromModel(Brand $brand): self
    {
        return new self(
            id: $brand->id,
            slug: $brand->slug,
            name: $brand->name,
            logo: $brand->logo,
            isActive: $brand->is_active,
        );
    }
}
