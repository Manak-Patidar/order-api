<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class DealerController extends Controller
{
    public function add(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'address' => 'required',
                    'longitude' => 'required',
                    'Attitude' => 'required',
                    'concered_name' => 'required',
                    'code' => 'required',
                    'number' => 'required',
                    'brands' => 'required',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validateUser->errors()->first(),
                ], 402);
            }
            $device = new Dealer();
            $device->imei_code = $request->get('imei_code');
            $device->mobile_version = $request->get('mobile_version');
            $device->device_name = $request->get('mobile_version');
            $device->user_id = Auth::user()->id;
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
}
