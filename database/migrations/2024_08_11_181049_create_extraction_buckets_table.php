<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extraction_buckets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('description')->nullable();
            $table->foreignUuid('created_by_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('status');
            $table->timestamp('started_at')->nullable();
            $table->string('extractor_id');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extraction_buckets');
    }
};
