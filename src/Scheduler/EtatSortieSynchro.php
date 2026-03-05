<?php

namespace App\Scheduler;

use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Console\Messenger\RunCommandMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Trigger\CronExpressionTrigger;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('etatSortie')]
class EtatSortieSynchro implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        return new Schedule()
            ->add(
                RecurringMessage::cron('*/10 * * * *',
                    new RunCommandMessage('app:sortie-etat-synchro')
                )
            );
    }
}
