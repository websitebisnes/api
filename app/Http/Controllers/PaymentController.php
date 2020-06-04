<?php

namespace App\Http\Controllers;

use App\Payment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Payment::filter($request->all())
            ->when($request->get('with_order'), function ($q) {
                return $q->with('order');
            })
            ->latest()
            ->paginate(10);
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
            'name' => 'required',
            'price' => 'required',
            'category_id' => 'required',
            'slug' => 'required',
            'price_discount' => 'nullable',
            'sku' => 'nullable',
            'stock' => 'required',
            'weight' => 'nullable',
            'height' => 'nullable',
            'width' => 'nullable',
            'image_ids' => 'nullable'
        ]);

        $product = Payment::create($request);

        if (isset($request['image_ids'])) {
            foreach ($request['image_ids'] as $image_id) {
                ProductMedia::create([
                    'product_id' => $product->id,
                    'media_id' => $image_id
                ]);
            }
        }

        return response()->json($product, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $product->load(['category', 'media']);

        return response()->json($product, Response::HTTP_OK);
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
        $request = $request->validate([
            'id' => 'required',
            'name' => 'required',
            'price' => 'required',
            'category_id' => 'required',
            'slug' => 'required',
            'price_discount' => 'nullable',
            'sku' => 'nullable',
            'stock' => 'required',
            'weight' => 'nullable',
            'height' => 'nullable',
            'width' => 'nullable',
            'image_ids' => 'nullable',
            'image_ids_delete' => 'nullable'
        ]);

        $product = Payment::findOrFail($id);
        $product->update($request);

        if (isset($request['image_ids_delete']) && is_array($request['image_ids_delete'])) {
            ProductMedia::where('product_id', $id)->whereIn('media_id', $request['image_ids_delete'])->delete();
        }

        if (isset($request['image_ids'])) {
            foreach ($request['image_ids'] as $image_id) {
                ProductMedia::create([
                    'product_id' => $product->id,
                    'media_id' => $image_id
                ]);
            }
        }

        return response()->json($product, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Payment::find($id)->delete();

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
        Payment::whereIn($id)->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
