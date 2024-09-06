<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('provider');
            $table->string('type');
            $table->string('secret');

            $table->timestamps();

            $table->index(['user_id', 'provider']);
            $table->unique(['user_id', 'provider', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};
