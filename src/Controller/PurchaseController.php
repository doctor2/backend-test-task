<?php

namespace App\Controller;

use App\Message\CalculatePriceMessage;
use App\Message\PurchaseMessage;
use App\Validator\MessageValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

final class PurchaseController extends AbstractController
{
    private MessageValidator $messageValidator;
    private MessageBusInterface $bus;

    public function __construct(MessageValidator $messageValidator, MessageBusInterface $bus)
    {
        $this->messageValidator = $messageValidator;
        $this->bus = $bus;
    }

    #[Route('/calculate-price', methods: ['POST'])]
    public function calculatePrice(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $message = new CalculatePriceMessage($data);
            $errors = $this->messageValidator->validate($message);

            if (count($errors) > 0) {
                return new JsonResponse(['errors' => $errors], 422);
            }

            $totalPrice = $this->handleMessage($message);

            return new JsonResponse(['price' => $totalPrice]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/purchase', methods: ['POST'])]
    public function purchase(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $message = new CalculatePriceMessage($data);
            $errors = $this->messageValidator->validate($message);

            if (count($errors) > 0) {
                return new JsonResponse(['errors' => $errors], 422);
            }

            $totalPrice = $this->handleMessage($message);

            $message = new PurchaseMessage($data, $totalPrice);
            $errors = $this->messageValidator->validate($message);

            if (count($errors) > 0) {
                return new JsonResponse(['errors' => $errors], 422);
            }

            $this->handleMessage($message);

            return new JsonResponse(['price' => $totalPrice], 200);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    private function handleMessage($message)
    {
        $envelope = $this->bus->dispatch($message);
        $handledStamp = $envelope->last(HandledStamp::class);
        return $handledStamp->getResult();
    }
}
