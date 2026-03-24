<?php

namespace App\EventListener;

use App\Exception\InvalidDataException;
use App\Exception\PaymentProcessorException;
use App\Exception\ValidationException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final class KernelExceptionListener
{
    private const EXCEPTION_MAP = [
        InvalidDataException::class => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
        PaymentProcessorException::class => JsonResponse::HTTP_BAD_REQUEST
//      OrderNotFound::class => JsonResponse::HTTP_NOT_FOUND,
//      AccessDenied::class => JsonResponse::HTTP_FORBIDDEN
    ];

    #[AsEventListener(event: KernelEvents::EXCEPTION)]
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HandlerFailedException) {
            $exception = $exception->getPrevious();
        }

        $class = $exception::class;

        if (!array_key_exists($class, self::EXCEPTION_MAP)){
            return;
        }

        if ($exception instanceof PaymentProcessorException) {
            $event->setResponse(new JsonResponse([
                'error' => $exception->getMessage(),
            ], self::EXCEPTION_MAP[$class]));
        } else {
            $event->setResponse(new JsonResponse([
                'errors' => $exception->getErrors()
            ], self::EXCEPTION_MAP[$class]));
        }
    }
}
