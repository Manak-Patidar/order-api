<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Exception;

class CompanyAuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role_id' => 2, 'is_active' => 1])) {
                $user = Auth::user();
                $success['token'] =  $user->createToken(env('APP_KEY'))->plainTextToken;
                $success['data'] =  $user;
                $user->session_expiration = now()->addDays(8);
                $user->save();
                return response()->json([
                    'status' => true,
                    'message' => 'User login successfully.',
                    'data' => $success,
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Incorrect email and password',
                ], 404);
            }
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }
    public function forgot_password(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);
            $user = User::where('email', $request->get('email'))
                ->where('role_id', 2)->first();
            if ($user) {
                $status = Password::sendResetLink(
                    $request->only('email')
                );
                $status = Password::RESET_LINK_SENT;
                if ($status == "passwords.sent") {
                    return response()->json([
                        'status' => true,
                        'message' => 'We have sent an email'
                    ], 200);
                }
            }
            return response()->json([
                'status' => false,
                'message' => 'Email does not exist'
            ], 400);
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
            $status == Password::PASSWORD_RESET;
            if ($status == "passwords.token") {
                return response()->json([
                    'status' => true,
                    'message' => 'Password Update Successfully'
                ], 200);
            }
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
