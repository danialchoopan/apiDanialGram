<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PHPUnit\TextUI\XmlConfiguration\Extension;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $creds = $request->only('email', 'password');
        if (!$token = auth()->attempt($creds)) {
            return response()->json([
                'success' => false
            ]);
        }

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => Auth::user()
        ]);
    }

    public function complete_user_info(Request $request)
    {
        // try {
            $user = auth()->user();
            if ($request->photo != '') {
                $photo = time() . '.jpg';
                file_put_contents('storage/profiles' . $photo, base64_decode($request->photo));
                $user->photo = 'storage/profiles' . $photo;
            }
            $user->name = $request->name;
            $user->last_name = $request->last_name;
            $user->save();
            return response([
                'success' => true,
                'message' => 'user updated',
                'user' => auth()->user()
            ]);
        // } catch (Exception $e) {
        //     return response([
        //         'success' => false,
        //         'message' => $e
        //     ]);
        // }
    }


    public function register(Request $request)
    {
        $encripted_password = Hash::make($request->password);
        $user = new User();
        try {
            $user->email = $request->email;
            $user->password = $encripted_password;
            $user->save();
        } catch (Exception $e) {
            return response([
                'success' => false,
                'message' => $e
            ]);
        }
        return $this->login($request);
    }

    public function check_token()
    {
        return response([
            'success' => true
        ]);
    }


    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::parsetoken($request->token));
            return response()->json([
                'success' => true,
                'message' => 'logout success'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e
            ]);
        }
    }
}
