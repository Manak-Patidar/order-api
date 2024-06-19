<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use App\Models\Visits;
use App\Models\Dealer;
use App\Models\PjbReport;
use App\Models\Device;
use App\Models\SubDealer;
use App\Models\GeoTag;
use Illuminate\Support\Facades\Validator;
class AdminController extends Controller
{
   public function user()
{
    try {
        if(\Auth::user()->role_id == 1)
        {
            $users = User::whereIn('role_id', [2, 3, 4, 5])
             ->where('company_id',\Auth::user()->company_id)
            ->get();
            foreach ($users as $user) {
                $device = Device::select('app_version')->where('user_id', $user->id)->first();
                $user->device = $device; // Add device information to the user 
            }
        }
        else
        {
            $user = User::find(\Auth::user()->id); // Singular variable name as we fetch only one user
            $device = Device::select('app_version')->where('user_id', $user->id)->first();
            $user->device = $device;
            $users = [$user]; // Wrapping the single user in an array for consistency
        }
        
        if (!$users || empty($users)) {
            return response()->json([
                'status' => false,
                'message' => 'No users found',
                'errors' => 'No users found'
            ], 404);
        }
        
        return response()->json([
            'status' => true,
            'message' => 'User list',
            'data' => $users,
        ], 200);
    } catch (Exception $ex) {
        return response()->json([
            'status' => false,
            'message' => $ex->getMessage()
        ], 500);
    }
}


