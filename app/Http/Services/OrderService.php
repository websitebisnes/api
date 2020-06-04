<?php

namespace App\Http\Services;

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
}
