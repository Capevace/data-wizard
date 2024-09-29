<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('saved_extractors', function (Blueprint $table) {
            $table->string('page_title')->nullable();
            $table->string('logo')->nullable();

            $table->string('introduction_view_heading')->nullable();
            $table->text('introduction_view_description')->nullable();
            $table->text('introduction_view_next_button_label')->nullable();

            $table->string('bucket_view_heading')->nullable();
            $table->text('bucket_view_description')->nullable();
            $table->string('bucket_view_back_button_label')->nullable();
            $table->string('bucket_view_continue_button_label')->nullable();
            $table->string('bucket_view_begin_button_label')->nullable();

            $table->string('extraction_view_heading')->nullable();
            $table->text('extraction_view_description')->nullable();
            $table->string('extraction_view_back_button_label')->nullable();
            $table->string('extraction_view_continue_button_label')->nullable();
            $table->string('extraction_view_restart_button_label')->nullable();
            $table->string('extraction_view_start_button_label')->nullable();
            $table->string('extraction_view_cancel_button_label')->nullable();
            $table->string('extraction_view_pause_button_label')->nullable();

            $table->string('results_view_heading')->nullable();
            $table->text('results_view_description')->nullable();
            $table->string('results_view_back_button_label')->nullable();
            $table->string('results_view_submit_button_label')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('saved_extractors', function (Blueprint $table) {
            $table->dropColumn([
                'page_title',
                'logo',

                'introduction_view_heading',
                'introduction_view_description',
                'introduction_view_next_button_label',

                'bucket_view_heading',
                'bucket_view_description',
                'bucket_view_back_button_label',
                'bucket_view_continue_button_label',
                'bucket_view_begin_button_label',

                'extraction_view_heading',
                'extraction_view_description',
                'extraction_view_back_button_label',
                'extraction_view_continue_button_label',
                'extraction_view_restart_button_label',
                'extraction_view_start_button_label',
                'extraction_view_cancel_button_label',
                'extraction_view_pause_button_label',

                'results_view_heading',
                'results_view_description',
                'results_view_back_button_label',
                'results_view_submit_button_label',
            ]);
        });
    }
};
