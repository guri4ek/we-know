<?php

declare(strict_types=1);
namespace App\Http;

use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class BackendConsoleErrorSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return ['console.error' => ['onError', 100]];
    }

    public function onError(ConsoleErrorEvent $event): void
    {
        $name = $event->getCommand()?->getName() ?? '';
        if (str_starts_with($name, 'migrations:') || $name === 'app:catalog:import') {
            $event->setError(new \RuntimeException('Database operation failed. Check database configuration and availability.'));
        }
    }
}
