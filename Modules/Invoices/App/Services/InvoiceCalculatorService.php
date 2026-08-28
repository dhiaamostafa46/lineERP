<?php

namespace Modules\Invoices\App\Services;

class InvoiceCalculatorService
{
    /**
     * Calculate invoice totals based on items, discounts, and tax settings.
     * Mimics the logic in invoice_scripts.blade.php to ensure consistency.
     * 
     * @param array $items Array of items with unit_price, quantity, type_discount, number_discount, vat_rate
     * @param float $globalDiscountValue
     * @param int $globalDiscountType (1 = %, 2 = Fixed)
     * @param bool $pricesIncludeVat
     * @param float $shippingCost
     * @param float $shippingVatRate
     * @return array Calculated totals and updated items
     */
    public function calculate(
        array $items,
        float $globalDiscountValue = 0,
        int $globalDiscountType = 1,
        bool $pricesIncludeVat = false,
        float $shippingCost = 0,
        float $shippingVatRate = 0
    ): array {
        $totalNetEntered = 0;
        $linesData = [];

        // 1. Calculate row gross and line discounts
        foreach ($items as $index => $item) {
            $qty = (float)($item['quantity'] ?? $item['qty'] ?? 0);
            $price = (float)($item['unit_price'] ?? $item['price'] ?? 0);
            $discountVal = (float)($item['number_discount'] ?? 0);
            $discountType = (int)($item['type_discount'] ?? 1); // 1 = %, 2 = Fixed
            $vatRate = (float)($item['vat_rate'] ?? $item['vat'] ?? 0);

            $rowGrossEntered = round($qty * $price, 2);
            $lineDiscEntered = ($discountType === 1) 
                ? round($rowGrossEntered * ($discountVal / 100), 2) 
                : $discountVal;
            
            if ($lineDiscEntered > $rowGrossEntered) {
                $lineDiscEntered = $rowGrossEntered;
            }

            $netLineEntered = round($rowGrossEntered - $lineDiscEntered, 2);
            $totalNetEntered += $netLineEntered;

            $linesData[$index] = [
                'original_item' => $item,
                'vatRate' => $vatRate,
                'rowGrossEntered' => $rowGrossEntered,
                'lineDiscEntered' => $lineDiscEntered,
                'netLineEntered' => $netLineEntered
            ];
        }

        // 2. Global Discount
        $globalDiscEntered = ($globalDiscountType === 1) 
            ? round($totalNetEntered * ($globalDiscountValue / 100), 2) 
            : $globalDiscountValue;
            
        if ($globalDiscEntered > $totalNetEntered) {
            $globalDiscEntered = $totalNetEntered;
        }

        $netInvoiceEntered = $totalNetEntered - $globalDiscEntered;
        $globalDiscountFactor = $totalNetEntered > 0 ? ($netInvoiceEntered / $totalNetEntered) : 1;

        // 3. Final calculations per item
        $sumBaseExclusive = 0;
        $sumDiscExclusive = 0;
        $sumVatUnrounded = 0;
        $sumVat = 0;
        $finalInvoiceTotal = 0;

        $processedItems = [];

        foreach ($linesData as $index => $data) {
            $finalNetEntered = $data['netLineEntered'] * $globalDiscountFactor;
            $totalLineDiscEntered = $data['rowGrossEntered'] - $finalNetEntered;

            $baseExclusive = 0;
            $finalNetExclusive = 0;
            $vatAmount = 0;
            $unroundedVat = 0;
            $totalLineDiscExclusive = 0;

            if ($pricesIncludeVat && $data['vatRate'] > 0) {
                $divFactor = 1 + ($data['vatRate'] / 100);
                $baseExclusive = round($data['rowGrossEntered'] / $divFactor, 2);
                $finalNetExclusive = round($finalNetEntered / $divFactor, 2);
                $unroundedVat = $finalNetEntered - $finalNetExclusive;
                $vatAmount = round($unroundedVat, 2);
                $totalLineDiscExclusive = round($totalLineDiscEntered / $divFactor, 2);
            } else {
                $baseExclusive = $data['rowGrossEntered'];
                $finalNetExclusive = $finalNetEntered;
                $unroundedVat = $finalNetExclusive * ($data['vatRate'] / 100);
                $vatAmount = round($unroundedVat, 2);
                $totalLineDiscExclusive = $totalLineDiscEntered;
            }

            $finalSubtotalWithVat = round($finalNetExclusive + $vatAmount, 2);

            $sumBaseExclusive += $baseExclusive;
            $sumDiscExclusive += $totalLineDiscExclusive;
            $sumVatUnrounded += $unroundedVat;
            $sumVat += $vatAmount;
            $finalInvoiceTotal += $finalSubtotalWithVat;

            // Merge calculated values back into original item
            $processedItem = $data['original_item'];
            $processedItem['vat_amount'] = $vatAmount;
            $processedItem['subtotal_with_vat'] = $finalSubtotalWithVat;
            $processedItem['total_discount'] = $data['lineDiscEntered']; // Just the line discount, same as frontend
            $processedItem['subtotal_without_vat'] = $finalNetExclusive;
            
            $processedItems[$index] = $processedItem;
        }

        // 4. Add Shipping
        $shippingVatAmount = round($shippingCost * ($shippingVatRate / 100), 2);
        
        $totalInvoiceVat = round($sumVatUnrounded + $shippingVatAmount, 2);
        $totalNetExclusive = round($sumBaseExclusive - $sumDiscExclusive, 2);
        $totalInvoiceInclusive = round($totalNetExclusive + $totalInvoiceVat + $shippingCost, 2);

        return [
            'total_exclusive_vat' => $totalNetExclusive,
            'total_discount' => round($sumDiscExclusive, 2),
            'total_vat' => $totalInvoiceVat,
            'total_inclusive_vat' => $totalInvoiceInclusive,
            'shipping_vat_amount' => $shippingVatAmount,
            'items' => $processedItems
        ];
    }
}
