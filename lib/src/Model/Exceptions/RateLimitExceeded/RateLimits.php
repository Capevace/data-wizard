<?php

namespace Capevace\MagicImport\Model\Exceptions\RateLimitExceeded;

use Carbon\CarbonImmutable;

readonly class RateLimits
{
    public function __construct(
        public ?string $model,
        public ?int $limit,
        public ?int $used,
        public ?int $requested,
        public ?CarbonImmutable $retryAfter
    ) {}

    public static function parseRateLimitError(string $errorMessage): RateLimits
    {
        $model = static::extractModelFromErrorMessage($errorMessage);
        $limit = static::extractLimitFromErrorMessage($errorMessage);
        $used = static::extractUsedFromErrorMessage($errorMessage);
        $requested = static::extractRequestedFromErrorMessage($errorMessage);
        $retryAfterSeconds = static::extractRetryAfterFromErrorMessage($errorMessage);
        $retryAfter = $retryAfterSeconds
            ? CarbonImmutable::now()->addSeconds($retryAfterSeconds)
            : null;

        return new RateLimits($model, $limit, $used, $requested, $retryAfter);
    }

    protected static function extractModelFromErrorMessage(string $errorMessage): ?string
    {
        $pattern = '/Rate limit reached for model `(\w+)`/';
        $matches = [];
        if (preg_match($pattern, $errorMessage, $matches)) {
            return $matches[1];
        }

        return null;
    }

    protected static function extractLimitFromErrorMessage(string $errorMessage): ?int
    {
        $pattern = '/Limit (\d+)/';
        $matches = [];
        if (preg_match($pattern, $errorMessage, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    protected static function extractUsedFromErrorMessage(string $errorMessage): ?int
    {
        $pattern = '/Used (\d+)/';
        $matches = [];
        if (preg_match($pattern, $errorMessage, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    protected static function extractRequestedFromErrorMessage(string $errorMessage): ?int
    {
        $pattern = '/Requested ~(\d+)/';
        $matches = [];
        if (preg_match($pattern, $errorMessage, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    protected static function extractRetryAfterFromErrorMessage(string $errorMessage): ?float
    {
        $pattern = '/Please try again in (\d+\.\d+)s/';
        $matches = [];
        if (preg_match($pattern, $errorMessage, $matches)) {
            return (float) $matches[1];
        }

        return null;
    }
}
