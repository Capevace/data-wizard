<?php

namespace Mateffy\JsonSchema\Concerns;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Mateffy\JsonSchema\Validator;
use Swaggest\JsonSchema\InvalidValue;

trait CanBeValidated
{
	public function validate(array $data, bool $clean = true): void
	{
        try {
            $this->validator->in(json_decode(json_encode($data)));
        } catch (InvalidValue|\LogicException $e) {
            $validator = Validator::fromJsonSchema($this, errors: [
                $this->exceptionPathToValidatorPath($e->path) => $e->error,
            ]);

            throw new ValidationException($validator);
        }
	}

    protected function exceptionPathToValidatorPath(string $path): string
    {
        return str($path)
            ->explode(':')
            ->filter(fn ($part) => !Str::startsWith($part, '#'))
            ->map(function (string $part) {
                return Str::before($part, '->');
            })
            ->values()
            ->implode('.');
    }

	public function invalid(array $data, bool $strict = false): bool
	{
		try {
            $this->validate($data, clean: !$strict);

            return false;
        } catch (ValidationException $e) {
            return true;
        }
	}

	public function validateMany(array $dataArray, bool $clean = true): void
	{
        foreach ($dataArray as $data) {
            $this->validate($data, $clean);
        }
	}
}
