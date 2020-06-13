<?php

namespace App\Http\Controllers;

use App\Product;
use App\ProductDetail;
use App\ProductMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Product::filter($request->all())
            ->with(['category', 'media', 'product_variations'])
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
            'detail' => 'nullable',
            'category_id' => 'required',
            'price' => 'required',
            'price_discount' => 'nullable',
            'slug' => 'nullable',
            'sku' => 'nullable',
            'stock_status' => 'required',
            'stock' => 'required',
            'deduct_stock' => 'nullable',

            'price_data' => 'nullable|array',
            'price_data.minimum_purchase' => 'nullable',
            'price_data.minimum_purchase' => 'nullable',
            'price_data.discount_period' => 'nullable|array',
            'price_data.discount_period.start' => 'nullable',
            'price_data.discount_period.end' => 'nullable',

            'price_data.price_wholesale' => 'nullable|array',
            'price_data.price_wholesale.*.minimum' => 'nullable',
            'price_data.price_wholesale.*.maximum' => 'nullable',
            'price_data.price_wholesale.*.price' => 'nullable',

            'attributes' => 'nullable|array',
            'attributes.weight' => 'nullable',
            'attributes.weight.value' => 'nullable',
            'attributes.weight.unit' => 'nullable',
            'attributes.dimension' => 'nullable',
            'attributes.dimension.length' => 'nullable',
            'attributes.dimension.width' => 'nullable',
            'attributes.dimension.height' => 'nullable',
            'attributes.dimension.unit' => 'nullable',

            'stock_data' => 'nullable|array',
            'stock_data.threshold' => 'nullable',
            'stock_data.notify' => 'nullable',
            'stock_data.notify.email' => 'nullable',
            'stock_data.notify.telegram' => 'nullable',
            'stock_data.empty_action' => 'nullable',

            'variations' => 'nullable|array',
            'image_ids' => 'nullable|array',
        ]);

        // #1 deduct stock
        $request['deduct_stock'] = !isset($request['deduct_stock']) ? 0 : 1;

        // #2 detail
        if ($request['detail'] == '{"ops":[{"insert":"\n"}]}') {
            unset($request['detail']);
        }

        // #3 If wholesale min,max,price is empty, remove
        foreach ($request['price_data']['price_wholesale'] as $key => $item) {
            if (empty($item['minimum']) or empty($item['maximum']) or empty($item['price'])) {
                unset($request['price_data']['price_wholesale'][$key]);
            }
        }

        // #4 Transfrom all wholesale prices
        if (!empty($request['price_data']['price_wholesale'])) {
            $request['price_data']['price_wholesale'] = collect($request['price_data']['price_wholesale'])->transform(function ($item, $key) use ($request) {
                return [
                    'minimum' => $item['minimum'],
                    'maximum' => $item['maximum'],
                    'price' => str_replace(['RM', ' '], '', $item['price'])
                ];
            })->toArray();
        } else {
            unset($request['price_data']['price_wholesale']);
        }

        // #5 Weight value is empty, remove
        if (empty($request['attributes']['weight']['value'])) {
            unset($request['attributes']['weight']);
        }

        // #6 Dimension, any of these (length, width, height) not present, remove
        if (empty($request['attributes']['dimension']['length']) && empty($request['attributes']['dimension']['width']) && empty($request['attributes']['dimension']['height'])) {
            unset($request['attributes']['dimension']);
        }

        if (empty($request['attributes'])) {
            unset($request['attributes']);
        }

        // #7 If discount period start / end empty, remove
        if (!empty($request['price_data']['discount_period'])) {
            if (!$request['price_data']['discount_period']['start'] && !$request['price_data']['discount_period']['end']) {
                unset($request['price_data']['discount_period']);
            }
        }

        // #8 Price
        $request['price'] = str_replace(['RM', ' '], '', $request['price']);

        // #9 Price discount
        $request['price_discount'] = str_replace(['RM', ' '], '', $request['price_discount']);

        // #10 Slug
        if (!isset($request['slug'])) {
            $request['slug'] = Str::slug($request['name'], '-');
        }

        // #11 Slug
        if (empty($request['price_data']['minimum_purchase'])) {
            unset($request['price_data']['minimum_purchase']);
        }

        if (empty($request['price_data']['maximum_purchase'])) {
            unset($request['price_data']['maximum_purchase']);
        }

        // #12 unsetting empty array
        if (empty($request['price_data'])) {
            unset($request['price_data']);
        }

        $product = Product::create($request);

        if (isset($request['image_ids'])) {
            foreach ($request['image_ids'] as $image_id) {
                ProductMedia::create([
                    'product_id' => $product->id,
                    'media_id' => $image_id
                ]);
            }
        }

        if (isset($request['detail'])) {
            ProductDetail::create([
                'product_id' => $product->id,
                'detail' => $request['detail']
            ]);
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
        $product->load(['category', 'media', 'product_detail']);

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
            'name' => 'required',
            'detail' => 'nullable',
            'price' => 'required',
            'category_id' => 'required',
            'slug' => 'nullable',
            'price_discount' => 'nullable',
            'discount_period' => 'nullable|array',
            'price_wholesale' => 'nullable|array',
            'sku' => 'nullable',
            'stock_status' => 'required',
            'stock' => 'required',
            'deduct_stock' => 'nullable',
            'stock_management' => 'nullable|array',
            'weight' => 'nullable|array',
            'dimension' => 'nullable|array',
            'image_ids' => 'nullable|array',
            'variations' => 'nullable|array',
            'image_ids_delete' => 'nullable|array'
        ]);

        $product = Product::findOrFail($id);
        $product->fill($request);
        if ($product->isDirty()) {
            $product->update($request);
        }

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

        return response()->json(null, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Product::find($id)->delete();

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
        Product::whereIn($id)->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
