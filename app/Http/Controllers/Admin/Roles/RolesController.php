<?php

namespace App\Http\Controllers\Admin\Roles;
use Exception;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
class RolesController extends Controller
{
    public function add()
    {
        return view('admin.roles.add');
    }
    public function create(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'role' => ['required'],
                ]
            );

            if ($validator->fails()) {
                return Redirect::back()->withInput($request->input())->withErrors($validator);
            }
            $data = new Role();
            $data->name = $request->get('role');
            if ($data->save()) {
                return Redirect::back()->withSuccess('Roles save Successfully');
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(['error' => $ex->getMessage()]);
        }
    }
}
