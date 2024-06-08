<?php

namespace App\Scheduler;

use App\Message\ContactSendMail;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[AsSchedule]
final class ContactSendMailSchedule implements ScheduleProviderInterface
{
    public function __construct(
        private CacheInterface $cache,
    ) {
    }

    public function getSchedule(): Schedule
    {
        return (new Schedule())
            ->add(
                // @TODO - Modify the frequency to suite your needs
                RecurringMessage::every('30 seconds', new ContactSendMail()),
            )
            ->stateful($this->cache)
        ;
    }
}
