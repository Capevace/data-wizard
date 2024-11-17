<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cloud_artifact_chunks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->softDeletes();

            $table->string('type');

            $table->foreignUuid('cloud_artifact_id')
                ->constrained('cloud_artifacts')
                ->cascadeOnDelete();

            $table->text('text')->nullable();

            $table->integer('page');
            $table->integer('tokens');

            $table->unique(['cloud_artifact_id', 'media_id', 'page']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cloud_artifact_chunks');
    }
};
