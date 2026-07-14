<?php

namespace App\Models;

use Database\Factories\ProductAttributeValueTranslationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttributeValueTranslation extends Model
{
    /** @use HasFactory<ProductAttributeValueTranslationFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'product_attribute_value_id',
        'locale',
        'value',
    ];

    /**
     * @return BelongsTo<ProductAttributeValue, $this>
     */
    public function attributeValue(): BelongsTo
    {
        return $this->belongsTo(ProductAttributeValue::class, 'product_attribute_value_id');
    }
}
