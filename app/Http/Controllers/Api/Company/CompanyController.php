<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
class CompanyController extends Controller
{
    public function changePassword(Request $request)
    {
        // return $request->all();
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'oldPassword' => 'required',
                    'password' => 'required|confirmed',
                ]
            );
            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validateUser->errors()->first(),
                    'errors' => $validateUser->errors()->first(),
                ], 401);
            }
            $user = User::where('id', Auth::user()->id)
                ->where('role_id', 2)->first();
            // $new_password =  Hash::make($request->old_password);
            if (Hash::check($request->oldPassword, $user->password)) {
                $user->update([
                    'password' => Hash::make($request->oldPassword)
                ]);
                return response()->json([
                    'status' => true,
                    'message' => ' password successfully updated',
                ], 200);
            }
            return response()->json([
                'status' => false,
                'message' => "Old Password didn't match",
            ], 400);
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }
    public function index()
    {
        try{
      $company = Company::all();
      if($company)
      {
           return response()->json([
                    'status' => true,
                    'data' => $company,
                ], 200);
      }
        }catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
        
    }
}
