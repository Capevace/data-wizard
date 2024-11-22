<?php

namespace App\OpenApi\Schemas;

use GoldSpecDigital\ObjectOrientedOAS\Contracts\SchemaContract;
use GoldSpecDigital\ObjectOrientedOAS\Objects\AllOf;
use GoldSpecDigital\ObjectOrientedOAS\Objects\AnyOf;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Not;
use GoldSpecDigital\ObjectOrientedOAS\Objects\OneOf;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;

class ExtractionSchema extends SchemaFactory implements Reusable
{
    /**
     * @return AllOf|OneOf|AnyOf|Not|Schema
     */
    public function build(): SchemaContract
    {
        return Schema::object('Extraction')
            ->properties(
                Schema::string('id')->default(null),
                Schema::string('started_by_id')->default('NULL'),
                Schema::string('bucket_id')->default(null),
                Schema::string('status')->default(null),
                Schema::string('result_json')->default('NULL'),
                Schema::string('partial_result_json')->default('NULL'),
                Schema::string('target_schema')->default('NULL'),
                Schema::string('token_stats')->default('NULL'),
                Schema::string('error')->default('NULL'),
                Schema::string('created_at')->format(Schema::FORMAT_DATE_TIME)->default(null),
                Schema::string('updated_at')->format(Schema::FORMAT_DATE_TIME)->default(null),
                Schema::string('strategy')->default('simple'),
                Schema::string('saved_extractor_id')->default(null)
            );
    }
}
