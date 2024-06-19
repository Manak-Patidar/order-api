<?php

namespace App\Http\Controllers\Api\Sales;


use App\Http\Controllers\Controller;
use App\Models\Dealer;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Device;
use App\Models\Role;
use App\Models\SubDealer;
use App\Models\Client;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class SalesController extends Controller
{


    public function clients(Request $request)
    {
        try {
            
             $clients = Client::where('is_active',1)
               ->whereIn('categoery_id', [5, 6, 7, 8, 9, 10])
               ->where('emp_code',Auth::user()->empCode)
               ->latest()
               ->get();
        
           
            if ($clients) {
                return response()->json([
                    'status' => true,
                    'message' => 'dealer list',
                    'data' => $clients,
                ], 200);
            }
            return response()->json([
                'status' => false,
                'message' => 'No data found',
                'errors' => 'No data found'
            ], 404);
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }

    public function clients_add(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'clientName' => 'required',
                    'address' => 'required',
                    'concered_person' => 'required',
                      'phone' => 'required|numeric',
                    //  'longitude' => 'required',
                    // 'latitude' => 'required',
                    'categoery_id' => 'required',
                    'device_time' => 'required',
                    'designation' => 'required',
                    
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validateUser->errors()->first(),
                ], 402);
            }

            $client = new Client();
             $client->u_id = 'v_' . Auth::user()->empCode . date('y-m-d:H:i:s');
            $client->clientName = $request->get('clientName');
            $client->address = $request->get('address');
            $client->concered_person = $request->get('concered_person');
            $client->phone = $request->get('phone');
            $client->email = $request->get('email');
            $client->emp_code =  Auth::user()->empCode;
            $client->designation = $request->get('designation');
            $client->longitude = $request->get('longitude');
            $client->latitude = $request->get('latitude');
            $client->categoery_id = $request->get('categoery_id');
            $client->device_time = $request->get('device_time');
            $client->brand = $request->get('brand');
            $client->other_category = $request->get('other_category');
            $client->other = $request->get('other') ? $request->get('other'):'';
            $client->company_id = \Auth::user()->company_id;
            if ($client->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'data save successfully',
                    'data' => $client,
                ], 200);
            }
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }
    public function clients_update(Request $request)
    {
        try {

            $validateUser = Validator::make(
                $request->all(),
                [
                     'id' => 'required',
                    'clientName' => 'required',
                    'address' => 'required',
                    'concered_person' => 'required',
                      'phone' => 'required|numeric',

                    // 'longitude' => 'required',
                    // 'latitude' => 'required',
                    'categoery_id' => 'required',
                    'device_time' => 'required',
                    'designation' => 'required',
                    
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validateUser->errors()->first(),
                ], 402);
            }
            $client =  Client::find($request->get('id'));
            $client->clientName = $request->get('clientName');
            $client->address = $request->get('address');
            $client->concered_person = $request->get('concered_person');
            $client->phone = $request->get('phone');
            $client->email = $request->get('email');
            $client->phone = $request->get('phone');
            $client->designation = $request->get('designation');
            $client->longitude = $request->get('longitude');
            $client->latitude = $request->get('latitude');
            $client->categoery_id = $request->get('categoery_id');
            $client->device_time = $request->get('device_time');
            $client->brand = $request->get('brand');
            $client->other_category = $request->get('other_category');
            $client->other = $request->get('other') ? $request->get('other'):'';
            if ($client->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'data update successfully',
                    'data' => $client,
                ], 200);
            }
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }
    public function dealers()
{
    
    try {
        $dealer = null;
        if(Auth::user()->role_id == 1)
        {
             // If the authenticated user's ID is 1 (presumably an admin), 
             // fetch all dealers.
             $dealer = Dealer::where('company_id',\Auth::user()->company_id)->get();
        }
        else {
             // If the authenticated user is not an admin, fetch dealers based on certain conditions.
             $dealer = Dealer::where('dealear_active', 1)
                ->where('role_id', 3)
                ->where('emp_code', Auth::user()->empCode)
                 ->where('company_id',\Auth::user()->company_id)
                ->latest()  // Orders by the latest created
                ->get();
        }
       
        if ($dealer) {
            // If dealers are found, return a success response with the dealer data.
            return response()->json([
                'status' => true,
                'message' => 'dealer list',
                'data' => $dealer,
            ], 200);
        }
        // If no dealers are found, return a failure response.
        return response()->json([
            'status' => false,
            'message' => 'No data found',
            'errors' => 'No data found'
        ], 404);
    } catch (Exception $ex) {
        // If an exception occurs, return an error response.
        return response()->json([
            'status' => false,
            'message' => $ex->getMessage()
        ], 500);
    }
}

  public function dealers_update(Request $request)
{
    try {
        $validateUser = Validator::make(
            $request->all(),
            [
                // 'dealerName' => 'required',
                // 'address' => 'required',
                // 'role_id' => 'required',
                // 'concered_person' => 'required',
                // 'dealer_code' => 'required',
                // 'designation' => 'required',
                'id' => 'required',
            ]
        );

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validateUser->errors()->first(),
            ], 422);
        }

        $dealer = Dealer::findOrFail($request->id);
            //  $dealer->dealerName = $request->dealerName;
            $dealer->address = $request->address ?$request->address :$dealer->address;
            // $dealer->role_id = $request->role_id;
            $dealer->email = $request->email ?? ''; // Use null coalescing operator for default value
            $dealer->longitude = $request->longitude ?$request->longitude :$dealer->longitude ;
            $dealer->latitude = $request->latitude ?$request->latitude: $dealer->latitude ;
            $dealer->concered_person = $request->concered_person;
            // $dealer->dealer_code = $request->get('dealer_code') ?$request->get('dealer_code'):$dealer->dealer_code;
            $dealer->number = $request->get('number')?$request->get('number'): $dealer->number;
            $dealer->designation = $request->get('designation') ?$request->get('designation'): $dealer->designation;
           if($dealer->save()){
                return response()->json([
                'status' => true,
                'message' => 'Dealer updated successfully.',
            ]);
           }
       
    } catch (Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
        return response()->json([
            'status' => false,
            'message' => 'Dealer not found.',
        ], 404);
    } catch (Exception $ex) {
        return response()->json([
            'status' => false,
            'message' => $ex->getMessage()
        ], 500);
    }
}




    public function dealers_details($id)
    {
        try {
            $dealer = Dealer::where('dealear_active', 1)
                ->where('role_id', 3)
                ->where('emp_code' ,Auth::user()->empCode)
                ->where('id', $id)
                ->first();
            if ($dealer) {
                return response()->json([
                    'status' => true,
                    'message' => 'dealer list',
                    'data' => $dealer,
                ], 200);
            }
            return response()->json([
                'status' => false,
                'message' => 'No data found',
                'errors' => 'No data found'
            ], 404);
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    // 'name' => 'required',
                    'address' => 'required',
                    // 'longitude' => 'required',
                    // 'Attitude' => 'required',
                    'geotag_address' => 'required',
                    // 'concered_name' => 'required',
                    'id' => 'required',
                    'designation' => 'required',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validateUser->errors()->first(),
                ], 402);
            }
            $dealer =  Dealer::find($request->get('id'));
            $dealer->address = $request->get('address');
            $dealer->geotag_address = $request->get('geotag_address');
            $dealer->designation = $request->get('designation');
            if ($dealer->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Dealer Update successfully',
                    'data' => $dealer,
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
    public function sub_dealer()
    {
        try {
             $dealer = SubDealer::where('sub_dealear_active', 1)
              ->where('emp_code' ,Auth::user()->empCode)
                ->where('role_id', 4)
                ->latest()
                ->get();
            if ($dealer) {
                return response()->json([
                    'status' => true,
                    'message' => 'subdealer list',
                    'data' => $dealer,
                ], 200);
            }
            return response()->json([
                'status' => false,
                'message' => 'No data found',
                'errors' => 'No data found'
            ], 404);
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }
    public function Sub_dealers_update(Request $request)
{
    try {
        $validateUser = Validator::make(
            $request->all(),
            [
                // 'dealerName' => 'required',
                // 'address' => 'required',
                // 'role_id' => 'required',
                // 'concered_person' => 'required',
                // 'dealer_code' => 'required',
                // 'designation' => 'required',
                'id' => 'required',
            ]
        );

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validateUser->errors()->first(),
            ], 422);
        }

        $dealer = SubDealer::findOrFail($request->id);
            //  $dealer->dealerName = $request->dealerName;
            $dealer->address = $request->address ? $request->address:$dealer->address;
            // $dealer->role_id = $request->role_id;
            $dealer->email = $request->email ?? ''; // Use null coalescing operator for default value
            $dealer->longitude = $request->longitude ?$request->longitude: $dealer->longitude;
            $dealer->latitude = $request->latitude ?$request->latitude: $dealer->latitude;
            $dealer->concered_person = $request->concered_person;
            // $dealer->sub_dealer_code = $request->get('sub_dealer_code') ?? '';
            $dealer->number = $request->get('number')?$request->get('number'): $dealer->number;
            $dealer->designation = $request->get('designation') ? $request->get('designation'): $dealer->designation;
           if($dealer->save()){
                return response()->json([
                'status' => true,
                'message' => 'Sub dealers update updated successfully.',
            ]);
           }
       
    } catch (Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
        return response()->json([
            'status' => false,
            'message' => 'Dealer not found.',
        ], 404);
    } catch (Exception $ex) {
        return response()->json([
            'status' => false,
            'message' => $ex->getMessage()
        ], 500);
    }
}
    
    public function category()
    {
        $category = Role::whereIn('id', [3, 4, 5, 6, 7, 8, 10])->get();
        if ($category) {
            return response()->json([
                'status' => true,
                'message' => 'category list',
                'data' => $category,
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'No data found',
            'errors' => 'No data found'
        ], 404);
    }
    public function device_info(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
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
        $old_device =     Device::where('imei_code',$request->get('imei_code'))
            ->where('mobile_version',$request->get('mobile_version'))
            ->where('device_name',$request->get('mobile_version'))->first();
            if(!$old_device){
                $device = new Device();
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
            }
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }
    public function customers_add(Request $request)
    {
        // return $request->all();
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'address' => 'required',
                    // 'longitude' => 'required',
                    // 'Attitude' => 'required',
                    'concered_name' => 'required',
                    'code' => 'required',
                    'number' => 'required',
                    'email' => 'required|email|',
                    'role_id' => 'required',
                    // 'code' =>'required',
                    'state' => 'required',
                    'rm' => 'required',
                    'territory' => 'required',
                    'brands' => 'required',
                    'designation' => 'required',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validateUser->errors()->first(),
                ], 402);
            }
            $user = new Client();
            $user->name = $request->get('name');
            $user->address = $request->get('address');
            $user->concered_name = $request->get('concered_name');
            $user->phone = $request->get('number');
            $user->role_id = $request->get('role_id');
            $user->designation = $request->get('designation');
            $user->email = $request->get('email');

            $user->longitude =  $request->get('longitude') ? $request->get('longitude') : '';
            $user->Attitude = $request->get('Attitude') ? $request->get('Attitude') : '';

            // Corrected variable name from $customers to $user
            if ($user->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Customer saved successfully',
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


    public function customers_update(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'address' => 'required',
                    // 'longitude' => 'required',
                    // 'Attitude' => 'required',
                    'concered_name' => 'required',
                    'code' => 'required',
                    'number' => 'required',
                    // 'email' => 'required|email|unique:users,email',
                    'role_id' => 'required',
                    'code' => 'required',
                    'state' => 'required',
                    'rm' => 'required',
                    'territory' => 'required',
                    'brands' => 'required',
                    'id' => 'required',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validateUser->errors()->first(),
                ], 402);
            }
            $customers =  Dealer::find($request->get('id'));
            $user =  User::find($customers->user_id);
            $user->name = $request->get('name');
            // $user->email = $request->get('email');
            $user->password = Hash::make('admin@123');
            $user->role_id = $request->get('role_id');
            $user->empCode = $request->get('code');
            $user->state = $request->get('state');
            $user->rm = $request->get('rm');
            $user->is_active = 1;
            $user->territory = $request->get('territory');
            $user->state = $request->get('state');
            if ($user->save()) {
                $customers =  Dealer::find($request->get('id'));
                $customers->user_id = $user->id;
                $customers->address = $request->get('address');
                $customers->longitude = $request->get('longitude') ? $request->get('longitude') : '';
                $customers->Attitude = $request->get('Attitude') ? $request->get('Attitude') : '';
                $customers->geotag_address = $request->get('geotag_address');
                $customers->concered_name = $request->get('concered_name');
                $customers->code = $request->get('code');
                $customers->number = $request->get('number');
                $customers->designation = $request->get('designation');
                $customers->brands = $request->get('brands');
                $customers->other_category = $request->get('other_category') ? $request->get('other_category') : '';
                if ($customers->save()) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Customers save successfully',

                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid data',
                    ], 404);
                }
            }
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }

    public function roles()
    {
        $roles = Role::all();
        if ($roles) {
            return response()->json([
                'status' => true,
                'message' => 'category list',
                'data' => $roles,
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'No data found',
            'errors' => 'No data found'
        ], 404);
    }
}
