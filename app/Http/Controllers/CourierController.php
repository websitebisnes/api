<?php

namespace App\Http\Controllers;

use App\Courier;
use App\Http\Services\CourierService;
use App\Order;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CourierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Courier::all()
            ->keyBy('courier_id');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request = $request->validate([
            'courier_id' => 'required',
            'api_key' => 'required',
            'api_secret' => 'nullable'
        ]);

        $courier = CourierService::integrate($request);

        if ($courier['status']) {
            return response()->json($courier, Response::HTTP_CREATED);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Courier $courier)
    {


        return response()->json($courier, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        return response()->json($courier, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Get supported couriers services
     */
    public function supported_couriers()
    {
        $couriers = CourierService::get_supported_couriers();

        return response()->json($couriers, Response::HTTP_OK);
    }

    /**
     * Get courier status
     */
    public function courier_get_status(Request $request, Courier $courier)
    {
        $courier = CourierService::get_courier_status($request, $courier);

        return response()->json($courier, Response::HTTP_OK);
    }


    /**
     * Get courier shipment rates
     */
    public function courier_get_rates(Courier $courier, Order $order)
    {
        $courier = CourierService::get_courier_rates($courier, $order);

        return response()->json($courier, Response::HTTP_OK);
    }
}
