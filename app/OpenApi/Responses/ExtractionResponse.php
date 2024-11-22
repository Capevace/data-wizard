<?php

namespace App\OpenApi\Responses;

use App\OpenApi\Schemas\ExtractionSchema;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class ExtractionResponse extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        $response = Schema::object()->properties(
            ExtractionSchema::ref('Extraction')
            ->type('Extraction')
        );

        return Response::create('Extraction')
            ->description('Extraction response')
            ->content(
                MediaType::json()
                    ->schema($response)
            );
    }
}
