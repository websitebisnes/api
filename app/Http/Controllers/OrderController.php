<?php

namespace App\Http\Controllers;

use App\Address;
use App\Customer;
use App\Order;
use App\OrderProduct;
use App\OrderReceipt;
use App\Payment;
use App\Product;
use App\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\OrderStoreRequest;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Order::when($request->get('with_customer'), function ($q) {
            return $q->with('customer');
        })->when($request->get('with_products'), function ($q) {
            return $q->with('order_products.product');
        })->when($request->get('with_payment'), function ($q) {
            return $q->with('payment');
        })->when($request->get('with_order_receipts'), function ($q) {
            return $q->with('order_receipts');
        })->when($request->get('with_shipment'), function ($q) {
            return $q->with('shipment');
        })->latest()->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderStoreRequest $request)
    {
        $request = $request->all();

        $validator = Validator::make($request, []);
        $validator->sometimes('image_ids', 'required', function ($request) {
            return ($request->payment['payment_method'] == 1 && $request->payment['payment_status'] == 2);
        })->validate();

        // Create customer
        $customer = Customer::create($request['customer']);

        $address = new Address($request);
        $customer->address()->save($address);

        $order = Order::create(collect($request)->put('customer_id', $customer->id)->toArray());

        $amount = 0.00;

        collect($request['products'])->map(function ($item) use ($order, &$amount) {
            $product = Product::findOrFail($item['product_id']);
            OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price,
                'price_discount' => $product->price_discount
            ]);

            $amount += ($product->price_discount ?? $product->price) * $item['quantity'];
        });

        $payment_data = collect($request['payment'])->put('amount', $amount)->toArray();
        $payment = new Payment($payment_data);
        $order->payment()->save($payment);

        if (isset($request['image_ids'])) {
            collect($request['image_ids'])->map(function ($image_id) use ($order) {
                OrderReceipt::create([
                    'order_id' => $order->id,
                    'image_id' => $image_id
                ]);
            });
        }

        if (isset($request['shipment'])) {
            $shipment = new Shipment($request);
            $order->shipment()->save($shipment);
        }

        return response()->json($order, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        $order->load([
            'customer',
            'order_products.product',
            'order_receipts',
            'payment',
            'shipment'
        ]);
        return response()->json($order, Response::HTTP_OK);
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
        $order = Order::findOrFail($id);

        $order->fill($request->all());
        if ($order->isDirty()) {
            $order->update($order->getDirty());
        }

        return response()->json($order, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Order::find($id)->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  array  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy_bulk(array $id)
    {
        Order::whereIn($id)->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
