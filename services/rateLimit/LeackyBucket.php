<?php

namespace app\services\rateLimit;

use app\extensions\components\storage\RedisStorage;
use yii\web\TooManyRequestsHttpException;

/**
 * Class LeackyBucket
 * @package app\services\rateLimit
 */
class LeackyBucket implements RateLimitingInterface
{
    public    $rate;
    public    $windowTime;
    protected $userId;
    protected $redis;

    public function __construct(int $userId, int $rate = 100, int $windowTime = 60)
    {
        $this->rate       = $rate;
        $this->windowTime = $windowTime;
        $this->userId     = $userId;
        $this->redis      = new RedisStorage();
    }

    /**
     * @return bool
     * @throws TooManyRequestsHttpException
     */
    public function canDoAction()
    {
        list($allowance, $lastChecker) = $this->loadCurrentRate();
        $currentTime = time();
        $allowance += (int)(($currentTime - $lastChecker) * $this->rate/$this->windowTime);

        if ($allowance > $this->rate) {
            $allowance = $this->rate;
        }

        if ($allowance < 1) {
            $this->saveCurrentRate(0, $currentTime);
            throw new TooManyRequestsHttpException('Rate limit exceeded.');
        }

        $this->saveCurrentRate($allowance - 1, $currentTime);

        return true;
    }

    /**
     * @return array
     */
    protected function loadCurrentRate() : array
    {
        $cnt = $this->redis->get($this->userId);
        if (empty($cnt)) {
            $result = [$this->rate, time()];
        } else {
            $cnt = explode('_', $cnt);
            $result = [$cnt[0], $cnt[1]];
        }

        return $result;
    }

    /**
     * @param int $allowed
     * @param int $currentTime
     */
    protected function saveCurrentRate(int $allowed, int $currentTime): void
    {
        $this->redis->set($this->userId, $allowed . '_' . $currentTime);
    }
}
