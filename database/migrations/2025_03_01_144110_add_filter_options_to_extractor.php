<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::table('saved_extractors', function (Blueprint $table) {
            $table->boolean('include_text')->default(true);
            $table->boolean('include_embedded_images')->default(true);
            $table->boolean('mark_embedded_images')->default(true);
            $table->boolean('include_page_images')->default(false);
            $table->boolean('mark_page_images')->default(false);
		});

        // Also add the columns to the extraction_runs table so that we can store/edit the settings for each run
        Schema::table('extraction_runs', function (Blueprint $table) {
            $table->boolean('include_text')->default(true);
            $table->boolean('include_embedded_images')->default(true);
            $table->boolean('mark_embedded_images')->default(true);
            $table->boolean('include_page_images')->default(false);
            $table->boolean('mark_page_images')->default(false);
		});
	}

	public function down(): void
	{
        Schema::table('extraction_runs', function (Blueprint $table) {
            $table->dropColumn('include_text');
            $table->dropColumn('include_embedded_images');
            $table->dropColumn('mark_embedded_images');
            $table->dropColumn('include_page_images');
            $table->dropColumn('mark_page_images');
        });

		Schema::table('saved_extractors', function (Blueprint $table) {
            $table->dropColumn('include_text');
            $table->dropColumn('include_embedded_images');
            $table->dropColumn('mark_embedded_images');
            $table->dropColumn('include_page_images');
            $table->dropColumn('mark_page_images');
		});
	}
};
