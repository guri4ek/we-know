<?php

declare(strict_types=1);
namespace App\Http;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

final class BackendExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return ['kernel.exception' => ['onException', 100]];
    }

    public function onException(ExceptionEvent $event): void
    {
        $path = $event->getRequest()->getPathInfo();
        if ($path !== '/answer' && !str_starts_with($path, '/a/')) {
            return;
        }
        $exception = $event->getThrowable();
        if ($exception instanceof HttpExceptionInterface && $exception->getStatusCode() < 500) {
            if ($exception->getStatusCode() === 404) {
                $event->setResponse(new Response('Result not found.', 404, ['Content-Type' => 'text/plain; charset=UTF-8', 'Cache-Control' => 'no-store', 'X-Robots-Tag' => 'noindex, nofollow']));
            }
            return;
        }
        $event->setResponse($path === '/answer' ? ApiError::response(503, 'answer_unavailable', 'An answer is temporarily unavailable.') : new Response('Result temporarily unavailable.', 503, ['Content-Type' => 'text/plain; charset=UTF-8', 'Cache-Control' => 'no-store', 'X-Robots-Tag' => 'noindex, nofollow']));
    }
}
