<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actor_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();

            $table->foreignUuid('actor_id')
                ->constrained('actors')
                ->cascadeOnDelete();

            $table->string('role');
            $table->string('type');
            $table->text('text')->nullable();
            $table->json('json')->nullable();

            $table->foreignUuid('media_id')
                ->nullable()
                ->constrained('media')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actor_messages');
    }
};
