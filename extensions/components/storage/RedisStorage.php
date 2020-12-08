<?php

namespace app\extensions\components\storage;


use Predis\Client;
use Yii;
use yii\base\BaseObject;

class RedisStorage extends BaseObject
{

    public  $config;

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return new Client(Yii::$app->redis->config);
    }

    /**
     * @param $key
     * @param $value
     * @param null $duration
     * @return mixed
     */
    public function set($key, $value, $duration = null)
    {
        $result = $this->getClient()->set($key, $value);
        if ($duration) {
            $result = $this->expire($key, $duration);
        }

        return $result;
    }

    /**
     * @param $key
     * @param $seconds
     * @return int
     */
    public function expire($key, $seconds): int
    {
        return $this->getClient()->expire($key, $seconds);
    }

    /**
     * @param $key
     * @return string
     */
    public function get($key): ?string
    {
        return $this->getClient()->get($key);
    }

    /**
     * @param $key
     * @return int
     */
    public function exists($key): int
    {
        return $this->getClient()->exists($key);
    }

    /**
     * @param $key
     * @return int
     */
    public function del($key): int
    {
        return $this->getClient()->del([$key]);
    }
}
