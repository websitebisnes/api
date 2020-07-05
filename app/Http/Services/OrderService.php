<?php

namespace App\Http\Services;

use App\Order;
use App\Payment;
use App\Product;
use App\Shipment;
use Illuminate\Support\Facades\DB;

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

    const ORDER_PENDING_PAYMENT = 'order_pending_payment';
    const ORDER_PENDING_SHIPPING = 'order_pending_shipping';

    /**
     * Total Order
     */
    public static function total_order($order_status): int
    {
        
        switch ($order_status) {
            // Order where payment is unpaid
            case self::ORDER_PENDING_PAYMENT:
                return Payment::where('payment_status', 1)->count();
                break;
        }

        return Order::where('order_status', $order_status)->count();
    }
}
