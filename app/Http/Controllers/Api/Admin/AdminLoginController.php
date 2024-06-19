<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Exception;

class AdminLoginController extends Controller
{
    protected function authenticated(Request $request, $user)
    {
        return response()->json(['user' => $user, 'token' => $user->createToken('API Token')->plainTextToken]);
    }

    public function web_login(Request $request)
    {
        // return \Hash::make('123');
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => ['required', 'email'],
                    'password' => ['required'],
                    'company_id' => ['required'],
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validateUser->errors()->first(),
                ], 401);
            }

            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                if(Auth::user()->is_active ==1)
                {
                return $this->authenticated($request, Auth::user());
                }
                else
                {
                    return response()->json(['message' => 'Invalid credentials'], 401);
                }
            }

            return response()->json(['message' => 'Invalid credentials'], 401);
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }

    public function sendResetLinkEmail(Request $request)
    {
        try {
            
            
            $request->validate(['email' => 'required|email']);
            $user = User::where('email', $request->get('email'))->first();

            if ($user) {
            $status = Password::sendResetLink(
                    $request->only('email')
                );
$status === Password::RESET_LINK_SENT;
                if ($status) {
                    if ($status == "passwords.sent") {
                    return response()->json([
                        'status' => true,
                        'message' => 'We have sent an email'
                    ], 200);
                }
                   
                }
            }

            return response()->json([
                'status' => false,
                'message' => 'Email does not exist or unable to send reset link.'
            ], 400);
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'token' => 'required',
                    'email' => 'required|email',
                    'password' => 'required|min:8',
                    'password_confirmation' => 'required|same:password',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validateUser->errors()->first(),
                    'errors' => $validateUser->errors()->toArray(),
                ], 401);
            }

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function (User $user, string $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                    ])->setRememberToken(Str::random(60));

                    $user->save();

                    // Optionally, you can dispatch an event here.
                }
            );

             $status === Password::PASSWORD_RESET
                ? response()->json(['message' => 'Password updated successfully'], 200)
                : response()->json(['message' => $status], 400);
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }
        public function reset_password(Request $request)
    {
        // return $request->all();
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'token' => 'required',
                    'email' => 'required|email',
                    'password' => 'required',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validateUser->errors()->first(),
                    'errors' => $validateUser->errors()->first(),
                ], 401);
            }
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function (User $user, string $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->setRememberToken(Str::random(60));

                    $user->save();

                    // event(new PasswordReset($user));
                }
            );
            return $status == Password::PASSWORD_RESET
        ? response()->json(['message' => __($status)], 200)
        : response()->json(['message' => __($status)], 400);
            
            return response()->json([
                'status' => false,
                'message' => "password not update",
            ], 400);
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }

}
