<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use App\Models\PjbReport;
use App\Models\Dealer;
use App\Models\SubDealer;
use App\Models\Client;
use App\Models\Visits;
use App\Models\GeoTag;
use App\Models\User;
use App\Models\UserTrack;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PjpController extends Controller
{
    public function index()
    {
        try {
            // Fetch all PjbReports
            if (Auth::user()->role_id == 1) {
                $pjp = PjbReport::where('is_active', 1)
                    ->latest()
                    ->get();
            } else {
                $pjp = PjbReport::where('emp_code', Auth::user()->empCode)
                    ->where('is_active', 1)
                    ->latest()
                    ->get();
            }

            $dealerDetails = [];
            // Iterate over each PjbReport
            foreach ($pjp as $dealer) {
                // Determine the type of dealer
                if ($dealer->category == 'Dealer') {
                    $user = Dealer::where('id', $dealer->firm_id)->first();
                    $dealer['concered_person'] = $user->concered_person;
                } else if ($dealer->category == 'SubDealer') {
                    $user = SubDealer::where('id', $dealer->firm_id)->first();
                    $dealer['concered_person'] = $user->concered_person;
                } else {
                    $user = Client::where('id', $dealer->firm_id)->first();
                    $dealer['concered_person'] = $user->concered_person;
                }
                $dealerDetails[] = [
                    'pjp' => $dealer,
                    'dealer' => $user
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

    public function add(Request $request)
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
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validateUser->errors()->first(),
                ], 401);
            }

            $imagePath = $request->file('image') ? $request->file('image')->store('images') : 'pjp';
            $pjp = new PjbReport();
             $pjp->u_id = 'p_' . Auth::user()->empCode . date('y-m-d:H:i:s');
            $pjp->category = $request->get('category');
            $pjp->concerned_person = $request->get('concerned_person');
            $pjp->date = $request->get('date');
            $pjp->emp_code = \Auth::user()->empCode;
            $pjp->firm_name = $request->get('firm_name');
            $pjp->server_time = $request->get('server_time');
            $pjp->firm_name = $request->get('firm_name');
            // $pjp->type = $request->get('type') ? $request->get('type') : '';
            $pjp->remarks = $request->get('remarks') ? $request->get('remarks') : 'null';
            $pjp->payment_status = $request->get('payment_status') ? $request->get('payment_status') : '';
            // $pjp->payment_method = $request->get('payment_mathod') ? $request->get('payment_mathod') : '';
            $pjp->amount = $request->get('amount') ? $request->get('amount') : '';
            // $pjp->billNo = $request->get('billNo') ? $request->get('billNo') : '';
            // $pjp->chequeNo = $request->get('chequeNo') ? $request->get('chequeNo') : '';
            // $pjp->bankName = $request->get('bankName') ? $request->get('bankName') : '';
            // $pjp->transferId = $request->get('transferId') ? $request->get('transferId') : '';
            // $pjp->payment_method = $request->get('payment_method') ? $request->get('payment_method') : '';
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
    public function edit($id)
    {
        try {
            $dealer = PjbReport::find($id);
            if ($dealer) {
                return response()->json([
                    'status' => true,
                    'message' => 'fetch pjb Data',
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
    public function update(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'id' => 'required',
                    'category' => 'required',
                    'concerned_person' => 'required',
                    // 'date' => 'required',
                    'firm_id' => 'required',
                    'schedule_date' => 'required',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validateUser->errors()->first(),
                ], 401);
            }
            $imagePath = $request->file('image') ? $request->file('image')->store('images') : '';
            $new_data = $request->get('schedule_date');
            $pjp =  PjbReport::find($request->get('id'));

            // Check if schedule_date exists and is not empty
            if ($pjp->schedule_date) {
                // Split the existing schedule_date string into an array
                $scheduleDates = explode(',', $pjp->schedule_date);

                // Check if the new date already exists in the existing schedule dates
                if (in_array($new_data, $scheduleDates)) {
                } else {
                    $pjp->schedule_date = $pjp->schedule_date . ',' . $new_data;
                }
            } else {
                if ($new_data) {
                    $pjp->schedule_date = $new_data;
                } else {
                    $pjp->schedule_date = $pjp->schedule_date;
                }
            }

            $pjp->category = $request->get('category');
            $pjp->concerned_person = $request->get('concerned_person');
            // $pjp->date = $request->get('date');
            $pjp->remarks = $request->get('remarks');
            $pjp->firm_name = $request->get('firm_name') ? $request->get('firm_name') : '';
            $pjp->firm_id = $request->get('firm_id');
            // Update other fields similarly...

            if ($pjp->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Pjb info saved successfully',
                    'data' => $pjp,
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to save Pjb info',
                ], 500);
            }
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }


    public function convert_pjp(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'id' => 'required',
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

            $delete_pjb = PjbReport::where('id', $request->id)->delete();

            if ($delete_pjb) {
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
                // $visit->payment_method = $request->payment_method ?? '';
                $visit->amount = $request->amount ?? '';
                // $visit->billNo = $request->billNo ?? '';
                // $visit->chequeNo = $request->chequeNo ?? '';
                $visit->order_recipt = $request->order_recipt;
                // $visit->transferId = $request->transferId ?? '';
                $visit->image = $imagePath ? $imagePath : $visit->image;
                $visit->image1 = $imagePath1 ? $imagePath1 : $visit->image1;
                $visit->image2 = $imagePath2 ? $imagePath2 : $visit->image2;
                $visit->distance = $request->get('distance');
                $visit->visit_latitude = $request->get('visit_latitude');
                $visit->visit_longitude = $request->get('visit_longitude');
                $visit->firm_id =  $request->get('firm_id');
                // $visit->payment_date = $request->get('payment_date') ? $request->get('payment_date'):'';
                $visit->type = 'Planned';
                $visit->company_id =\Auth::user()->company_id;
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
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to delete PjbReport record',
                ], 500);
            }
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }

    public function visits()
    {
        try {
            $vists =   Visits::where('emp_code', Auth::user()->empCode)
             ->where('company_id',\Auth::user()->company_id)
                ->where('is_active', 1)
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
            $visit->u_id = 'C_' . $request->category . date('y-m-d:H:i:s');
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
              $visit->company_id = \Auth::user()->company_id;
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


    public function dashboard()
    {

        $months = array_map(function ($month) {
            return sprintf("%02d", $month);
        }, range(1, 12));

        $data = [];
        $currentYear = date('Y');

        foreach ($months as $key => $c_month) {
            if (Auth::user()->role_id == 1) {
                $exist = Visits::whereYear('created_at', $currentYear)
                    ->where('is_active', 1)
                    ->whereMonth('created_at', $c_month)
                    ->count();
            } else {
                $exist = Visits::where('emp_code', Auth::user()->empCode)
                    ->where('is_active', 1)
                    ->whereYear('created_at', $currentYear)
                    ->whereMonth('created_at', $c_month)
                    ->count();
            }

            $monthName = date('F', mktime(0, 0, 0, $c_month, 1));
            $data[$monthName] = $exist;
            // if ($exist) {
            //     // Only add to the result if there is data for the month
            //     $data[$monthName] = $exist;
            // }
        }

        return $data;
    }
    public function save_geoTage(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'empCode' => 'required',
                    'longitude' => 'required',
                    'latitude' => 'required',
                    'server_time' => 'required',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validateUser->errors()->first(),
                ], 401);
            }
            $geo = GeoTag::where('empCode', $request->get('empCode'))->first();

            if ($geo) {

                $geo->longitude = $request->get('longitude');
                $geo->latitude = $request->get('latitude');
                $geo->server_time = $request->get('server_time');

                $geo->save();
            } else {
                // Insert a new record
                $data = new GeoTag();
                $data->empCode = $request->get('empCode');
                $data->longitude = $request->get('longitude');
                $data->latitude = $request->get('latitude');
                $data->server_time = $request->get('server_time');
                $data->save();
            }
            return response()->json([
                'status' => false,
                'message' => 'data save successfully',
                'data' => GeoTag::where('empCode', $request->get('empCode'))->get(),
            ], 200);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }
    public function user_trace_add(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'empCode' => 'required',
                    'is_tracking_active' => 'required',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validateUser->errors()->first(),
                ], 401);
            }

            // Check if the user track record with the given empCode exists
            $user = User::where('empCode', $request->get('empCode'))->first();

            if ($user) {

                $user->is_tracking_active = $request->get('is_tracking_active');
                if ($user->save()) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Data saved successfully',
                        'data' => User::all(), // Return all user track records after the operation
                    ], 200);
                }
            }
            else
            {
                 return response()->json([
                        'status' => true,
                        'message' => 'not found',
                    ], 404);
            }
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }

    public function user_trace()
    {
        try {
            // Retrieve the user's track data along with their name
            if (\Auth::user()->role_id == 1) {
                $userTrack = User::with('user') // Load the relationship with the user
                    ->get();
            } else {
                $userTrack = User::where('empCode', \Auth::user()->empCode)
                    ->get();
            }


            return response()->json([
                'status' => true,
                'message' => 'User track data retrieved successfully',
                'data' => $userTrack,
            ], 200);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }
    public function user_info()
    {
        try {
            // Retrieve the user's track data along with their name
           
                $user = User::where('empCode', \Auth::user()->empCode)
                    ->first();
        


            return response()->json([
                'status' => true,
                'message' => 'User  data retrieved successfully',
                'data' => $user,
            ], 200);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }
   
}
