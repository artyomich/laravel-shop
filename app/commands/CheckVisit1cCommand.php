<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CheckVisit1cCommand extends Command
{
    /**
     * @const максимальное время, когда слебует бить тревогу.
     */
    const MAX_TIME_LOST_VISIT = 60;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'command:checkvisit1c';

    /**
     * @var array время начала отправки смс по будням и выходным.
     */
    protected $timeStart = [7, 11];

    /**
     * @var array время конца отправки смс по будням и выходным.
     */
    protected $timeFinish = [23, 23];

    /**
     * @var int gmt по Мосскве.
     */
    protected $gmt = 3;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * @var string
     */
    protected $smsText = 'ПОШК. 1С не опрашивала сайт %d часов %d минут';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $lastVisit = Config::get('1c.lastVisit');
        $diffTime = (time() - $lastVisit) / 60;
        $minutes = round($diffTime);
        $currentHour = (int)date('H') + $this->gmt;
        $dayIsWeekend = (int)(date('N') >= 6);

        //  Сначала проверим, с какого времени нужно проверять, ходила 1с или нет. Если время не подходящее, то запишем, что 1с ходила.
        if ($currentHour < $this->timeStart[$dayIsWeekend] || $currentHour > $this->timeFinish[$dayIsWeekend]) {
            \components\ConfigWriter::set([
                'lastVisit' => time(),
                'lastFailVersion' => \Config::get('1c.lastFailSms')
            ], '', '1c');
            return;
        }

        //  Далее смотрим, если 1С не опрашивала сайт в течении 60 минут, то отправляем смс.
        if ($diffTime > self::MAX_TIME_LOST_VISIT && Config::get('1c.lastFailSms') <= Config::get('1c.lastFailVersion')) {
            $message = sprintf($this->smsText, round($minutes / 60), round($minutes % 60));
            $phones = Config::get('1c.phone');

            foreach ($phones as $phone) {
                \components\Sms::addJob($phone, $message, [
                    $this->timeStart[$dayIsWeekend] - $this->gmt,
                    $this->timeFinish[$dayIsWeekend] - $this->gmt
                ]);
            }

            //  Перезаписываем версию.
            \components\ConfigWriter::set([
                'lastFailSms' => (int)Config::get('1c.lastFailSms') + 1
            ], '', '1c');
        }
    }
}
