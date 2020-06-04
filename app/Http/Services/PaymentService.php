<?php

namespace App\Http\Services;

use App\Http\Resources\Resources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class PaymentService
{

    /**
     * Constants
     */

    // Payment Status: Pending
    const PAYMENT_STATUS_UNPAID = 1;
    const PAYMENT_STATUS_UNPAID_TEXT = 'Belum dibayar';

    // Payment Status: Paid
    const ORDER_STATUS_COMPLETED = 2;
    const ORDER_STATUS_COMPLETED_TEXT = 'Dibayar';

    // Payment Method: Bank Transfer
    const ORDER_METHOD_BANK_TRANSFER = 1;
    const ORDER_METHOD_BANK_TRANSFER_TEXT = 'Bank Transfer (Upload Resit)';

    // Payment Method: Online Banking
    const ORDER_METHOD_ONLINE_BANKING = 2;
    const ORDER_METHOD_ONLINE_BANKING_TEXT = 'Online Banking';

    /**
     * Payment Statuses
     * Key-value mapping
     * 
     */
    public static function payment_status($value)
    {
        switch ($value) {
            case self::PAYMENT_STATUS_UNPAID:
                return self::PAYMENT_STATUS_UNPAID_TEXT;
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
     * Payment Methods
     * Key-value mapping
     * 
     */
    public static function payment_method($value)
    {
        switch ($value) {
            case self::ORDER_METHOD_BANK_TRANSFER:
                return self::ORDER_METHOD_BANK_TRANSFER_TEXT;
                break;

            case self::ORDER_METHOD_ONLINE_BANKING:
                return self::ORDER_METHOD_ONLINE_BANKING_TEXT;
                break;

            default:
                return '';
                break;
        }
    }
}
