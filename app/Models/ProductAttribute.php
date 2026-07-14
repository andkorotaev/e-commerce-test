<?php

namespace App\Models;

use Database\Factories\ProductAttributeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductAttribute extends Model
{
    /** @use HasFactory<ProductAttributeFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'slug',
    ];

    /**
     * @return HasMany<ProductAttributeTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ProductAttributeTranslation::class);
    }

    /**
     * @return HasOne<ProductAttributeTranslation, $this>
     */
    public function translation(?string $locale = null): HasOne
    {
        return $this->hasOne(ProductAttributeTranslation::class)
            ->where('locale', $locale ?? app()->getLocale());
    }

    /**
     * @return HasMany<ProductAttributeValue, $this>
     */
    public function values(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }
}
