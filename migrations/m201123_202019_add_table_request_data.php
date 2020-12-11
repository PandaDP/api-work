<?php

use yii\db\Migration;

/**
 * Class m201123_202019_add_table_request_data
 */
class m201123_202019_add_table_request_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand(
            'CREATE TABLE `user_data` (
                    `id` int(11) unsigned not null AUTO_INCREMENT,
                    `userId` int(11) unsigned not null,
                    `typeId` varchar(20) not null,
                    `typeValue` varchar(32) not null,
                    `value` json not null,
                    `createdAt` datetime not null,
                    PRIMARY KEY id(id),
                    INDEX userType(userId, typeId, typeValue),
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
)'
        )
            ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201123_202019_add_table_request_data cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201123_202019_add_table_request_data cannot be reverted.\n";

        return false;
    }
    */
}
