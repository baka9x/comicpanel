<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

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
    return view('welcome');
});

//Add Tag
Route::post('app/create_tag', [AdminController::class, 'addTag']);
//Get Tags
Route::get('/app/get_tags', [AdminController::class, 'getTag']);

Route::any('{slug}', function () {
    return view('welcome');
});