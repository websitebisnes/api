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
            'name' => 'required|min:10',
            'detail_short' => 'required|min:10|max:100',
            'price' => 'required',
            'stock_status' => 'required',
            'stock' => 'required',

            'detail' => 'nullable|array',
            'detail.delta' => 'nullable',
            'detail.text' => 'nullable',
            'detail.html' => 'nullable',

            'category_id' => 'nullable',
            'price_discount' => 'nullable',
            'slug' => 'nullable',
            'sku' => 'nullable',
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
            'stock_data.purchase_limit_minimum' => 'nullable',
            'stock_data.purchase_limit_maximum' => 'nullable',

            'variations' => 'nullable|array',
            'image_ids' => 'nullable|array',
        ]);

        // #1 deduct stock
        $request['deduct_stock'] = !isset($request['deduct_stock']) ? 0 : 1;

        // #2 detail
        if (!empty($request['detail']['delta']) && $request['detail']['delta'] == '{"ops":[{"insert":"\n"}]}') {
            unset($request['detail']);
        }

        // #4 Transfrom all wholesale prices
        if (!empty($request['price_data']['price_wholesale'])) {
            foreach ($request['price_data']['price_wholesale'] as $key => $item) {
                if (empty($item['minimum']) or empty($item['maximum']) or empty($item['price'])) {
                    unset($request['price_data']['price_wholesale'][$key]);
                }
            }

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
        $request['price_discount'] = !empty($request['price_discount']) ? str_replace(['RM', ' '], '', $request['price_discount']) : null;

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

        // Product Images
        if (isset($request['image_ids'])) {
            foreach ($request['image_ids'] as $image_id) {
                ProductMedia::create([
                    'product_id' => $product->id,
                    'media_id' => $image_id
                ]);
            }
        }

        // Variations images
        collect($request['variations'])->each(function ($item) use ($product) {
            if (isset($item['image_id']) && !empty($item['image_id'])) {
                ProductMedia::create([
                    'product_id' => $product->id,
                    'media_id' => $item['image_id']
                ]);
            }
        });

        if (isset($request['detail'])) {
            ProductDetail::create([
                'product_id' => $product->id,
                'delta' => $request['detail']['delta'] ?? null,
                'text' => $request['detail']['text'] ?? null,
                'html' => $request['detail']['html'] ?? null
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
            'id' => 'required',
            'name' => 'required|min:10',
            'detail_short' => 'required|min:10|max:100',
            'price' => 'required',
            'stock_status' => 'required',
            'stock' => 'required',

            'detail' => 'nullable|array',
            'detail.delta' => 'nullable',
            'detail.text' => 'nullable',
            'detail.html' => 'nullable',

            'category_id' => 'nullable',
            'price_discount' => 'nullable',
            'slug' => 'nullable',
            'sku' => 'nullable',
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
            'stock_data.purchase_limit_minimum' => 'nullable',
            'stock_data.purchase_limit_maximum' => 'nullable',

            'variations' => 'nullable|array',
            'image_ids' => 'nullable|array',
            'image_ids_delete' => 'nullable|array'
        ]);

        // #1 deduct stock
        $request['deduct_stock'] = !isset($request['deduct_stock']) ? 0 : 1;

        // #2 detail
        if ($request['detail']['delta'] == '{"ops":[{"insert":"\n"}]}') {
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

        // #13 variations
        if (empty($request['variations'])) {
            $request['variations'] = null;
        }

        $product = Product::findOrFail($id);
        $product->fill($request);
        if ($product->isDirty()) {
            $product->update($request);
        }

        if (isset($request['image_ids_delete']) && is_array($request['image_ids_delete'])) {
            ProductMedia::where('product_id', $id)->whereIn('media_id', $request['image_ids_delete'])->delete();
        }

        // Images
        if (isset($request['image_ids'])) {
            foreach ($request['image_ids'] as $image_id) {
                ProductMedia::create([
                    'product_id' => $product->id,
                    'media_id' => $image_id
                ]);
            }
        }

        // Variations images
        collect($request['variations'])->each(function ($item) use ($product) {
            if (isset($item['image_id']) && !empty($item['image_id'])) {
                ProductMedia::create([
                    'product_id' => $product->id,
                    'media_id' => $item['image_id']
                ]);
            }
        });

        if (isset($request['detail'])) {
            //$detail = ProductDetail::where('product_id', $product->id)->exists();
            /*if ($detail) {
                ProductDetail::where('product_id', $product->id)
                    ->update([
                        'delta' => $request['detail']['delta'] ?? null,
                        'text' => $request['detail']['text'] ?? null,
                        'html' => $request['detail']['html'] ?? null
                    ]);
            } else {*/
            ProductDetail::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'delta' => $request['detail']['delta'] ?? null,
                    'text' => $request['detail']['text'] ?? null,
                    'html' => $request['detail']['html'] ?? null
                ]
            );
            //}
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

    // Check
    public function check(Request $request)
    {
        $request = $request->validate([
            'name' => 'required'
        ]);

        $product = Product::where('name', $request['name'])->exists();

        return response()->json(['exist' => $product], Response::HTTP_OK);
    }
}
