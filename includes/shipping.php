<?php

function getShippingQuote(array $cartItems, array $destination): array
{
    //Get all price multipliers
    $zone = getShippingZone($destination); // Multiplier based on destination
    $baseMultiplier = match ($zone) {
        1 => 1.0,
        2 => 1.5,
        3 => 2.3,
        default => 1.5
    };

    $totalWeight = 0; // Multiplier based on weight and dimensions (weightFactor)
    $totalDimWeight = 0;
    $hasDimensions = true;
    foreach ($cartItems as $item) {
        $qty = (int)($item['quantity'] ?? 1);
        $weight = (float)($item['weight'] ?? 0);
        $length = (float)($item['length'] ?? 0);
        $width  = (float)($item['width'] ?? 0);
        $height = (float)($item['height'] ?? 0);

        $totalWeight += $weight * $qty;
        if ($length && $width && $height) {
            $dimWeight = (($length * $width * $height) / 139) * $qty;
            $totalDimWeight += $dimWeight;
        } else {
            $hasDimensions = false;
        }
    }
    if ($totalWeight <= 0) {
        $totalWeight = 1;
    }
    $chargeableWeight = max($totalWeight, $totalDimWeight);
    if ($chargeableWeight <= 10) {
        $weightFactor = 1 + ($chargeableWeight * 0.08);
    } elseif ($chargeableWeight <= 50) {
        $weightFactor = 2.2 + (($chargeableWeight - 10) * 0.04);
    } else {
        $weightFactor = 4.2 + (($chargeableWeight - 50) * 0.01);
    }

    $itemCount = 0; // Multiplier based on number of items, more items, more complex or bigger box
    foreach ($cartItems as $item) {
        $itemCount += (int)($item['quantity'] ?? 1);
    }
    $complexityMultiplier = 1 + ($itemCount * 0.03);

    $sizeMultiplier = 1.0; //Multiplier based on dimensions
    foreach ($cartItems as $item) {
        $length = (float)($item['length'] ?? 0);
        $width  = (float)($item['width'] ?? 0);
        $height = (float)($item['height'] ?? 0);
        if ($length && $width && $height) {
            if ($length > 20 || $width > 20 || $height > 20) {
                $sizeMultiplier = 1.35;
                break;
            }
        }
    }

    // Base rates for each service
    $minimumShipping = 8.99;
    $groundBase   = 10.99;
    $threeDayBase = 22.99;
    $twoDayBase   = 36.99;
    $groundPrice = ($groundBase + ($chargeableWeight * 0.80) + (($itemCount - 1) * 0.50)) * $baseMultiplier * $sizeMultiplier;
    $threeDayPrice = ($threeDayBase + ($chargeableWeight * 1.10) + (($itemCount - 1) * 0.75)) * $baseMultiplier * $sizeMultiplier;
    $twoDayPrice = ($twoDayBase + ($chargeableWeight * 1.50) + (($itemCount - 1) * 1.00)) * $baseMultiplier * $sizeMultiplier;

    //Set rates
    $rates = [
        'ground' => [
            'service_code' => 'UPS_GROUND',
            'service_name' => 'UPS Ground',
            'price' => roundShippingPrice(max($groundPrice, $minimumShipping)),
            'eta' => getETA($zone, 'ground'),
        ],
        '3_day' => [
            'service_code' => 'UPS_3DAY',
            'service_name' => 'UPS 3 Day Select',
            'price' => roundShippingPrice(max($threeDayPrice, $minimumShipping)),
            'eta' => getETA($zone, '3_day'),
        ],
        '2nd_day' => [
            'service_code' => 'UPS_2ND_DAY',
            'service_name' => 'UPS 2nd Day Air',
            'price' => roundShippingPrice(max($twoDayPrice, $minimumShipping)),
            'eta' => getETA($zone, '2day'),
        ]
    ];

    return [
        'meta' => [
            'total_weight' => $totalWeight,
            'dimensions_used' => $hasDimensions
        ],
        'rates' => $rates
    ];
}


//lolxd functions
function getShippingZone(array $destination): int
{
    $state = strtolower($destination['state'] ?? '');

    $zone1 = ['ca', 'or', 'wa', 'nv', 'az', 'ut']; // Zone 1: West Coast (cheap)
    $zone2 = ['tx', 'co', 'nm', 'id', 'mt', 'wy', 'ok', 'ks', 'ne', 'sd', 'nd', 'mn', 'ia', 'mo', 'ar', 'la'];  // Zone 2: Central / most US (medium)
    if (in_array($state, $zone1)) return 1;
    if (in_array($state, $zone2)) return 2;
    return 3; // East Coast / remote / expensive
}

function roundShippingPrice(float $price): float
{
    $price = ceil($price * 100) / 100;
    $whole = floor($price);
    return $whole + 0.99;
}

function getETA(int $zone, string $service): string
{
    $base = [
        'ground' => [4, 6],
        '3_day'  => [3, 5],
        '2day'   => [2, 3],
    ];
    [$min, $max] = $base[$service];
    $zoneDelay = match ($zone) {
        1 => 0,
        2 => 1,
        3 => 2,
        default => 1
    };
    $min += $zoneDelay;
    $max += $zoneDelay;
    if ($min === $max) {
        return "{$min} business days";
    }
    return "{$min}-{$max} business days";
}
