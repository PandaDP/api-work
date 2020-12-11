<?php


namespace app\services\rateLimit;


use app\extensions\components\storage\RedisStorage;
use yii\web\TooManyRequestsHttpException;

class SlidingWindow implements RateLimitingInterface
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
        $currentTime         = time();
        $startTime           = $this->getStartCurrentTime();
        $elapsedTime         = !(empty($startTime)) ? doubleval($currentTime - $startTime) : 0;
        $prevCount           = $this->getPrevRequestCount();
        $currentCountRequest = $this->getCurrentCountRequest();

        if (!empty($startTime)) {
            if ($elapsedTime >= $this->windowTime) {
                if ($elapsedTime >= $this->windowTime * 2) {
                    $startTime           = $currentTime;
                    $prevCount           = 0;
                    $currentCountRequest = 0;

                    $elapsedTime = 0;
                } else {
                    $startTime           += $this->windowTime;
                    $prevCount           = $currentCountRequest;
                    $currentCountRequest = 0;

                    $elapsedTime = $currentTime - $startTime;
                }
            }
        } else {
            $startTime = $currentTime;
        }

        $weightedRequestCount = $prevCount * (doubleval(
                ($this->windowTime - $elapsedTime) / $this->windowTime
            )) + $currentCountRequest + 1;

        if ($weightedRequestCount <= $this->rate) {
            $currentCountRequest++;
            $this->setCurrentCountRequest($currentCountRequest);
            $this->setPrevRequestCount($prevCount);
            $this->setStartCurrentTime($startTime);
            return true;
        } else {
            throw new TooManyRequestsHttpException('Rate limit exceeded.');
        }
    }

    /**
     * @return string|null
     */
    public function getStartCurrentTime()
    {
        $startTime = $this->redis->get($this->userId . '_startTime');

        return $startTime;
    }

    /**
     * @param int $time
     */
    public function setStartCurrentTime(int $time)
    {
        $this->redis->set($this->userId . '_startTime', $time);
    }

    /**
     * @return int|string
     */
    public function getPrevRequestCount()
    {
        $prevCount = $this->redis->get($this->userId . '_prevCount');

        return $prevCount ?? 0;
    }

    /**
     * @param int $count
     */
    public function setPrevRequestCount(int $count)
    {
        $this->redis->set($this->userId . '_prevCount', $count);
    }

    /**
     * @return int|string
     */
    public function getCurrentCountRequest()
    {
        $currentCount = $this->redis->get($this->userId . '_currentCountRequest');

        return $currentCount ?? 0;
    }

    /**
     * @param int $count
     */
    public function setCurrentCountRequest(int $count)
    {
        $this->redis->set($this->userId . '_currentCountRequest', $count);
    }
}