<?php

namespace App\Http\Services;

use App\Http\Resources\Resources;
use Illuminate\Http\Request;
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
}
