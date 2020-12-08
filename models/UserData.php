<?php


namespace app\models;

use app\extensions\formatters\DatetimeFormatter;
use yii\db\ActiveRecord;

class UserData extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'user_data';
    }

    /**
     * @return array|array[]
     */
    public function rules(): array
    {
        return [
            [
                [
                    'id',
                    'userId',
                    'typeId',
                    'typeValue',
                    'value',
                    'createdAt'
                ],
                'safe'
            ],
        ];
    }

    /**
     * @param array $attributes
     * @return bool
     */
    public function saveData(array $attributes = []): bool
    {
        $newObj            = new self();
        $newObj->userId    = $attributes['userId'];
        $newObj->typeId    = $attributes['typeId'];
        $newObj->typeValue = $attributes['typeValue'];
        $newObj->value      = $attributes['value'];
        $newObj->createdAt = DatetimeFormatter::getCurrentDatetime();

        return $newObj->save();
    }

    /**
     * @param string $datetime
     * @return array
     */
    public function getExamplesDataForDelete(string $datetime): array
    {
        return self::find()
            ->where('createdAt <= :date', ['date' => $datetime])
            ->orderBy(['createdAt' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();
    }
}