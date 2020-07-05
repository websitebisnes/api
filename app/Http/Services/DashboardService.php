<?php

namespace App\Http\Services;

use App\Product;

class DashboardService
{

    public static function get_all_status()
    {

        $pending_payment = OrderService::total_order(OrderService::ORDER_PENDING_PAYMENT);
        $complete_process = OrderService::total_order(OrderService::ORDER_STATUS_COMPLETED);

        return [
            'order' => [
                'pending_payment' => $pending_payment,
                'complete_process' => $complete_process,
                'total' => $pending_payment + $complete_process
            ],
            'payment' => [
                'paid' => 'RM '. number_format(PaymentService::total_payment(PaymentService::PAYMENT_STATUS_PAID), 2),
                'unpaid' => 'RM '. number_format(PaymentService::total_payment(PaymentService::PAYMENT_STATUS_UNPAID), 2)
            ],
            'shipment' => [
                'pending' => ShipmentService::total_shipment(ShipmentService::SHIPPING_REQUIRE_PROCESS),
                'dispatched' => ShipmentService::total_shipment(ShipmentService::SHIPMENT_STATUS_COMPLETED)
            ],
            'customer' => [
                'total' => CustomerService::total_customer(),
            ]
        ];
    }
}
