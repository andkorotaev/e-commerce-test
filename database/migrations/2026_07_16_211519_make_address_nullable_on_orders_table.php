<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Address is now only collected for courier ("address") delivery —
        // plain DB::statement rather than Schema::table()->change() to
        // avoid pulling in doctrine/dbal just for one column tweak.
        DB::statement('ALTER TABLE orders MODIFY address VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE orders MODIFY address VARCHAR(255) NOT NULL');
    }
};
