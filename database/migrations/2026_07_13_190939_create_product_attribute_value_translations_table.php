<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_attribute_value_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_attribute_value_id')
                ->constrained(indexName: 'attribute_value_translations_value_id_foreign')
                ->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('value');
            $table->timestamps();

            $table->unique(['product_attribute_value_id', 'locale'], 'attribute_value_translations_value_locale_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attribute_value_translations');
    }
};
