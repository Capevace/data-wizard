<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('extraction_buckets', function (Blueprint $table) {
            $table->string('created_using')->default('app');
        });

        // Update existing buckets to use the new column
        \Illuminate\Support\Facades\DB::table('extraction_buckets')
            ->where('description', 'Embedded extraction bucket')
            ->update(['created_using' => 'embed']);
    }

    public function down(): void
    {
        Schema::table('extraction_buckets', function (Blueprint $table) {
            $table->dropColumn('created_using');
        });
    }
};
