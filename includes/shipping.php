<?php

/**
 * SHIPPING ENGINE (STUB VERSION)
 * - replaces frontend shipping logic
 * - will later be replaced with UPS API
 * - NEVER trust browser shipping values
 */

function getShippingQuote(array $cartItems, array $destination): array
{
    /*
    -------------------------------------------------
    STEP 1: Aggregate cart data
    -------------------------------------------------
    */

    $totalWeight = 0;
    $hasDimensions = true;

    foreach ($cartItems as $item) {

        $qty = (int)($item['quantity'] ?? 1);

        $weight = (float)($item['weight'] ?? 0);
        $length = (float)($item['length'] ?? 0);
        $width  = (float)($item['width'] ?? 0);
        $height = (float)($item['height'] ?? 0);

        $totalWeight += $weight * $qty;

        // if any dimension missing, we flag it (future UPS logic)
        if (!$length || !$width || !$height) {
            $hasDimensions = false;
        }
    }

    if ($totalWeight <= 0) {
        $totalWeight = 1;
    }

    /*
    -------------------------------------------------
    STEP 2: DUMMY RATE ENGINE (UPS PLACEHOLDER)
    -------------------------------------------------
    */

    $weightFactor = ceil($totalWeight / 5);

    $rates = [
        'ground' => [
            'service_code' => 'UPS_GROUND',
            'service_name' => 'UPS Ground',
            'price' => 15.99 * $weightFactor,
            'eta' => '3-5 business days'
        ],
        '3_day' => [
            'service_code' => 'UPS_3DAY',
            'service_name' => 'UPS 3 Day Select',
            'price' => 25.99 * $weightFactor,
            'eta' => '3 business days'
        ],
        '2nd_day' => [
            'service_code' => 'UPS_2ND_DAY',
            'service_name' => 'UPS 2nd Day Air',
            'price' => 50.99 * $weightFactor,
            'eta' => '2 business days'
        ]
    ];

    /*
    -------------------------------------------------
    STEP 3: ADD DEBUG CONTEXT (optional but useful)
    -------------------------------------------------
    */

    return [
        'meta' => [
            'total_weight' => $totalWeight,
            'dimensions_used' => $hasDimensions
        ],
        'rates' => $rates
    ];
}
