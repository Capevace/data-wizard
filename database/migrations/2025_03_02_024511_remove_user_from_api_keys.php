<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::table('api_keys', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'provider']);
            $table->dropUnique(['user_id', 'provider', 'type']);

            $table->dropForeign(['user_id']);
			$table->dropColumn('user_id');

            $table->index(['provider']);
            $table->unique(['provider', 'type']);
		});
	}

	public function down(): void
	{
		Schema::table('api_keys', function (Blueprint $table) {
            $table->dropIndex(['provider']);
            $table->dropUnique(['provider', 'type']);

			$table->foreignUuid('user_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->index(['user_id', 'provider']);
            $table->unique(['user_id', 'provider', 'type']);
		});
	}
};
