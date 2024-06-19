<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Device;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use App\Models\plumbercategory;
use App\Mail\ExcelEmail;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Client;
use App\Models\Dealer;
use App\Models\SubDealer;

class SendOtpController extends Controller
{
    public function send(Request $request)
    {

        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required',

                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validateUser->errors()->first(),
                ], 401);
            }
            $otp = mt_rand(100000, 999999); // Generate a 6-digit OTP

            $user = User::where('email', $request->get('email'))->first();
            if ($user) {
                $user->otp = $otp;
                if ($user->save()) {
                    $recipient = $request->get('email');

                    $data = Mail::to($recipient)->send(new \App\Mail\SendOtp($otp));
                    if ($data) {
                        return response()->json([
                            'status' => true,
                            'message' => 'Email send',
                        ], 200);
                    }
                }
            } else {

                return response()->json([
                    'status' => false,
                    'message' => 'no record found',
                ], 404);
            }
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }

    public function verify(Request $request)
    {

        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'otp' => 'required',
                    'email' => 'required',
                    'imei_code' => 'required',
                    'mobile_version' => 'required',
                    'device_name' => 'required',
                    'company_id' => 'required',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validateUser->errors()->first(),
                ], 401);
            }

            // $storedOtp = \Session::get('otp');
            $user = User::where('email', $request->get('email'))
                ->where('company_id', $request->get('company_id'))
                ->first();
            if ($user) {
                if ($user->otp == $request->get('otp')) {
                    // $user->otp = '';
                    // $user->save();
                    $device_info =  Device::where('mobile_version', $request->get('mobile_version'))
                        ->where('device_name', $request->get('device_name'))
                        ->where('imel_code', $request->get('imei_code'))
                        ->where('mobile_version', $request->get('mobile_version'))
                        ->where('user_id', $user->id)

                        ->first();
                    if ($device_info) {

                        $device_info->app_version = $request->get('app_version') ? $request->get('app_version') : "";
                        $device_info->save();
                        $users = User::select('name', 'empCode', 'role_id', 'email', 'rm', 'territory')
                            ->where('email', $request->get('email'))
                            ->first();
                        $users['device'] = $device_info;
                        $users['token'] =  $user->createToken(env('APP_KEY'))->plainTextToken;
                        return response()->json([
                            'status' => true,
                            'message' => 'OTP verified',
                            'data' => $users,
                        ], 200);
                    } else {
                        $device = new Device();
                        $device->imel_code = $request->get('imei_code');
                        $device->mobile_version = $request->get('mobile_version');
                        $device->device_name = $request->get('device_name');
                        $device->user_id = $user->id;
                        $device->emp_code = $user->empCode;
                        $device->app_version = $request->get('app_version') ? $request->get('app_version') : "";
                        $device->save();
                        $user['token'] =  $user->createToken(env('APP_KEY'))->plainTextToken;

                        $user['device'] = Device::where('mobile_version', $request->get('mobile_version'))
                            ->where('device_name', $request->get('device_name'))
                            ->where('imel_code', $request->get('imei_code'))
                            ->where('mobile_version', $request->get('mobile_version'))
                            ->where('user_id', $user->id)->first();
                        return response()->json([
                            'status' => true,
                            'message' => 'Device info save successuflly',
                            'data' => $user,
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid OTP',
                    ], 401);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Not found',
                ], 401);
            }
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }
    public function user_info(Request $request)
    {
        try {
            $users = User::select('name', 'empCode', 'role_id', 'email', 'rm', 'territory', 'state', 'company_id')
                ->where('id', \Auth::user()->id)
                ->first();
            $users->company = Company::select('name')
                ->where('id', $users->company_id)->first();
            $users['lock_user'] = \DB::table('log_user')->first();
            $users['device'] = Device::where('user_id', \Auth::user()->id)
                ->where('device_active', 1)
                ->first();
            if ($users) {
                return response()->json([
                    'status' => true,
                    'message' => 'User fetch successfully',
                    'data' => $users,
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found',
                ], 401);
            }
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }
    public function register(Request $request)
    {
        // return $request->all();
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'empCode' => 'required',
                    'rm' => 'required',
                    'territory' => 'required',
                    'state' => 'required',
                    'email' => 'required|email|unique:users,email'
                ]

            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validateUser->errors()->first(),
                ], 402);
            }
            $user = new User();
            $user->name = $request->get('name');
            $user->empCode = $request->get('empCode');
            $user->rm = $request->get('rm');
            $user->territory = $request->get('territory');
            $user->email = $request->get('email');
            $user->password = Hash::make('admin@123');
            $user->state = $request->get('state');

            $user->is_active = 1;

            $user->role_id = 2;
            if ($user->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Registration  successfully',
                    'data' => Auth::user(),
                ], 200);
            }
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }
    public function device_info(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required',
                    'imei_code' => 'required',
                    'mobile_version' => 'required',
                    'device_name' => 'required',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validateUser->errors()->first(),
                ], 402);
            }
            $user = User::where('email', $request->get('email'))->first();
            $device = new Device();
            $device->imel_code = $request->get('imei_code');
            $device->mobile_version = $request->get('mobile_version');
            $device->device_name = $request->get('mobile_version');
            $device->user_id = $user->id;
            if ($device->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Device info save successfully',
                    'data' => Auth::user(),
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid data',
                ], 404);
            }
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }
    public function updatePassword(Request $request)
    {
        $validateUser = Validator::make(
            $request->all(),
            [
                'current_password' => 'required',
                'new_password' => 'required|min:8',
                'confirm_password' => 'required|same:new_password',
            ]
        );

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validateUser->errors()->first(),
            ], 401);
        }




        $user = auth()->user();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 401);
        }

        $user->update([
            'password' => Hash::make($request->input('new_password')),
        ]);

        return response()->json(['message' => 'Password updated successfully'], 200);
    }
    public function other()
    {
        try {
            $data = plumbercategory::all();
            return response()->json([
                'status' => true,
                'data' => $data,
            ], 200);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }

    public function all_user_email(Request $request)
    {
        return $this->email_report();
        Excel::store(new UsersExport, 'public/Excel/Excel.xlsx');

        // $otp ='123';
        //  $data = Mail::to('patidarmanak6@gmai();l.com')->send(new \App\Mail\SendOtp($otp));
        Mail::to('aqibulhaque630@gmail.com')->send(new ExcelEmail());
    }

    private function email_report()
    {

        try {

            // Retrieve data from the database

            $dealers = Dealer::with('user')->get()->map(function ($dealer) {
                return [
                    'id' => $dealer->id,
                    'uid' => $dealer->uid,
                    'empname' => $dealer->user->name,
                    'name' => $dealer->dealerName,  // Ensure name is included
                    'categoery_id' => '0',
                    'emp_code' => $dealer->emp_code,
                    'company_id' => $dealer->company_id,

                ];
            });
            $clients = Client::with('user')->get()->map(function ($client) {
                return [
                    'id' => $client->id,
                    'uid' => $client->uid,
                    'empname' => $client->user->name,
                    'name' => $client->clientName,  // Assuming name is in the related user
                    'emp_code' => $client->emp_code,
                    'company_id' => $client->company_id,
                    'categoery_id' => $client->categoery_id,
                ];
            });
            $sub_dealers = SubDealer::with('user')->get()->map(function ($subDealer) {
                return [
                    'id' => $subDealer->id,
                    'uid' => $subDealer->uid,
                    'empname' => $subDealer->user->name,
                    'name' => $subDealer->name,
                    'emp_code' => $subDealer->emp_code,
                    'company_id' => $subDealer->company_id,
                    'categoery_id' => '1',
                ];
            });
            // Check if data is retrieved
            if ($dealers->isEmpty() && $clients->isEmpty() && $sub_dealers->isEmpty()) {
                return response()->json([
                    'status' => true,
                    'data' => [
                        'AllData' => []
                    ],
                ], 200);
            }

            // Merge all data into a single collection
            $allData = $dealers->concat($clients)->concat($sub_dealers);

            // Format the response
            return response()->json([
                'status' => true,
                'data' => [
                    'AllData' => $allData
                ],
            ], 200);
        } catch (\Exception $ex) {
            // Handle exceptions and return a response
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }
}
