<?php

namespace App\Http\Controllers;

use App\Cart;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CartController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //$user_id = $request->user()->id;
        $request = $request->validate([
            'ip_address' => 'nullable',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|min:1|max:9999',
            'variation' => 'nullable',
            'session_id' => 'nullable',
            'customer_id' => 'nullable'
        ]);

        // Check session
        if (empty($request['session_id']) && empty($request['customer_id'])) {
            return response()->json(null, Response::HTTP_PRECONDITION_FAILED);
        }

        // Check
        $cart = Cart::create($request);

        if ($cart) {
            return response()->json(null, Response::HTTP_CREATED);
        }
    }

    public function destroy(Request $request)
    {
        $request = $request->validate([
            'product_id' => 'required',
            'session_id' => 'required',
        ]);

        $cart = Cart::where([
            'product_id' => $request['product_id'],
            'session_id' => $request['session_id']
        ])->delete();

        if ($cart) {
            return response()->json(null, Response::HTTP_OK);
        }
    }
}
