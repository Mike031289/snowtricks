<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoginSubscriber implements EventSubscriberInterface
{
    public function onLoginSubscriber($event): void
    {
        // ...
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'LoginSubscriber' => 'onLoginSubscriber',
        ];
    }
}
