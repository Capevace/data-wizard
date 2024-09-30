<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extraction_runs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('status');
            $table->json('result_json')->nullable();
            $table->text('partial_result_json')->nullable();
            $table->foreignUuid('started_by_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignUuid('bucket_id')
                ->constrained('extraction_buckets')
                ->cascadeOnDelete();

            $table->json('target_schema')
                ->nullable();

            $table->json('token_stats')
                ->nullable();

            $table->json('error')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extraction_runs');
    }
};
