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
        $data = json_decode($request->getContent(), true);

        $totalPrice = $this->calculateProductPrice($data);

        return new JsonResponse(['price' => $totalPrice]);
    }

    #[Route('/purchase', methods: ['POST'])]
    public function purchase(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $totalPrice = $this->calculateProductPrice($data);

        $message = new PurchaseMessage($data, $totalPrice);

        $this->messageValidator->validate($message);

        $this->handleMessage($message);

        return new JsonResponse(['price' => $totalPrice], 200);
    }

    private function calculateProductPrice(?array $data)
    {
        $message = new CalculatePriceMessage($data);

        $this->messageValidator->validate($message);

        return $this->handleMessage($message);
    }

    private function handleMessage($message)
    {
        $envelope = $this->bus->dispatch($message);
        $handledStamp = $envelope->last(HandledStamp::class);
        return $handledStamp->getResult();
    }
}
