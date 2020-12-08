<?php

namespace app\services\rateLimit;

class Worker
{
    protected $rateLimiting;

    public function __construct(RateLimitingInterface $rateLimiting)
    {
        $this->rateLimiting = $rateLimiting;
    }

    /**
     * @return mixed
     */
    public function doAction()
    {
        return $this->rateLimiting->canDoAction();
    }
}