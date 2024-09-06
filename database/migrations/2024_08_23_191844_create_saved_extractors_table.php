<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_extractors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->softDeletes();
            $table->timestamps();

            $table->boolean('was_automatically_created');

            $table->string('strategy');
            $table->string('label')
                ->nullable();

            $table->json('json_schema');
            $table->text('output_instructions')
                ->nullable();
        });

        Schema::table('extraction_runs', function (Blueprint $table) {
            $table->string('strategy')
                ->default('simple');

            $table->foreignUuid('saved_extractor_id')
                ->nullable()
                ->constrained('saved_extractors')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('extraction_runs', function (Blueprint $table) {
            $table->dropColumn('strategy');
        });

        Schema::table('extraction_runs', function (Blueprint $table) {
            $table->dropColumn('saved_extractor_id');
        });

        Schema::dropIfExists('saved_extractors');
    }
};
