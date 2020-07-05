<?php

namespace App\Http\Services;

use App\Http\Resources\Resources;
use App\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class ShipmentService
{

    /**
     * Constants
     */

    // Shipping Status: Pending
    const SHIPMENT_STATUS_PENDING = 1;
    const SHIPMENT_STATUS_PENDING_TEXT = 'Belum dihantar';

    // Shipping Status: Sent
    const SHIPMENT_STATUS_COMPLETED = 2;
    const SHIPMENT_STATUS_COMPLETED_TEXT = 'Dihantar';

    /**
     * Shipment Statuses
     * Key-value mapping
     * 
     */
    public static function shipment_status($value)
    {
        switch ($value) {
            case self::SHIPMENT_STATUS_PENDING:
                return self::SHIPMENT_STATUS_PENDING_TEXT;
                break;

            case self::SHIPMENT_STATUS_COMPLETED:
                return self::SHIPMENT_STATUS_COMPLETED_TEXT;
                break;

            default:
                return null;
                break;
        }
    }

    public static function shipment_method($value)
    {
        switch ($value) {
            case CourierService::COURIER_EASYPARCEL:
                return CourierService::COURIER_EASYPARCEL_TEXT;
                break;

            case CourierService::COURIER_DELYVA:
                return CourierService::COURIER_DELYVA_TEXT;
                break;

            default:
                return null;
                break;
        }
    }

    const SHIPPING_REQUIRE_PROCESS = 'require-process';

    /**
     * Total Shipment
     */
    public static function total_shipment($shipment_status): int
    {
        
        switch ($shipment_status) {
                // Shipment where payment is paid, but shipping is pending
            case self::SHIPPING_REQUIRE_PROCESS:
                return DB::table('orders')
                    ->join('payments', 'payments.order_id', 'orders.id')
                    ->join('shipments', 'shipments.order_id', 'orders.id')
                    ->where('payments.payment_status', '=', PaymentService::PAYMENT_STATUS_PAID)
                    ->where('shipments.shipping_status', '=', ShipmentService::SHIPMENT_STATUS_PENDING)
                    ->where('orders.user_id', '=', request()->user()->id)
                    ->count();
                break;
        }

        return Shipment::where('shipping_status', $shipment_status)->count();
    }
}
