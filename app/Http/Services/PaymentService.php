<?php

namespace App\Http\Services;

use App\Http\Resources\Resources;
use App\Payment;
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
    const PAYMENT_STATUS_PAID = 2;
    const PAYMENT_STATUS_PAID_TEXT = 'Dibayar';

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

            case self::PAYMENT_STATUS_PAID:
                return self::PAYMENT_STATUS_PAID_TEXT;
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

    /**
     * Payment
     */

    public static function total_payment($payment_status, $month = null): float
    {
        return Payment::where('payment_status', $payment_status)
            ->when($month, function ($q) use ($month) {
                return $q->whereMonth('payment_verified_date', $month);
            })
            ->sum('amount');
    }
}
