<?php

namespace App\Entity\Enum;

enum DiscountType: string
{
    case FIXED = 'fixed';
    case PERCENT = 'percent';
}