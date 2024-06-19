<?php

namespace App\Http\Controllers\Admin\Dealer;

use Exception;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use App\Models\Dealer;
use Illuminate\Http\Request;

class DealerController extends Controller
{
    public function index()
    {
        return view('admin.dealer.index');
    }
    public function add()
    {
        return view('admin.dealer.add');
    }
    public function create(Request $request)
    { 
            try {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'name' => ['required'],
                        'address' => ['required'],
                        'longitude' => ['required'],
                        'Attitude' => ['required'],
                        'concered_name' => ['required'],
                        'code' => ['required', 'string'],
                        'number' => ['required', 'string'],
                        'brands' => ['required', 'string'],
                    ]
                );

                if ($validator->fails()) {
                    return Redirect::back()->withInput($request->input())->withErrors($validator);
                }
                $data = new Dealer();
                $data->name = $request->get('name');
                $data->address = $request->get('address');
                $data->longitude = $request->get('longitude');
                $data->Attitude = $request->get('Attitude');
                $data->concered_name = $request->get('concered_name');
                $data->code = $request->get('code');
                $data->number = $request->get('number');
                $data->brands = $request->get('brands');
                if ($data->save()) {
                    return Redirect::back()->withSuccess('Dealer save Successfully');
                }
            } catch (Exception $ex) {
                return redirect()->back()->withErrors(['error' => $ex->getMessage()]);
            }
        
    }
}
