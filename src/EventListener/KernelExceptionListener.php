<?php

namespace App\EventListener;

use App\Exception\ValidationException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class KernelExceptionListener
{
    #[AsEventListener(event: KernelEvents::EXCEPTION)]
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ValidationException) {
            $response = new JsonResponse([
                'errors' => $exception->getErrors()
            ], $exception->getCode());
        } else {
            if ($exception instanceof HttpExceptionInterface) {
                $statusCode = $exception->getStatusCode();
            } else {
                $statusCode = 400;
            }

            $response = new JsonResponse([
                'error' => $exception->getMessage(),
            ], $statusCode);
        }

        $event->setResponse($response);
    }
}
