<?php

namespace App\Dto\Brand;

use Illuminate\Http\UploadedFile;

final readonly class BrandInputDto
{
    public function __construct(
        public string $slug,
        public string $name,
        public ?UploadedFile $logo,
        public bool $isActive,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            slug: $data['slug'],
            name: $data['name'],
            logo: $data['logo'] ?? null,
            isActive: (bool) ($data['is_active'] ?? false),
        );
    }
}
