<?php
function isProductOnSale(array $p, DateTimeZone $tz): bool
{
    if (empty($p['sale_price'])) {
        return false;
    }

    $now = new DateTime('now', $tz);

    $saleStart = !empty($p['sale_start'])
        ? new DateTime($p['sale_start'], $tz)
        : null;

    $saleEnd = !empty($p['sale_end'])
        ? new DateTime($p['sale_end'], $tz)
        : null;

    return
        ($saleStart === null && $saleEnd === null) ||
        ($saleStart !== null && $saleEnd === null && $now >= $saleStart) ||
        ($saleStart === null && $saleEnd !== null && $now <= $saleEnd) ||
        ($saleStart !== null && $saleEnd !== null && $now >= $saleStart && $now <= $saleEnd);
}

function getDiscountPercent(float $price, float $salePrice): int
{
    if ($price <= 0 || $salePrice >= $price) {
        return 0;
    }

    return (int) round((($price - $salePrice) / $price) * 100);
}

function getProductPricing(array $product, DateTimeZone $tz): array
{
    $price     = (float) $product['price'];
    $salePrice = (float) ($product['sale_price'] ?? 0);

    $isOnSale = isProductOnSale($product, $tz);

    if ($isOnSale && $salePrice < $price) {
        return [
            'is_on_sale'       => true,
            'final_price'      => $salePrice,
            'original_price'   => $price,
            'discount_percent' => getDiscountPercent($price, $salePrice),
        ];
    }

    return [
        'is_on_sale'       => false,
        'final_price'      => $price,
        'original_price'   => $price,
        'discount_percent' => 0,
    ];
}