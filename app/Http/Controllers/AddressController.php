<?php

namespace App\Http\Controllers;

use App\Address;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Address::paginate(10);
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
            'customer_id' => 'required',
            'address' => 'nullable',
            'address2' => 'nullable',
            'city' => 'nullable',
            'state' => 'nullable',
            'postcode' => 'nullable',
            'country' => 'nullable',
        ]);

        $address = Address::create($request);
        return response()->json($address, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Category $Category)
    {
        return response()->json($Category, Response::HTTP_OK);
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
        $Category = Address::findOrFail($id);
        $Category->update($request->all());

        return response()->json($Category, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Address::find($id)->delete();

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
        Address::whereIn($id)->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
