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
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

final class PurchaseController extends AbstractController
{
    public function __construct(private MessageValidator $messageValidator, private MessageBusInterface $bus,
                                private SerializerInterface $serializer)
    {}

    #[Route('/calculate-price', methods: ['POST'])]
    public function calculatePrice(Request $request): JsonResponse
    {
        $totalPrice = $this->calculateProductPrice($request);

        return new JsonResponse(['price' => $totalPrice]);
    }

    #[Route('/purchase', methods: ['POST'])]
    public function purchase(Request $request): JsonResponse
    {
        $totalPrice = $this->calculateProductPrice($request);

        $message = $this->serializer->deserialize($request->getContent(), PurchaseMessage::class,'json', [
            BackedEnumNormalizer::ALLOW_INVALID_VALUES => true,
        ]);
        $message->productPrice = $totalPrice;

        $this->messageValidator->validate($message);

        $this->handleMessage($message);

        return new JsonResponse(['price' => $totalPrice], 200);
    }

    private function calculateProductPrice(Request $request)
    {
        $message = $this->serializer->deserialize($request->getContent(), CalculatePriceMessage::class,'json');

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
