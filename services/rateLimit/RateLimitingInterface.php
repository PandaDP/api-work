<?php

namespace app\services\rateLimit;

interface RateLimitingInterface
{
    public function __construct(int $userId, int $rate, int $windowTime);

    public function canDoAction();
}