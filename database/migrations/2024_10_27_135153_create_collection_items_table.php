<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('smart_collection_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignUuid('smart_collection_id')
                ->nullable()
                ->constrained('smart_collections')
                ->cascadeOnDelete();

            $table->string('title');
            $table->jsonb('data');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smart_collection_items');
    }
};
