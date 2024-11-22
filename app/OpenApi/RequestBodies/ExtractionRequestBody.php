<?php

namespace App\OpenApi\RequestBodies;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Factories\RequestBodyFactory;

class ExtractionRequestBody extends RequestBodyFactory
{
    public function build(): RequestBody
    {
        return RequestBody::create()
            ->description('Extraction request')
            ->content(
                MediaType::json()
                    ->schema(
                        Schema::object()->properties(
                            Schema::string('extractor_id')->format(Schema::FORMAT_UUID),
                            Schema::string('description')
                                ->nullable()
                        )
                    )
            );
    }
}
