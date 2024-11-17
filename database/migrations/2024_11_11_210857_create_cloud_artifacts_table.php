<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cloud_artifacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignUuid('bucket_id')
                ->nullable()
                ->constrained('extraction_buckets')
                ->cascadeOnDelete();

            $table->string('status');
            $table->string('name');
            $table->string('extension');
            $table->string('mime_type');
            $table->integer('size');
            $table->text('ai_summary')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cloud_artifacts');
    }
};
