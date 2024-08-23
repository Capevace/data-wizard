<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('actor_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();

            $table->foreignUuid('extraction_actor_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('role');
            $table->string('type');
            $table->string('text')->nullable();
            $table->json('json')->nullable();

            $table->foreignUuid('file_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actor_messages');
    }
};
