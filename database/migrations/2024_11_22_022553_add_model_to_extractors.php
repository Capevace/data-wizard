<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('saved_extractors', function (Blueprint $table) {
            $table->string('model')
                ->nullable()
                ->index()
                ->after('output_instructions');
        });
    }

    public function down(): void
    {
        Schema::table('saved_extractors', function (Blueprint $table) {
            $table->dropColumn('model');
        });
    }
};
