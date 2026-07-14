<?php

namespace App\Models;

use Database\Factories\ProductAttributeValueFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductAttributeValue extends Model
{
    /** @use HasFactory<ProductAttributeValueFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'product_attribute_id',
        'slug',
    ];

    /**
     * @return BelongsTo<ProductAttribute, $this>
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class, 'product_attribute_id');
    }

    /**
     * @return HasMany<ProductAttributeValueTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ProductAttributeValueTranslation::class);
    }

    /**
     * @return HasOne<ProductAttributeValueTranslation, $this>
     */
    public function translation(?string $locale = null): HasOne
    {
        return $this->hasOne(ProductAttributeValueTranslation::class)
            ->where('locale', $locale ?? app()->getLocale());
    }
}
