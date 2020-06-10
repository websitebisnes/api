<?php

namespace App\Http\Services;

use App\Courier;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class CourierService
{

    /**
     * Constants
     */

    // Courier Available: EasyParcel
    const COURIER_EASYPARCEL = 1;
    const COURIER_EASYPARCEL_TEXT = 'EasyParcel';
    const COURIER_EASYPARCEL_WEBSITE = 'https://easyparcel.my';
    const COURIER_EASYPARCEL_ENDPOINT_URL_DEVELOPMENT = "http://demo.connect.easyparcel.my";
    const COURIER_EASYPARCEL_ENDPOINT_URL_PRODUCTION = "http://connect.easyparcel.my";
    const COURIER_EASYPARCEL_ENDPOINT_NODE_RATE_CHECKING = "/?ac=EPRateCheckingBulk"; // rate checking from all courier companies in EasyParcel platform
    const COURIER_EASYPARCEL_ENDPOINT_NODE_SUBMIT_ORDER = "/?ac=EPSubmitOrderBulk"; // make single order at EasyParcel
    const COURIER_EASYPARCEL_ENDPOINT_NODE_PAY_ORDER = "/?ac=EPPayOrderBulk"; // payment for the orders made, order payment refers to payment for a single parcel only
    const COURIER_EASYPARCEL_ENDPOINT_NODE_ORDER_STATUS = "/?ac=EPOrderStatusBulk"; // Get order status by using order number
    const COURIER_EASYPARCEL_ENDPOINT_NODE_PARCEL_STATUS = "/?ac=EPParcelStatusBulk"; // Get parcel status by using order number
    const COURIER_EASYPARCEL_ENDPOINT_NODE_TRACKING_STATUS = "/?ac=EPTrackingBulk"; // Track the shipment status using airway bill number
    const COURIER_EASYPARCEL_ENDPOINT_NODE_BALANCE = "/?ac=EPCheckCreditBalance"; // check current user balance
    const COURIER_EASYPARCEL_ENDPOINT_NODE_SUBMIT_ORDER_DIRECT_MY = "/?ac=EPSubmitOrderBulkV3"; // designed to direct create order + payment at EasyParcel. Currently it is limited within Domestic (Malaysia) shipping delivery

    // Courier Available: Delyva
    const COURIER_DELYVA = 2;
    const COURIER_DELYVA_TEXT = 'Delyva';
    const COURIER_DELYVA_WEBSITE = 'https://delyva.com';

    /**
     * List of supported couriers
     * Key-value mapping
     * 
     */

    public static function get_supported_couriers()
    {
        return [
            [
                'id' => self::COURIER_EASYPARCEL,
                'name' => self::COURIER_EASYPARCEL_TEXT,
                'website' => self::COURIER_EASYPARCEL_WEBSITE,
            ],
            /*[
                'id' => self::COURIER_DELYVA,
                'courier' => self::COURIER_DELYVA_TEXT,
                'courier' => self::COURIER_DELYVA_WEBSITE
            ]*/
        ];
    }

    // EASYPARCEL API ENDPOINT
    public static function get_easyparcel_endpoint()
    {
        if (App::environment('localx')) {
            return self::COURIER_EASYPARCEL_ENDPOINT_URL_DEVELOPMENT;
        } else {
            return self::COURIER_EASYPARCEL_ENDPOINT_URL_PRODUCTION;
        }
    }

    // EasyParcel States Converter
    public static function convert_to_easyparcel_state($state)
    {
        return array_flip(self::get_easyparcel_state_malaysia())[$state];
    }

    // EASYPARCEL ERROR CODES LIST
    public static function get_easyparcel_error_codes()
    {
        return [
            0 => 'Success',
            1 => 'Required authentication key',
            2 => 'Invalid authentication key',
            3 => 'Required api key',
            4 => 'Invalid api key',
            5 => 'Unauthorized user',
            6 => 'Invalid data insert format in array',
        ];
    }

    // EASYPARCEL MALAYSIA STATE CODE
    public static function get_easyparcel_state_malaysia()
    {
        return [
            'jhr' => 'Johor',
            'kdh' => 'Kedah',
            'ktn' => 'Kelantan',
            'mlk' => 'Melaka',
            'nsn' => 'Negeri Sembilan',
            'phg' => 'Pahang',
            'prk' => 'Perak',
            'pls' => 'Perlis',
            'png' => 'Pulau Pinang',
            'sgr' => 'Selangor',
            'trg' => 'Terengganu',
            'kul' => 'Kuala Lumpur',
            'pjy' => 'Putrajaya',
            'srw' => 'Sarawak',
            'sbh' => 'Sabah',
            'lbn' => 'Labuan'
        ];
    }

    public static function integrate(array $courier_data)
    {
        switch ($courier_data['courier_id']) {

                // EASYPARTCEL
            case self::COURIER_EASYPARCEL:
                $response = self::get_balance($courier_data);

                if ($response->ok()) {
                    $response_data = $response->json();
                    if ($response_data['api_status'] == 'Success' && $response_data['error_code'] == 0) {

                        $courier = Courier::updateOrCreate(
                            ['courier_id' => $courier_data['courier_id']],
                            [
                                'courier_id' => $courier_data['courier_id'],
                                'name' => self::COURIER_EASYPARCEL_TEXT,
                                'is_enabled' => 1,
                                'config' => $courier_data,
                                'data' => $response_data['wallet'][0] ?? []
                            ]
                        );
                        return [
                            'status' => true,
                            'courier' => $courier
                        ];
                    } else if ($response_data['api_status'] == 'Error') {
                        return [
                            'error' => $response_data['error_remark']
                        ];
                    }
                }
                return response()->json([
                    'status' => false
                ]);
                break;
        }
    }

    public static function get_balance(array $courier_data)
    {
        $response = Http::asForm()->post(self::get_easyparcel_endpoint() . self::COURIER_EASYPARCEL_ENDPOINT_NODE_BALANCE, [
            'api' => $courier_data['api_key']
        ]);

        return $response;
    }

    /**
     * Get courier status
     */
    public static function get_courier_status(array $courier_data)
    {
        $response = self::get_balance($courier_data);
        if ($response->ok()) {
            $response_data = $response->json();
            if ($response_data['api_status'] == 'Success' && $response_data['error_code'] == 0) {

                $courier = Courier::updateOrCreate(
                    ['courier_id' => $courier_data['courier_id']],
                    [
                        'courier_id' => $courier_data['courier_id'],
                        'name' => self::COURIER_EASYPARCEL_TEXT,
                        'is_enabled' => 1,
                        'config' => $courier_data,
                        'data' => $response_data['wallet'][0] ?? []
                    ]
                );
                return [
                    'status' => true,
                    'courier' => $courier
                ];
            } else if ($response_data['api_status'] == 'Error') {
                return [
                    'error' => $response_data['error_remark']
                ];
            }
        }
    }

    /**
     * Get courier rates
     */
    public static function get_courier_rates(Courier $courier, Order $order)
    {

        $order->load(['customer']);

        switch ($courier->id) {
            case self::COURIER_EASYPARCEL:
                $post_data = [
                    'api' => $courier->config['api_key'],
                    'bulk' => [
                        [
                            'pick_code' => '47650', //string(10) Yes Sender's postcode.
                            'pick_state' => 'sgr', //string(35) Yes Sender's state. (Refer to Appendix III)
                            'pick_country' => 'MY', //string(2) Yes Sender's country (“MY”).
                            'send_code' => $order->customer->address->postcode, //string(10) Yes Receiver's postcode.
                            'send_state' => self::convert_to_easyparcel_state($order->customer->address->state), //string(35) Yes Receiver's state. (Refer to Appendix III)
                            'send_country' => 'MY', //string(2) Yes Receiver's country (“MY”).
                            'weight' => $order->shipment->weight, //double(8,2) Yes The weight of the parcel.
                            'width' => '', //double(8,2) Optional The width of the parcel.
                            'length' => '', //double(8,2) Optional The length of the parcel.
                            'height' => '', //double(8,2) Optional The height of the parcel.
                            'date_coll' => '', //date Optional Check the available pickup date. If the date is left empty, the default will be today’s date. Format : “YYYY-MM-DD”
                            //'date_coll' => Carbon::now()->tz('Asia/Kuala_Lumpur')->addDay()->toDateString(), //date Optional Check the available pickup date. If the date is left empty, the default will be today’s date. Format : “YYYY-MM-DD”
                        ]
                    ]
                ];

                $response_data = Http::asForm()->post(
                    self::get_easyparcel_endpoint() . self::COURIER_EASYPARCEL_ENDPOINT_NODE_RATE_CHECKING,
                    $post_data
                );
                if ($response_data['api_status'] == 'Success' && $response_data['error_code'] == 0) {
                    $rates = $response_data['result'][0]['rates'];

                    $data = [
                        'rates' => collect($rates)->groupBy('service_detail')->toArray()
                    ];

                    return $data;
                } else if ($response_data['api_status'] == 'Error') {
                    return $response_data['error_remark'];
                }
                break;
        }
    }
}
