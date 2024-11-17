<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('smart_collections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->softDeletes();
            $table->string('title');
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->jsonb('json_schema');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smart_collections');
    }
};
