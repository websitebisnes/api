<?php

namespace App\Http\Controllers;

use App\OrderReceipt;
use App\Payment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request = $request->validate([
            'model' => 'required|string'
        ]);

        $fillable = null;

        if (class_exists("App\\" . $request['model'])) {
            $fillable = call_user_func_array(array("App\\" . $request['model'], 'updateable'), array(1, 1));
            return response()->json($fillable, Response::HTTP_OK);
        }
        return response()->json(null, Response::HTTP_NO_CONTENT);
        
    }
}
