<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::table('saved_extractors', function (Blueprint $table) {
			$table->dropColumn('was_automatically_created');
		});
	}

	public function down(): void
	{
		Schema::table('saved_extractors', function (Blueprint $table) {
			$table->boolean('was_automatically_created')->default(false);
		});
	}
};
