<?php

use App\Http\Controllers\Admin\Category\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Admin\AdminLoginController;
use App\Http\Controllers\Api\Admin\RolesController;
use App\Http\Controllers\Api\Company\CompanyAuthController;
use App\Http\Controllers\Api\Company\CompanyController;
use App\Http\Controllers\Api\Sales\PjpController;
use App\Http\Controllers\Api\Sales\SalesController;
use App\Http\Controllers\Api\Sales\SallerController;
use App\Http\Controllers\Api\Sales\DealerController;
use App\Http\Controllers\Api\Sales\SendOtpController;
use App\Models\Category;

use App\Http\Controllers\Api\Super\SuperAdminController;
use PHPUnit\TextUI\XmlConfiguration\Group;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
//  App Api
Route::post('send-otp', [SendOtpController::class, 'send']);
Route::post('otp-verify', [SendOtpController::class, 'verify']);
Route::post('register', [SendOtpController::class, 'register']);
Route::post('device-info', [SendOtpController::class, 'device_info']);
Route::get('email-report-all', [SendOtpController::class, 'email_report']);
  Route::get('email-report',[SendOtpController::class,'all_user_email']);
Route::post('web-login', [AdminLoginController::class, 'web_login']);
// Route::post('/reset-password', [AuthController::class, 'reset_password']);
Route::post('forget-password', [AdminLoginController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [AdminLoginController::class, 'reset_password']);

Route::prefix('sales')->middleware('auth:sanctum')->group(function () {

    Route::post('update-password', [SendOtpController::class, 'updatePassword']);
    Route::prefix('profile')->group(function () {
        Route::get('/', [SendOtpController::class, 'user_info']);
    });
    Route::prefix('device')->group(function () {
        Route::get('/', [SendOtpController::class, 'device']);
    });
 Route::prefix('other')->group(function () {
        Route::get('/', [SendOtpController::class, 'other']);
    });
    Route::prefix('category')->group(function () {
        Route::get('/', [SalesController::class, 'category']);
    });
    Route::prefix('roles')->group(function () {
        Route::get('/', [SalesController::class, 'roles']);
    });
    Route::prefix('dealer')->group(function () {
      Route::get('/', [SalesController::class, 'dealers']);
        Route::get('details/{id}', [SalesController::class, 'dealers_details']);
        Route::post('update', [SalesController::class, 'dealers_update']);
        Route::post('add', [SalesController::class, 'store']);
        Route::post('customers/add', [SalesController::class, 'customers_add']);
        Route::post('customers/update', [SalesController::class, 'customers_update']);
    });
    Route::prefix('client')->group(function () {
        Route::get('/', [SalesController::class, 'clients']);
          Route::post('add', [SalesController::class, 'clients_add']);
            Route::post('update', [SalesController::class, 'clients_update']);
    });

    Route::prefix('subdealer')->group(function () {
        Route::get('/', [SalesController::class, 'sub_dealer']);
        Route::post('update', [SalesController::class, 'Sub_dealers_update']);
        
    });
    Route::prefix('pjp-report')->group(function () {
        Route::get('/', [PjpController::class, 'index']);
        Route::post('add', [PjpController::class, 'add']);
        Route::get('edit/{id}', [PjpController::class, 'edit']);
        Route::post('update', [PjpController::class, 'update']);
        Route::get('dashboard', [PjpController::class, 'dashboard']);
        Route::post('convert-pjp', [PjpController::class, 'convert_pjp']);
        
    });
    Route::prefix('visits')->group(function () {
    Route::get('/', [PjpController::class, 'visits']);
    Route::post('add', [PjpController::class, 'visits_add']);
});
Route::post('geo-tag',[PjpController::class,'save_geoTage']);
Route::post('user-trace/add',[PjpController::class,'user_trace_add']);
Route::get('user-trace',[PjpController::class,'user_trace']);
Route::get('user-info',[PjpController::class,'user_info']);
});

// Company api
Route::prefix('company')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/', [CompanyAuthController::class, 'login']);
        Route::post('/reset-password', [CompanyAuthController::class, 'reset_password']);
        Route::post('/forgot-password', [CompanyAuthController::class, 'forgot_password']);
    });
    Route::get('/',[CompanyController::class,'index']);
    Route::post('change-password', [CompanyAuthController::class, 'changePassword']);
});


Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::prefix('category')->group(function () {
        Route::get('add', [CategoryController::class, 'create']);
        Route::get('/', [CategoryController::class, 'index']);
    });

    Route::prefix('role')->group(function () {
        Route::get('index', [RolesController::class, 'index']);
        Route::post('add', [RolesController::class, 'add']);
    });
    Route::prefix('user')->group(function () {
          Route::get('/', [AdminController::class, 'user']);
    });
     Route::prefix('pjp')->group(function () {
         Route::get('/', [AdminController::class, 'pjp']);
           Route::post('add', [AdminController::class, 'pjp_add']);
            Route::post('update', [AdminController::class, 'pjp_update']);
     });
     Route::prefix('client')->group(function () {
          Route::get('/', [AdminController::class, 'client']);
           Route::post('add', [AdminController::class, 'client_add']);
              Route::post('update', [AdminController::class, 'client_update']);
     });
        Route::prefix('user')->group(function(){
              Route::get('company', [AdminController::class, 'user_compnay']);
        });
    //   Route::prefix('visits')->group(function () {
    //       Route::get('/', [AdminController::class, 'visits']);
    //  });
      Route::prefix('sub-dealer')->group(function () {
           Route::get('/', [AdminController::class, 'sub_dealer']);
             Route::post('update', [AdminController::class, 'Sub_dealers_update']);
     });
       Route::prefix('dealer')->group(function () {
            Route::get('/', [AdminController::class, 'dealers']);
        Route::get('details/{id}', [AdminController::class, 'dealers_details']);
        Route::post('update', [AdminController::class, 'dealers_update']);
       });
        Route::prefix('visits')->group(function () {
    Route::get('/', [AdminController::class, 'visits']);
    Route::post('add', [AdminController::class, 'visits_add']);
      Route::get('report', [AdminController::class, 'report']);
        });
        Route::get('geo-tage',[AdminController::class,'geoTage']);
});
Route::prefix('super-admin')->middleware('auth:sanctum')->group(function () {
    Route::prefix('dealer')->group(function (){
          Route::get('/', [SuperAdminController::class, 'index']);
         Route::post('add', [SuperAdminController::class, 'add']);
          Route::post('update',[SuperAdminController::class, 'update_dealer']);
          Route::get('{comapny_id}', [SuperAdminController::class, 'dealer_company']);
    });
    Route::prefix('comapny')->group(function (){
         Route::get('/', [SuperAdminController::class, 'company']);
    });
    Route::prefix('sub_dealer')->group(function (){
           Route::get('/', [SuperAdminController::class, 'sub_dealer']);
         Route::post('add', [SuperAdminController::class, 'add_sub_dealer']);
           Route::post('update', [SuperAdminController::class, 'update_sub_dealer']);
            Route::get('{comapny_id}', [SuperAdminController::class, 'sub_dealer_company']);
    });
    Route::prefix('pjp')->group(function (){
           Route::get('/', [SuperAdminController::class, 'pjp_list']);
         Route::get('{comapny_id}', [SuperAdminController::class, 'pjp_company']);
      });
       Route::prefix('visits')->group(function (){
           Route::get('/', [SuperAdminController::class, 'visits_list']);
            Route::get('{comapny_id}', [SuperAdminController::class, 'visits_company']);
      });
       Route::prefix('user')->group(function (){
           Route::get('/', [SuperAdminController::class, 'user']);
           Route::get('{comapny_id}', [SuperAdminController::class, 'user_company']);
      });
        Route::prefix('report')->group(function (){
        Route::get('/', [SuperAdminController::class, 'report']);
           Route::get('list', [SuperAdminController::class, 'report_list']);
        });
         Route::prefix('client')->group(function (){
           Route::get('/', [SuperAdminController::class, 'client']);
           Route::get('{comapny_id}', [SuperAdminController::class, 'client_company']);
      });
       Route::prefix('dashboard')->group(function (){
           Route::get('{comapny_id}', [SuperAdminController::class, 'dashboard']);
      });
      Route::get('all-user/{comapny}',[SuperAdminController::class,'all_user']);
});
