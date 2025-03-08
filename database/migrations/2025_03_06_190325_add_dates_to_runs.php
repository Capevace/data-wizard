<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::table('extraction_runs', function (Blueprint $table) {
            $table->timestamp('started_at')->nullable();
			$table->timestamp('finished_at')->nullable();
		});
	}

	public function down(): void
	{
		Schema::table('extraction_runs', function (Blueprint $table) {
            $table->dropColumn('started_at');
			$table->dropColumn('finished_at');
		});
	}
};
