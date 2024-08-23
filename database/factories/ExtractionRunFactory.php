<?php

namespace Database\Factories;

use App\Models\ExtractionRun;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ExtractionRunFactory extends Factory
{
    protected $model = ExtractionRun::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'status' => $this->faker->word(),
            'result_json' => $this->faker->words(),
            'partial_result_json' => $this->faker->word(),

            'started_by_id' => User::factory(),
        ];
    }
}
