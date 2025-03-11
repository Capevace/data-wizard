<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\spin;

class ClearActorsCommand extends Command
{
	protected $signature = 'clear:actors';

	protected $description = 'Truncates the actor messages table to remove the message data.';

	public function handle(): void
	{
        spin(
            callback: fn () => sleep(2) || DB::table('actor_messages')->truncate(),
            message: 'Truncating actor_messages table...',
        );

        $this->info('Actor messages cleared!');
	}
}
