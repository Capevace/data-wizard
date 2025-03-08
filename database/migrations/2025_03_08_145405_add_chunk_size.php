<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::table('saved_extractors', function (Blueprint $table) {
			$table->integer('chunk_size')->nullable();
		});

        Schema::table('extraction_runs', function (Blueprint $table) {
			$table->integer('chunk_size')->nullable();
		});
	}

	public function down(): void
	{
		Schema::table('saved_extractors', function (Blueprint $table) {
			$table->dropColumn('chunk_size');
        });

        Schema::table('extraction_runs', function (Blueprint $table) {
            $table->dropColumn('chunk_size');
		});
	}
};
