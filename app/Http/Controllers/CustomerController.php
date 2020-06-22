<?php

namespace App\Http\Controllers;

use App\Address;
use App\Customer;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Customer::when($request->get('with') == 'address', function ($q) {
            return $q->with('address');
        })->latest()->paginate(10);
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
            'first_name' => 'required',
            'last_name' => 'required',
            'phone_number' => 'required',
            'email' => 'nullable',
            'password' => 'nullable|confirmed|min:8',

            'address' => 'nullable',
            'address2' => 'nullable',
            'city' => 'nullable',
            'state' => 'nullable',
            'postcode' => 'nullable',
            'country' => 'nullable',
        ]);

        $customer = Customer::create($request);

        $address = new Address($request);
        $customer->address()->save($address);

        return response()->json($customer, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        $customer->load(['address']);
        return response()->json($customer, Response::HTTP_OK);
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
        $customer = Customer::findOrFail($id);

        $customer->fill($request->all());
        if ($customer->isDirty()) {
            $customer->update($customer->getDirty());
        }

        $customer->address->fill($request->all());
        if ($customer->address->isDirty()) {
            $customer->address()->update($customer->address->getDirty());
        }

        return response()->json($customer, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Customer::find($id)->delete();

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
        Customer::whereIn($id)->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Check customer email existence
     *
     * @param  array  $id
     * @return \Illuminate\Http\Response
     */
    public function check_email(Request $request)
    {
        $request = $request->validate([
            'email' => 'required'
        ]);

        $exist = Customer::where('email', $request['email'])->exists();

        if ($exist) {
            $status = ['status' => false];
        } else {
            $status = ['status' => true];
        }

        return response()->json($status, Response::HTTP_OK);
    }
}
