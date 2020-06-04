<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'customer' => 'required|array|min:3',
            'customer.first_name' => 'required',
            'customer.last_name' => 'required',
            'customer.phone_number' => 'required',
            'customer.email' => 'nullable',
            'customer.password' => 'nullable',

            'customer.address' => 'nullable',
            'customer.address2' => 'nullable',
            'customer.city' => 'nullable',
            'customer.state' => 'nullable',
            'customer.postcode' => 'nullable',
            'customer.country' => 'nullable',

            'products' => 'required|array',
            'products.*.product_id' => 'required',
            'products.*.quantity' => 'required|numeric|min:1',

            'payment' => 'required|array',
            'payment.payment_status' => 'required|numeric',
            'payment.payment_method' => 'required|numeric',

            'shipment' => 'nullable|array',
            'shipment.shipping_status' => 'nullable|numeric',
            'shipment.shipping_method' => 'nullable|numeric',

            'image_ids' => 'sometimes|array'
        ];
    }
}