    public function pjp_add(Request $request)
    {
         try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'category' => 'required',
                    'concerned_person' => 'required',
                    'date' => 'required',
                    // 'image' =>  'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'server_time' => 'required',
                    'firm_id' => 'required',
                    'firm_name' => 'required',
                      'empCode' => 'required',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validateUser->errors()->first(),
                ], 401);
            }

            $imagePath = $request->file('image') ? $request->file('image')->store('images') : '';
            $pjp = new PjbReport();
            $pjp->category = $request->get('category');
            $pjp->concerned_person = $request->get('concerned_person');
            $pjp->date = $request->get('date');
            $pjp->emp_code = $request->get('empCode');
            $pjp->firm_name = $request->get('firm_name');
            $pjp->server_time = $request->get('server_time');
            $pjp->firm_name = $request->get('firm_name');
            // $pjp->type = $request->get('type') ? $request->get('type') : '';
            $pjp->remarks = $request->get('remarks') ? $request->get('remarks') : 'null';
            $pjp->payment_status = $request->get('payment_status') ? $request->get('payment_status') : '';
            // $pjp->payment_method = $request->get('payment_mathod') ? $request->get('payment_mathod') : '';
            $pjp->amount = $request->get('amount') ? $request->get('amount') : '';
            $pjp->billNo = $request->get('billNo') ? $request->get('billNo') : '';
            $pjp->chequeNo = $request->get('chequeNo') ? $request->get('chequeNo') : '';
            $pjp->bankName = $request->get('bankName') ? $request->get('bankName') : '';
            $pjp->transferId = $request->get('transferId') ? $request->get('transferId') : '';
            $pjp->payment_method = $request->get('payment_method') ? $request->get('payment_method') : '';
            $pjp->image = $imagePath ? $imagePath : '';
            $pjp->firm_id = $request->get('firm_id');
            $pjp->schedule_date = $request->get('date');
             $pjp->company_id = \Auth::user()->company_id;
            if ($pjp->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Pjb info save successuflly',
                    'data' => PjbReport::latest()->get(),
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'error' => 'unable to create pjb report',
                ], 401);
            }
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }
    
    public function pjp_update(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'category' => 'required',
                    'concerned_person' => 'required',
                    'date' => 'required',
                    // 'image' =>  'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'server_time' => 'required',
                    'firm_id' => 'required',
                    'firm_name' => 'required',
                      'empCode' => 'required',
                       'id' => 'required',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validateUser->errors()->first(),
                ], 401);
            }
            $schedule_data = '';
            $imagePath = $request->file('image') ? $request->file('image')->store('images') : '';
            $pjp =  PjbReport::find($request->get('id'));
             if($pjp->schedule_date != $request->get('date'))
             {
                 $schedule_data = $request->get('date');
             }
            $pjp->category = $request->get('category');
            $pjp->concerned_person = $request->get('concerned_person');
            $pjp->date = $request->get('date');
            $pjp->emp_code = $request->get('empCode');
            $pjp->firm_name = $request->get('firm_name');
            $pjp->server_time = $request->get('server_time');
            $pjp->firm_name = $request->get('firm_name');
            // $pjp->type = $request->get('type') ? $request->get('type') : '';
            $pjp->remarks = $request->get('remarks') ? $request->get('remarks') : 'null';
            $pjp->payment_status = $request->get('payment_status') ? $request->get('payment_status') : '';
            // $pjp->payment_method = $request->get('payment_mathod') ? $request->get('payment_mathod') : '';
            $pjp->amount = $request->get('amount') ? $request->get('amount') : '';
            $pjp->billNo = $request->get('billNo') ? $request->get('billNo') : '';
            $pjp->chequeNo = $request->get('chequeNo') ? $request->get('chequeNo') : '';
            $pjp->bankName = $request->get('bankName') ? $request->get('bankName') : '';
            $pjp->transferId = $request->get('transferId') ? $request->get('transferId') : '';
            $pjp->payment_method = $request->get('payment_method') ? $request->get('payment_method') : '';
            $pjp->image = $imagePath ? $imagePath : '';
            $pjp->firm_id = $request->get('firm_id');
            // $pjp->schedule_date = $schedule_data;
            if ($pjp->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Pjb info save successuflly',
                    'data' => PjbReport::latest()->get(),
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'error' => 'unable to create pjb report',
                ], 401);
            }
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }
    public function pjp()
    {
        try {
            // Fetch all PjbReports
              $pjp = PjbReport::where('is_active',1)
                ->where('company_id',\Auth::user()->company_id)
                  ->latest()
                ->get();
             $dealerDetails = [];
            // Iterate over each PjbReport
            foreach ($pjp as $dealer) {
                // Determine the type of dealer
                if ($dealer->category == 'Dealer') {
                    $client = Dealer::where('id', $dealer->firm_id)->first();
                } else if ($dealer->category == 'SubDealer') {
                    $client = SubDealer::where('id', $dealer->firm_id)->first();
                } else {
                    $client = Client::where('id', $dealer->firm_id)->first();
                }
                $user = User::where('empCode',$dealer->emp_code)->first();
                $dealerDetails[] = [
                    'pjp' => $dealer,
                    'user' => $user,
                    'client'=>$client,
                ];
            }

            // Check if any data is found
            if ($dealerDetails) {
                return response()->json([
                    'status' => true,
                    'message' => 'fetch pjb Data',
                    'data' => $dealerDetails,
                ], 200);
            }

            return response()->json([
                'status' => false,
                'message' => 'No data found',
                'errors' => 'No data found'
            ], 404);
        } catch (Exception $ex) {
            // Catch any exceptions and return error response
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }
public function client(Request $request)
    {
        try {
             $clients = Client::where('is_active', 1)
    ->whereIn('categoery_id', [5, 6, 7, 8, 9, 10])
    ->where('company_id',\Auth::user()->company_id)
    ->latest()
    ->get();

// Assuming there's a column named 'emp_code' in the clients table
// Loop through each client and assign a user to it
foreach ($clients as $client) {
    // Fetching the user based on the client's 'emp_code'
    $user = User::where('empCode', $client->emp_code)->first();

    // Assigning the user to the client
    $client->user = $user;
}
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

    public function client_add(Request $request)
    {
         try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'clientName' => 'required',
                    'address' => 'required',
                    'concered_person' => 'required',
                      'phone' => 'required|numeric',
                     'empCode' => 'required',
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
            $client->clientName = $request->get('clientName');
            $client->address = $request->get('address');
            $client->concered_person = $request->get('concered_person');
            $client->phone = $request->get('phone');
            $client->email = $request->get('email');
            $client->emp_code =  $request->get('empCode');
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
    public function client_update(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'clientName' => 'required',
                    'address' => 'required',
                    'concered_person' => 'required',
                      'phone' => 'required|numeric',
                     'empCode' => 'required',
                    'id' => 'required',
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
            $client->emp_code =  $request->get('empCode');
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
    
    
    public function sub_dealer()
    {
        try {
                  $dealer = SubDealer::with('user')
                   ->where('company_id',\Auth::user()->company_id)
                     ->where('sub_dealear_active', 1)
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
            $dealer->emp_code = $request->empCode ? $request->empCode:$dealer->emp_code;
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
    
    public function dealers_details($id)
    {
        try {
            $dealer = Dealer::where('dealear_active', 1)
                ->where('role_id', 3)
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
     public function dealers()
{
    
    try {
             $dealer = Dealer::with('user')
             ->where('dealear_active', 1)
             ->where('company_id',\Auth::user()->company_id)
             ->get();
       
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
              $dealer->emp_code = $request->empCode ?$request->empCode :$dealer->emp_code;
            // $dealer->role_id = $request->role_id;
            // $dealer->email = $request->email; // Use null coalescing operator for default value
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
public function visits()
    {
       
        try {
            $vists =   Visits::where('is_active',1)
               ->where('company_id',\Auth::user()->company_id)
                ->latest()->get();
            if ($vists) {
                $dealerDetails = [];
                // Iterate over each PjbReport
                foreach ($vists as $dealer) {
                    // Determine the type of dealer
                    if ($dealer->category == 'Dealer') {
                        $user = Dealer::where('id', $dealer->firm_id)->first();
                    } else if ($dealer->category == 'SubDealer') {
                        $user = SubDealer::where('id', $dealer->firm_id)->first();
                    } else {
                        $user = Client::where('id', $dealer->firm_id)->first();
                    }
                    
                    $dealer['total_order'] = Visits::where('emp_code',$dealer->emp_code)
                    ->where('is_active',1)
                        ->count('order_recipt');
                    $dealer['order_yes'] = Visits::where('emp_code',$dealer->emp_code)
                    ->where('is_active',1)
                        ->where('order_recipt','yes')->count('order_recipt');
                    $dealer['order_per'] = (  $dealer['order_yes'] / $dealer['total_order']) * 100;
                     $dealer['user_name'] = User::select('name','empCode')->where('empCode',$dealer->emp_code)->first();
                    $image = $dealer->image;
                    $image1 = $dealer->image1;
                    $image2 = $dealer->image2;
                    $dealerDetails[] = [
                        'visits' => $dealer,
                        'dealer' => $user,
                        'image' => asset('storage/' . $image),
                        'image1' => asset('storage/' . $image1),
                        'image2' => asset('storage/' . $image2),
                    ];
                }
                return response()->json([
                    'status' => true,
                    'message' => 'fetch visits info  successuflly',
                    'data' => $dealerDetails,
                ], 200);
            }
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }

     public function visits_add(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'category' => 'required',
                    'concerned_person' => 'required',
                    'payment_status' => 'required',
                    'firm_name' => 'required',
                    'distance' => 'required',
                    'order_recipt' => 'required',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validateUser->errors()->first(),
                ], 401);
            }

            // $delete_pjb = PjbReport::where('id', $request->id)->delete();
            // Handle image upload if needed
            $imagePath = $request->file('image') ? $request->file('image')->store('visits', 'public') : '';
            $imagePath1 = $request->file('image1') ? $request->file('image1')->store('visits', 'public') : '';
            $imagePath2 = $request->file('image2') ? $request->file('image2')->store('visits', 'public') : '';

            $visit = new Visits();
            $visit->category = $request->category;
            $visit->concerned_person = $request->concerned_person;
            $visit->firm_name = $request->firm_name;
            $visit->emp_code = Auth::user()->empCode;
            $visit->remarks = $request->remarks ?? '';
            $visit->payment_status = $request->payment_status;
            $visit->payment_method = $request->payment_method ?? '';
            $visit->amount = $request->amount ?? '';
            $visit->billNo = $request->billNo ?? '';
            $visit->chequeNo = $request->chequeNo ?? '';
            $visit->bankName = $request->bankName ?? '';
            $visit->transferId = $request->transferId ?? '';
            $visit->image = $imagePath ?? '';
            $visit->image1 = $imagePath1 ?? '';
            $visit->image2 = $imagePath2 ?? '';
            $visit->distance = $request->get('distance');
            $visit->firm_id =  $request->get('firm_id');
            $visit->visit_latitude = $request->get('visit_latitude');
            $visit->visit_longitude = $request->get('visit_longitude');
            $visit->type = 'unplanned';
            // $visit->payment_date = $request->get('payment_date') ? $request->get('payment_date'):'';
            $visit->order_recipt = $request->order_recipt;
            if ($visit->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Visit info saved successfully',
                    'data' => $visit,
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to save visit info',
                ], 500);
            }
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }
public function geoTage(){
    try {
        $data =  GeoTag::where('is_active',1)->get();
        $users = User::where('is_active',1)->get(); // Fetch all users from the User model

        foreach ($data as $record) {
            // Loop through each record in $data
            foreach ($users as $user) {
                // Loop through each user to find matching user_id
                if ($record->empCode == $user->empCode) {
                    // If user_id matches, retrieve user's name and assign it to the record
                    $record->user_name = $user->name;
                     // Exit the loop since we found a match
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'fetch data',
            'data' => $data,
        ], 200);
    } catch (\Exception $ex) {
        return response()->json([
            'status' => false,
            'message' => $ex->getMessage()
        ], 500);
    }
}

public function report()
{
    try {
        $currentYear = date('Y');
        $months = array_map(function ($month) {
            return sprintf("%02d", $month);
        }, range(1, 12)); // Create an array from January (01) to December (12)

        $monthlyData = [];

        foreach ($months as $c_month) {
            $vists = Visits::where('is_active', 1)
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $c_month)
                ->latest()
                ->get();

            $dealerDetails = $this->getDealerDetails($vists);

            $monthlyData[] = [
                'month' => $c_month,
                'visits' => $dealerDetails
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'Fetch visits info successfully',
            'data' => $monthlyData,
        ], 200);

    } catch (Exception $ex) {
        return response()->json([
            'status' => false,
            'message' => $ex->getMessage()
        ], 500);
    }
}

private function getDealerDetails($visits)
{
    $dealerDetails = [];

    foreach ($visits as $dealer) {
        $user = $this->getUserByCategory($dealer->category, $dealer->firm_id);

        $totalOrderCount = Visits::where('emp_code', $dealer->emp_code)
            ->where('is_active', 1)
            ->count('order_recipt');

        $orderYesCount = Visits::where('emp_code', $dealer->emp_code)
            ->where('is_active', 1)
            ->where('order_recipt', 'yes')
            ->count('order_recipt');

        $orderPercentage = $totalOrderCount ? ($orderYesCount / $totalOrderCount) * 100 : 0;

        $userName = User::select('name', 'empCode')
            ->where('empCode', $dealer->emp_code)
            ->first();

        $dealerDetails[] = [
            'visits' => $dealer,
            'dealer' => $user,
            'total_order' => $totalOrderCount,
            'order_yes' => $orderYesCount,
            'order_per' => $orderPercentage,
            'user_name' => $userName,
            'image' => asset('storage/' . $dealer->image),
            'image1' => asset('storage/' . $dealer->image1),
            'image2' => asset('storage/' . $dealer->image2),
        ];
    }

    return $dealerDetails;
}

private function getUserByCategory($category, $firm_id)
{
    if ($category == 'Dealer') {
        return Dealer::where('id', $firm_id)->first();
    } elseif ($category == 'SubDealer') {
        return SubDealer::where('id', $firm_id)->first();
    } else {
        return Client::where('id', $firm_id)->first();
    }
}
public function user_compnay(Request $request)
{
    try {
        // Retrieve data from the database
        
        $dealers = Dealer::with('user')->where('company_id',\Auth::user()->company_id)->get()->map(function($dealer) {
            return [
                'id' => $dealer->id,
                'uid' => $dealer->uid,
                 'empname' => $dealer->user->name,
                'name' => $dealer->dealerName,  // Ensure name is included
                'categoery_id' =>'0',
                   'emp_code' => $dealer->emp_code,
                'company_id' => $dealer->company_id,
               
            ];
        });
      $clients = Client::with('user')->where('company_id', \Auth::user()->company_id)->get()->map(function($client) {
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
       $sub_dealers = SubDealer::with('user')->where('company_id', \Auth::user()->company_id)->get()->map(function($subDealer) {
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
