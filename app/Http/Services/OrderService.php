<?php

namespace App\Http\Services;

use App\Product;

class OrderService
{

    /**
     * Constants
     */

    // Order Status: Pending
    const ORDER_STATUS_PENDING = 1;
    const ORDER_STATUS_PENDING_TEXT = 'Belum diproses';

    // Order Status: Pending
    const ORDER_STATUS_COMPLETED = 2;
    const ORDER_STATUS_COMPLETED_TEXT = 'Lengkap';

    /**
     * Order Statuses
     * Key-value mapping
     * 
     */
    public static function order_status($value)
    {
        switch ($value) {
            case self::ORDER_STATUS_PENDING:
                return self::ORDER_STATUS_PENDING_TEXT;
                break;

            case self::ORDER_STATUS_COMPLETED:
                return self::ORDER_STATUS_COMPLETED_TEXT;
                break;

            default:
                return '';
                break;
        }
    }

    /**
     * Standardize weight for shipping, in KG
     */
    public static function standardize_weight(Product $product)
    {
        if (empty($product->attributes) or empty($product->attributes['weight'])) {
            return 0.00;
        }

        if ($product->attributes['weight']['unit'] == 'g') {
            return $product->attributes['weight']['value'] / 1000;
        }

        return $product->attributes['weight']['value'];
    }
}
