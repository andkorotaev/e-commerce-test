<?php

namespace App\Models;

use Database\Factories\ProductTranslationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTranslation extends Model
{
    /** @use HasFactory<ProductTranslationFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'locale',
        'name',
        'slug',
        'h1',
        'meta_title',
        'meta_description',
        'description',
    ];

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
