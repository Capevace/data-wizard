<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
        if (Schema::hasColumn('extraction_buckets', 'extractor_id')) {
            Schema::table('extraction_buckets', function (Blueprint $table) {
                $table->dropColumn('extractor_id');
            });
        }
	}

	public function down(): void
	{
		// This is a one-way migration as the column is not implemented in the codebase to begin with
	}
};
