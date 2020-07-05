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
            'password' => 'required|min:8|max:20'
        ]);

        if (Auth::attempt($request)) {
            $response = [
                'token' => encrypt(Auth::user()->token),
                'user' => collect(Auth::user())->except('token')
            ];

            return response()->json($response, Response::HTTP_OK);
        }

        return response()->json(null, Response::HTTP_CONFLICT);
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

    // Register
    public function store(Request $request)
    {
        $request = $request->validate([
            'email' => 'required|max:100',
            'password' => 'required|min:8|max:20'
        ]);

        $user = User::create([
            'email' => $request['email'],
            'password' => Hash::make($request['password'])
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
                /**
             * Undo step to 2
             * Reset sms_sent to 0
             * Clear phone number
             * Flag update_phone
             */
            case 'change':
                request()->user()->update([
                    'step' => 2,
                    'sms_sent' => 0,
                    'phone' => null,
                    'update_phone' => DB::raw('update_phone+1')
                ]);

                return response()->json(['status' => true], Response::HTTP_OK);
                break;

            case 'verify':

                $request = $request->validate([
                    'phone' => 'required|min:10|max:11',
                    'provider' => 'required',
                    'key' => 'required',
                    'secret' => 'required'
                ]);

                if (request()->user()->sms_sent >= 5) {
                    return response()->json(['status' => false, 'sms-limit' => true], Response::HTTP_OK);
                }

                $code = mt_rand(100000, 999999);


                if ($request['provider'] == 'nexmo') {
                    config(['nexmo.api_key' => $request['key']]);
                    config(['nexmo.api_secret' => $request['secret']]);

                    $phone = $request['phone'];

                    if (strlen($phone) == 10 || strlen($phone) == 11) {
                        $start = substr($phone, 0, 1);
                        if ($start == 0) {
                            $phone = '6' . $phone;
                        } else {
                            return response()->json(['status' => false], Response::HTTP_OK);
                        }
                    }

                    $nexmo = Nexmo::message()->send([
                        'to'   => $phone,
                        'from' => 'WasapOrder.my',
                        'text' => 'WasapOrder: ' . $code . ' adalah kod pengesahan akaun anda'
                    ]);

                    $response = $nexmo->getResponseData();

                    if ($response['messages'][0]['status'] == 0) {
                        request()->user()->update([
                            'step' => 3,
                            'sms_sent' => DB::raw('sms_sent+1'),
                            'phone' => $phone,
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

    // Check subdomain existence
    public function get_user_by_subdomain(Request $request)
    {
        $request = $request->validate([
            'subdomain' => 'required|max:30'
        ]);

        $user = User::where('subdomain', $request['subdomain'])
            ->select('subdomain', 'business_name', 'email', 'phone', 'theme_id', 'token')
            ->first();

        if ($user) {
            return response()->json($user, Response::HTTP_OK);
        }

        return response()->json([], Response::HTTP_OK);
    }
}
