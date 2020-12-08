<?php

namespace app\services;

use app\services\rateLimit\LeackyBucket;
use app\services\rateLimit\Worker;
use Yii;

class User
{
    public $id;

    public static $authorization = [
        '4b06c4b6a16e4dc791033da5259936c48f1d505afb4f492fd7302985af049159' => ['WawNE5nN1J', 'pGyoAkVmEx',],
        '622b0e3a850199c3d75aac02e73a42cc02f65e303fc15e41b4968ef8c858ddd2' => ['pGyoA1EoEx', 'drKQ6KnmaE']
    ];

    /**
     * @param $token
     * @return mixed|null
     */
    public static function identityUser($token) : ?int
    {
        if (isset(self::$authorization[$token['key']]) && in_array($token['id'], self::$authorization[$token['key']])) {
            $userId = Yii::$app->hashSecretCode->decode($token['id'])[0];
            if (!empty($userId)) {
                $worker = new Worker(new LeackyBucket($userId, 2, 10));
                $worker->doAction();

                return $userId;
            }
        }

        return null;
    }
}
