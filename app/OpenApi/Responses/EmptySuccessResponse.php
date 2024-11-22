<?php

namespace App\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class EmptySuccessResponse extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        return Response::create('EmptySuccess')
            ->description('No content')
            ->statusCode(204)
            ->content(
                MediaType::json()
                    ->schema(
                        Schema::object()
                            ->properties(
                                Schema::string('success')->default('true')
                            )
                    )
            );
    }
}
