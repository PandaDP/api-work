<?php

namespace app\extensions\formatters;

use yii\helpers\Html;

class RequestFormatter
{
    /**
     * @param array $data
     * @param bool $isForSave
     * @return array
     */
    public static function prepareRequestData(array $data, bool $isForSave = false): array
    {
        $result['typeId']    = Html::decode($data['key1']);
        $result['typeValue'] = Html::decode($data['key2']);

        if ($isForSave && !empty($data['value'])) {
            $result['value'] = json_decode($data['value']);
        }

        return $result;
    }

    /**
     * @param array $data
     * @return array
     */
    public static function prepareResponseData(array $data): array
    {
        $result = [];

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $result[$key]['key1']  = $value['typeId'];
                $result[$key]['key2']  = $value['typeValue'];
                $result[$key]['value'] = json_decode($value['value']);
            }
        }

        return $result;
    }

    public static function getDataForConsole(array $data): string
    {
        $result = "For examples: \n";
        foreach ($data as $value) {
            $result .= 'key1: ' . $value['typeId'] . ', key2: ' . $value['typeValue'] . ', data: ' . $value['value'] . ";\n";
        }

        return $result;
    }
}