<?php

namespace App\Http\Controllers;

use App\Http\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{
    public function status()
    {
        $status = DashboardService::get_all_status();
        return response()->json($status, Response::HTTP_OK);
    }
}
