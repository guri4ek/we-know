<?php

declare(strict_types=1);
namespace App\Http;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Twig\Environment;

final class BackendExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly Environment $twig) {}

    public static function getSubscribedEvents(): array
    {
        return ['kernel.exception' => ['onException', 100]];
    }

    public function onException(ExceptionEvent $event): void
    {
        $path = $event->getRequest()->getPathInfo();
        $exception = $event->getThrowable();
        if ($path === '/answer') {
            if (!$exception instanceof HttpExceptionInterface || $exception->getStatusCode() >= 500) {
                $event->setResponse(ApiError::response(503, 'answer_unavailable', 'An answer is temporarily unavailable.'));
            }
            return;
        }
        $result = str_starts_with($path, '/a/');
        if ($exception instanceof HttpExceptionInterface && $exception->getStatusCode() < 500) {
            if ($exception->getStatusCode() !== 404) {
                return;
            }
            $status = 404;
            $heading = $result ? 'Result not found' : 'Page not found';
            $message = $result ? 'This link does not lead to a saved answer. Check the link, or ask a new question.' : 'Even we don’t have an answer for this address. Let’s get you back.';
        } else {
            $status = 503;
            $heading = 'Temporarily unavailable';
            $message = 'We couldn’t load this page. Please try again later.';
        }
        try {
            $content = $this->twig->render('error/show.html.twig', compact('status', 'heading', 'message'));
        } catch (\Throwable) {
            // A template failure must not reveal the original exception.
            $content = '<!doctype html><html lang="en"><meta charset="utf-8"><title>'.$heading.'</title><h1>'.$heading.'</h1><p>'.$message.'</p><a href="/">Back to asking</a></html>';
        }
        $event->setResponse(new Response($content, $status, ['Content-Type' => 'text/html; charset=UTF-8', 'Cache-Control' => 'no-store', 'X-Robots-Tag' => 'noindex, nofollow']));
    }
}
