<?php

namespace App\Entity\Enum;

enum PaymentProcessor: string
{
    case PAYPAL = 'paypal';
    case STRIPE = 'stripe';
}