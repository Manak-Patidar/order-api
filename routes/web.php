<?php

use App\Http\Controllers\Admin\Category\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeofencingController;
use App\Http\Controllers\Admin\Dealer\DealerController;
use App\Http\Controllers\Admin\Roles\RolesController;

/*

|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('geofencing.index');
});
// Route::get('test', [GeofencingController::class , 'index']);
Route::prefix('admin')->group(function () {
    Route::prefix('dealer')->group(function () {
        Route::get('/', [DealerController::class, 'index'])->name('dealer');
        Route::get('add', [DealerController::class, 'add'])->name('dealer.add');    
        Route::post('create', [DealerController::class, 'create'])->name('dealer.create');    
    });
    Route::prefix('category')->group(function(){
        Route::get('add',[CategoryController::class,'add'])->name('category.add');
        Route::post('create',[CategoryController::class,'create'])->name('category.create');
    });
    Route::prefix('roles')->group(function(){
        Route::get('add',[RolesController::class,'add'])->name('roles.add');
        Route::post('create',[RolesController::class,'create'])->name('roles.create');
    });
});
Route::post('/geofencing/check', [GeofencingController::class, 'checkGeofence']);
