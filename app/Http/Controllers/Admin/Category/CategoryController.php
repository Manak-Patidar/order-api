<?php

namespace App\Http\Controllers\Admin\Category;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categoery;
use App\Models\Category;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function add()
    {
        return view('admin.category.add');
    }
    public function create(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'category' => ['required'],
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()->first(),
                ], 402);
            }
            $data = new Category();
            $data->name = $request->get('category');
            if ($data->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Category save successfully',
                    'data' => $data
                ], 200);
            }
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }
}
