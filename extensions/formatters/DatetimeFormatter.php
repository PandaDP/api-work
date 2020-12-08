<?php


namespace app\extensions\formatters;


use DateTime;
use Exception;

class DatetimeFormatter
{
    /**
     * @return string
     */
    public static function getCurrentDatetime() :string
    {
        $datetime = new DateTime();
        return $datetime->format('Y-m-d H:i:s');
    }

    /**
     * @param int $n
     * @return string
     * @throws Exception
     */
    public static function nDaysBefore(int $n) :string
    {
        $datetime = new DateTime("-". $n . "days");

        return $datetime->format('Y-m-d 23:59:59');
    }
}