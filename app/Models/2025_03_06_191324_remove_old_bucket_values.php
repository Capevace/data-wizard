<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
        if (Schema::hasColumn('extraction_buckets', 'status')) {
            Schema::table('extraction_buckets', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }

        if (Schema::hasColumn('extraction_buckets', 'started_at')) {
            Schema::table('extraction_buckets', function (Blueprint $table) {
                $table->dropColumn('started_at');
            });
        }
	}

	public function down(): void
	{
		// We don't recreate the columns because they were never used in the first place
	}
};
