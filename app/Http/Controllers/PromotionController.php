<?php

namespace App\Http\Controllers;

use App\Http\Services\PromotionService;
use App\Product;
use App\ProductDetail;
use App\ProductMedia;
use App\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Promotion::latest()
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

    /**
     * Get promotion types
     *
     * @param  array  $id
     * @return \Illuminate\Http\Response
     */
    public function types()
    {
        $promotion_types = PromotionService::get_types();
        return response()->json($promotion_types, Response::HTTP_OK);
    }
}
