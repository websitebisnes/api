<?php

namespace App\Http\Controllers;

use App\Http\Services\UserService;
use App\User;
use Nexmo\Laravel\Facade\Nexmo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{

    public function authenticate(Request $request)
    {
        $request = $request->validate([
            'email' => 'required|email|max:100',
            'password' => 'required|max:20'
        ]);

        if (Auth::attempt($request)) {
            // Authentication passed...
            $token = encrypt(Auth::user()->token);

            $response = [
                'status' => true,
                'token' => $token,
                'user' => collect(Auth::user())->only(['name', 'phone', 'subdomain'])->toArray()
            ];
        } else {
            $response = [
                'status' => false,
            ];
        }

        return response()->json($response, Response::HTTP_OK);
    }

    public function recover(Request $request)
    {
        $request = $request->validate([
            'email' => 'required|email|max:100',
        ]);

        $user = User::where('email', $request['email'])->first();
        $response =  ['status' => true];

        if ($user) {
            $response = [
                'status' => true,
                'user_id' => $user['id'],
                'email' => $user['email'],
                '_token' => encrypt(URL::temporarySignedRoute('recover.checkpoint', now()->addMinutes(60), ['user' => $user['id']]))
            ];
        }

        return response()->json($response, Response::HTTP_OK);
    }

    // Recover checkpoint
    public function recover_checkpoint(Request $request)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }

        $user_token = User::where('id', $request['user'])->value('token');

        $response = [
            'token' => encrypt($user_token)
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    // Get self
    public function users()
    {
        return response()->json(request()->user(), Response::HTTP_OK);
    }

    // Register
    public function store(Request $request)
    {
        $request = $request->validate([
            'email' => 'required|max:100'
        ]);

        $user = User::create([
            'email' => $request['email'],
            'password' => Hash::make(Str::random(10))
        ]);

        $token = $user->createToken('token');
        $user_token = $token->plainTextToken;

        $user->update([
            'step' => 2,
            'token' => $user_token
        ]);

        return response()->json(['token' => encrypt($user_token)], Response::HTTP_CREATED);
    }

    // Phone verification
    public function phone(Request $request, $action)
    {

        switch ($action) {
            case 'verify':

                $request = $request->validate([
                    'phone' => 'required',
                    'provider' => 'required',
                    'key' => 'required',
                    'secret' => 'required'
                ]);

                if (request()->user()->sms_sent >= 3) {
                    return response()->json(['status' => false, 'sms-limit' => true], Response::HTTP_OK);
                }

                $code = mt_rand(100000, 999999);

                if ($request['provider'] == 'nexmo') {
                    config(['nexmo.api_key' => $request['key']]);
                    config(['nexmo.api_secret' => $request['secret']]);

                    $nexmo = Nexmo::message()->send([
                        'to'   => $request['phone'],
                        'from' => 'WasapOrder.my',
                        'text' => 'WasapOrder: ' . $code . ' adalah kod pengesahan anda'
                    ]);

                    $response = $nexmo->getResponseData();

                    if ($response['messages'][0]['status'] == 0) {
                        request()->user()->update([
                            'step' => 3,
                            'sms_sent' => DB::raw('sms_sent+1'),
                            'phone' => $request['phone'],
                            'verify_code' => $code
                        ]);

                        return response()->json(['status' => true], Response::HTTP_CREATED);
                    } else {
                        return response()->json(['status' => false], Response::HTTP_OK);
                    }
                }

                break;

            case 'verify_code':
                $request = $request->validate([
                    'code' => 'required|min:6|max:6'
                ]);

                if ($request['code'] == request()->user()->verify_code) {
                    request()->user()->update([
                        'step' => 4,
                        'phone_verified_at' => now()
                    ]);
                    return response()->json(['status' => true], Response::HTTP_OK);
                } else {
                    return response()->json(['status' => false], Response::HTTP_OK);
                }

                break;
        }
    }

    public function update(Request $request)
    {
        $request = $request->validate([
            'name' => 'required|max:225',
            'business_name' => 'required|max:225',
            'subdomain' => 'required|max:50'
        ]);

        $request['step'] = 5;

        request()->user()->update($request);

        return response()->json(['status' => true], Response::HTTP_OK);
    }

    // Check subdomain existence
    public function check_subdomain(Request $request)
    {
        $request = $request->validate([
            'subdomain' => 'required|max:30'
        ]);

        $status = true;
        $subdomain = User::where('subdomain', $request['subdomain'])->exists();

        if ($subdomain) {
            $status = false;
        }

        return response()->json(['status' => $status], Response::HTTP_OK);
    }

    // Check email existence
    public function check_email(Request $request)
    {
        $request = $request->validate([
            'email' => 'required|max:100'
        ]);

        $status = true;
        $email = User::where('email', $request['email'])->exists();

        if ($email) {
            $status = false;
        }

        return response()->json(['status' => $status], Response::HTTP_OK);
    }
}
