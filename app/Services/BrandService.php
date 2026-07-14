<?php

namespace App\Services;

use App\Dto\Brand\BrandDto;
use App\Dto\Brand\BrandInputDto;
use App\Repositories\BrandRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class BrandService
{
    public function __construct(
        protected BrandRepository $brands,
    ) {}

    /**
     * @return Collection<int, BrandDto>
     */
    public function list(): Collection
    {
        return $this->brands->all();
    }

    /**
     * @return Collection<int, BrandDto>
     */
    public function active(): Collection
    {
        return $this->brands->active();
    }

    public function find(int $id): ?BrandDto
    {
        return $this->brands->find($id);
    }

    /**
     * @throws RuntimeException
     */
    public function create(BrandInputDto $dto): BrandDto
    {
        $logoPath = null;

        try {
            $logoPath = $dto->logo?->store('brands', 'public');

            return $this->brands->create([
                'slug' => $dto->slug,
                'name' => $dto->name,
                'logo' => $logoPath,
                'is_active' => $dto->isActive,
            ]);
        } catch (Throwable $e) {
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }

            report($e);

            throw new RuntimeException('Failed to create brand.', previous: $e);
        }
    }

    /**
     * @throws RuntimeException
     */
    public function update(int $brandId, BrandInputDto $dto): BrandDto
    {
        $existing = $this->brands->find($brandId);

        if (! $existing) {
            throw new RuntimeException("Brand #{$brandId} not found.");
        }

        $newLogoPath = null;

        try {
            $attributes = [
                'slug' => $dto->slug,
                'name' => $dto->name,
                'is_active' => $dto->isActive,
            ];

            if ($dto->logo !== null) {
                $newLogoPath = $dto->logo->store('brands', 'public');
                $attributes['logo'] = $newLogoPath;
            }

            $brand = $this->brands->update($brandId, $attributes);

            if ($newLogoPath && $existing->logo) {
                Storage::disk('public')->delete($existing->logo);
            }

            return $brand;
        } catch (Throwable $e) {
            if ($newLogoPath) {
                Storage::disk('public')->delete($newLogoPath);
            }

            report($e);

            throw new RuntimeException("Failed to update brand #{$brandId}.", previous: $e);
        }
    }

    /**
     * @throws RuntimeException
     */
    public function delete(int $brandId): void
    {
        $brand = $this->brands->find($brandId);

        if (! $brand) {
            return;
        }

        try {
            $this->brands->delete($brandId);

            if ($brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException("Failed to delete brand #{$brandId}.", previous: $e);
        }
    }
}
