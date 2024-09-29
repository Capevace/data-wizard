<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('saved_extractors', function (Blueprint $table) {
            $table->boolean('allow_download')->default(true);
            $table->boolean('enable_webhook')->default(false);

            $table->string('webhook_url')->nullable();
            $table->string('webhook_secret')->nullable();

            $table->string('redirect_url')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('saved_extractors', function (Blueprint $table) {
            $table->dropColumn([
                'allow_download',
                'enable_webhook',
                'webhook_url',
                'webhook_secret',
                'redirect_url',
            ]);
        });
    }
};
