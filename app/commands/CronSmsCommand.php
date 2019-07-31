<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CronSmsCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'command:cronsms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $jobs = \models\SmsJobs::all();

        foreach ($jobs as $job) {
            if ($job->accepted_time) {
                $times = explode(':', $job->accepted_time);
                if ($times[0] < (int)date('H') && $times[1] > (int)date('H')) {
                    \components\Sms::send($job->phone, $job->message);
                    $job->delete();
                }
            }
        }
    }
}
