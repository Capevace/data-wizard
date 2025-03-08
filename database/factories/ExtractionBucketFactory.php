<?php

namespace Database\Factories;

use App\Models\ExtractionBucket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ExtractionBucketFactory extends Factory
{
    protected $model = ExtractionBucket::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'description' => $this->faker->text(),
            'status' => $this->faker->word(),
            'extractor_id' => $this->faker->word(),

            'created_by' => User::factory(),
        ];
    }
}
