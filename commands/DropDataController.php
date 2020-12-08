<?php


namespace app\commands;

use app\extensions\formatters\DatetimeFormatter;
use app\extensions\formatters\RequestFormatter;
use app\models\UserData;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Delete records older than a specified number of days (enter the "force" flag to delete without confirmation)
 */
class DropDataController extends Controller
{
    private $userData;

    public function actionIndex(string $flag = '')
    {
        $countOfDays = $this->prompt(
            "Number of days of relevance (records older than this period will be deleted): ",
            [
                'required'  => true,
                'pattern'   => '[\d]',
                'validator' => function ($input, &$error) {
                    if (!is_numeric($input)) {
                        $error = 'Number of days must be integer';
                        return false;
                    }
                    return true;
                },
            ]
        );

        $dateFrom       = DatetimeFormatter::nDaysBefore((int)$countOfDays);
        $this->userData = new UserData();

        if ('force' === $flag) {
            echo $this->deletingProcess($dateFrom);
        } else {
            $examplesData = $this->userData->getExamplesDataForDelete($dateFrom);
            if (!empty($examplesData)) {
                if (
                $this->confirm(
                    RequestFormatter::getDataForConsole($examplesData) . "Are you sure you want to delete?[y/n]"
                )
                ) {
                    echo $this->deletingProcess($dateFrom);
                } else {
                    echo "Aborting\n";
                    return ExitCode::UNSPECIFIED_ERROR;
                }
            } else {
                echo "Does not have any data\n";
            }
        }

        return ExitCode::OK;
    }

    private function deletingProcess(string $dateFrom = '')
    {
        $time = rand(5, 10);
        do {
            echo $time . "\n";
            sleep(1);
            $time--;
        } while ($time != 0);

        $this->userData::deleteAll('createdAt <= :date', ['date' => $dateFrom]);

        Console::startProgress(0, 100);

        foreach (range(0, 100) as $v) {
            usleep(1000);
            Console::updateProgress($v, 100);
        }

        Console::endProgress("Deleted" . PHP_EOL);
    }
}
