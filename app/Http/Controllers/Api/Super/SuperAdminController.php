<?php

namespace App\Http\Controllers\Api\Super;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dealer;
use App\Models\SubDealer;
use Illuminate\Support\Str;
use App\Models\PjbReport;
use App\Models\Visits;
use App\Models\Client;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Validator;

class SuperAdminController extends Controller
{
    public function index()
    {
        try {
            $dealer = Dealer::all();
            if ($dealer) {
                return response()->json([
                    'status' => true,
                    'data' => $dealer,
                ], 200);
            }
        } catch (\Exception $e) {
            // If an exception occurs, rollback the transaction
            DB::rollback();

            // Log the exception for debugging purposes
            \Log::error('Error adding data: ' . $e->getMessage());

            // Return an error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function add(Request $request)
    {

        try {
            DB::beginTransaction();

            $jsonData = $request->json()->all();
            foreach ($jsonData as $sheet) {
                // $sheetName = $sheet[0]; // Assuming the first item is the sheet name
                $dataRows = $sheet[1]; // Assuming the second item is the array of data rows

                foreach ($dataRows as $data) {
                    if (!empty($data)) {
                        // Process each data row
                        // You can access each field of the row using $data[index]
                        $id = $data[0]; // Example: accessing the 'id' field
                        $uid = $data[1]; // Example: accessing the 'uid' field
                        $dealerName = $data[2]; // Example: accessing the 'dealerName' field
                        $empCode = $data[3];
                        $territory = $data ? $data[4] : '';
                        $email = $data[5]; // Example: accessing the 'email' field
                        $address = $data[6]; 
                        // $longitude = $data ? $data[8] : ''; 
                        // $latitude =  $data ? $data[9] :''; 
                        // Example: accessing the 'address' field
                        // Access other fields in a similar manner
                        $number = $data[12];
                        $concered_person = $data[10];
                        $company_id = $data[16];
                        // Now, you can insert or process this data as needed
                        // For example, you can create a new User model and save it to the database
                        $dealer = new Dealer();
                        $dealer->dealerName = $dealerName;
                        $dealer->uid = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
                        $dealer->emp_code = $empCode;
                        $dealer->email = $email;
                        $dealer->dealer_code = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
                        $dealer->address =  $address;
                        $dealer->concered_person = $concered_person;
                        $dealer->number =  $number;
                        $dealer->dealear_active =  1;
                        $dealer->company_id = $company_id;
                        $dealer->role_id = 3;
                        $dealer->territory = $territory;
                    
                        // Save the user
                        $dealer->save();
                    }
                }
            }
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Data added successfully',
            ], 200);
        } catch (\Exception $e) {
            // If an exception occurs, rollback the transaction
            DB::rollback();

            // Log the exception for debugging purposes
            \Log::error('Error adding data: ' . $e->getMessage());

            // Return an error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
public function dealer_company($comapny)
    {
        try {
            $dealer = Dealer::where('company_id',$comapny)->get();
            if ($dealer) {
                return response()->json([
                    'status' => true,
                    'data' => $dealer,
                ], 200);
            }
        } catch (\Exception $e) {
            // If an exception occurs, rollback the transaction
            DB::rollback();

            // Log the exception for debugging purposes
            \Log::error('Error adding data: ' . $e->getMessage());

            // Return an error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function add_sub_dealer(Request $request)
    {
        try {
            DB::beginTransaction();

            $jsonData = $request->json()->all();

            foreach ($jsonData as $sheet) {
                $dataRows = $sheet[1]; // Assuming the second item is the array of data rows

                foreach ($dataRows as $data) {
                    if (!empty($data)) {
                        $dealer = new SubDealer();
                        $dealer->name = $data[2]; // Accessing the 'name' field
                        $dealer->uid = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
                        $dealer->emp_code = $data[3]; // Accessing the 'emp_code' field
                        $territory = $data ? $data[4] : $data;
                        $dealer->email = $data[5]; // Accessing the 'email' field
                        $dealer->address = $data[6]; // Accessing the 'address' field
                        $dealer->concered_person = $data[10]; // Accessing the 'concered_person' field
                        $dealer->number = $data[12]; // Accessing the 'number' field
                        $dealer->designation = $data ? $data[13] : ''; // Accessing the 'designation' field
                        $dealer->brands = $data[14]; // Accessing the 'brands' field
                        $dealer->sub_dealear_active = 1;
                        $dealer->role_id = 4;
                        $dealer->territory = $dealer;
                        $dealer->sub_dealer_code = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
                        $dealer->save();
                    }
                }
            }
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data added successfully',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();

            \Log::error('Error adding data: ' . $e->getMessage());

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function sub_dealer()
    {
        try {
            $dealer = SubDealer::all();
            if ($dealer) {
                return response()->json([
                    'status' => true,
                    'data' => $dealer,
                ], 200);
            }
        } catch (\Exception $e) {
            // If an exception occurs, rollback the transaction
            DB::rollback();

            // Log the exception for debugging purposes
            \Log::error('Error adding data: ' . $e->getMessage());

            // Return an error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function sub_dealer_company($comapny)
    {
        try {
            $dealer = SubDealer::where('company_id',$comapny)->get();
            if ($dealer) {
                return response()->json([
                    'status' => true,
                    'data' => $dealer,
                ], 200);
            }
        } catch (\Exception $e) {
            // If an exception occurs, rollback the transaction
            DB::rollback();

            // Log the exception for debugging purposes
            \Log::error('Error adding data: ' . $e->getMessage());

            // Return an error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function pjp_list()
    {
        try {
            $pjp = PjbReport::all();
            if ($pjp) {
                return response()->json([
                    'status' => true,
                    'data' => $pjp,
                ], 200);
            }
        } catch (\Exception $e) {
            // If an exception occurs, rollback the transaction
            DB::rollback();

            // Log the exception for debugging purposes
            \Log::error('Error adding data: ' . $e->getMessage());

            // Return an error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    // public function pjp_company($comapny)
    // {
    //     try {
    //         $pjp = PjbReport::where('company_id',$comapny)->get();
    //         if ($pjp) {
    //             return response()->json([
    //                 'status' => true,
    //                 'data' => $pjp,
    //             ], 200);
    //         }
    //     } catch (\Exception $e) {
    //         // If an exception occurs, rollback the transaction
    //         DB::rollback();

    //         // Log the exception for debugging purposes
    //         \Log::error('Error adding data: ' . $e->getMessage());

    //         // Return an error response
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }
    
    public function visits_list()
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
    
  public function pjp_company($company)
{
    try {
        $pjp = PjbReport::where('company_id', $company)->get();
        
        // Initialize the dealerDetails array
        $dealerDetails = [];
        
        foreach ($pjp as $dealer) {
            // Determine the type of dealer
            if ($dealer->category == 'Dealer') {
                $user = Dealer::where('id', $dealer->firm_id)->first();
                $dealer['concered_person'] = $user->concered_person ? $user->concered_person :'';
            } else if ($dealer->category == 'SubDealer') {
                $user = SubDealer::where('id', $dealer->firm_id)->first();
                $dealer['concered_person'] = $user->concered_person ? $user->concered_person :'';
            } else {
                $user = Client::where('id', $dealer->firm_id)->first();
                // $dealer['concered_person'] = $user->concered_person ? $user->concered_person :'';
            }
            
            $users = User::select('name', 'empCode')->where('empCode', $dealer->emp_code)->first();
            
            $dealerDetails[] = [
                'pjp' => $dealer,
                'dealer' => $user,
                'users' => $users,
            ];
        }
        
        if (!empty($dealerDetails)) {
            return response()->json([
                'status' => true,
                'data' => $dealerDetails,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No pjp  found.',
            ], 404);
        }
    } catch (\Exception $e) {
        // If an exception occurs, rollback the transaction
        DB::rollback();

        // Log the exception for debugging purposes
        \Log::error('Error adding data: ' . $e->getMessage());

        // Return an error response
        return response()->json(['error' => $e->getLine()], 500);
    }
}

    public function visits_company($comapny)
    {
       
        try {
            $vists =   Visits::where('is_active',1)
               ->where('company_id',$comapny)
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
    
    // user
    public function user()
    {
        try {
            $user = User::all();
            if ($user) {
                return response()->json([
                    'status' => true,
                    'data' => $user,
                ], 200);
            }
        } catch (\Exception $e) {
            // If an exception occurs, rollback the transaction
            DB::rollback();

            // Log the exception for debugging purposes
            \Log::error('Error adding data: ' . $e->getMessage());

            // Return an error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
public function user_company($comapny)
    {
        try {
            $user = User::where('company_id',$comapny)->get();
            if ($user) {
                return response()->json([
                    'status' => true,
                    'data' => $user,
                ], 200);
            }
        } catch (\Exception $e) {
            // If an exception occurs, rollback the transaction
            DB::rollback();

            // Log the exception for debugging purposes
            \Log::error('Error adding data: ' . $e->getMessage());

            // Return an error response
            return response()->json(['error' => $e->getMessage()], 500);
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
                   ->groupBy('firm_id')
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
                 ->where('order_recipt', 'yes')
                ->count('order_recipt');

            $orderYesCount = Visits::where('emp_code', $dealer->emp_code)
                ->where('is_active', 1)
                ->where('order_recipt', 'yes')
                ->count('order_recipt');
         $orderAmount = Visits::where('emp_code', $dealer->emp_code)
                ->where('is_active', 1)
                // ->where('amount')
                ->sum('amount');
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
                'total_amount' =>$orderAmount,
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
    public function update_dealer(Request $request)
    {
        
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'id' => 'required|exists:dealer,id',
                    'status' => 'required',
                ]
            );
            if ($validateUser->fails()) {
               
                return response()->json([
                    'status' => false,
                    'errors' => $validateUser->errors()->first(),
                ], 402);
            }

            $dealer = Dealer::find($request->get('id'));
            $dealer->dealear_active = $request->get('status');
            $dealer->save();

            return response()->json(['message' => 'Dealer status updated successfully'], 200);
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getLine()
            ], 500);
        }
    }
    public function update_sub_dealer(Request $request)
    {
         try {
        $validateUser = Validator::make(
            $request->all(),
            [
                'id' => 'required|exists:sub_dealer,id',
                'status' => 'required',
            ]
        );
        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validateUser->errors()->first(),
            ], 402);
        }
        $subDealer = SubDealer::find($validated['id']);
        $subDealer->status = $validated['status'];
        $subDealer->save();
         }
         catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getLine()
            ], 500);
        }
    }
    
    public function company()
    {
        
        try{
          $company  = Company::all();
          if($company)
          {
              return response()->json([
                'status' => true,
                'message' => 'Fetch Company info successfully',
                'data' => $company,
            ], 200); 
          }
      }
       catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getLine()
            ], 500);
        }
    }
    public function client_company($comapny)
    {
        try{
          $client  = Client::with('user')->where('company_id',$comapny)->get();
        //   $client->user = $client::where('empCode',$client->emp_code)->first();
          if($client)
          {
              return response()->json([
                'status' => true,
                'message' => 'Fetch client info successfully',
                'data' => $client,
            ], 200); 
          }
      }
       catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getLine()
            ], 500);
        }
    }
  public function report_list()
{
       
    try {
        $entities = [
            ['type' => 'dealer', 'model' => Dealer::paginate(5)],
            ['type' => 'subdealer', 'model' => SubDealer::paginate(5)],
            ['type' => 'client', 'model' => Client::paginate(5)],
        ];
        
        $currentYear = date('Y');
        $months = range(1, 12); // Create an array from January (1) to December (12)

        $monthlyData = [];

        foreach ($entities as $entity) {
            foreach ($entity['model'] as $item) {
                $entityMonthlyData = [
                    'type' => $entity['type'],
                    'id' => $item->id,
                    'name' => $item->name, // Assuming each model has a name attribute
                    'monthly_visits' => []
                ];

                foreach ($months as $c_month) {
                    $visits = Visits::where('is_active', 1)
                        ->whereYear('created_at', $currentYear)
                        ->whereMonth('created_at', $c_month)
                        ->where('firm_id', $item->id)
                        ->get();

                    $entityMonthlyData['monthly_visits'][] = [
                        'month' => $c_month,
                        'visits_count' => $visits->count(), // Count of visits for the month
                        'visits' => $visits // Include the visits data if needed
                    ];
                }

                $monthlyData[] = $entityMonthlyData;
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Fetched visits info successfully',
            'data' => $monthlyData,
        ], 200);
    } catch (\Exception $ex) {
        return response()->json([
            'status' => false,
            'message' => $ex->getMessage()
        ], 500);
    }
}
public function all_user($company)
{
    try {
        // Retrieve data from the database
        
        $dealers = Dealer::with('user')->where('company_id', $company)->get()->map(function($dealer) {
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
      $clients = Client::with('user')->where('company_id', $company)->get()->map(function($client) {
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
       $sub_dealers = SubDealer::with('user')->where('company_id', $company)->get()->map(function($subDealer) {
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
public function dashboard($company)
    {

        $months = array_map(function ($month) {
            return sprintf("%02d", $month);
        }, range(1, 12));

        $data = [];
        $currentYear = date('Y');

        foreach ($months as $key => $c_month) {
           
                $exist = Visits::where('is_active', 1)
                    ->whereYear('created_at', $currentYear)
                    ->whereMonth('created_at', $c_month)
                      ->where('company_id',$company)
                    ->count();
        

            $monthName = date('F', mktime(0, 0, 0, $c_month, 1));
            $data[$monthName] = $exist;
            // if ($exist) {
            //     // Only add to the result if there is data for the month
            //     $data[$monthName] = $exist;
            // }
        }

        return $data;
    }



}
