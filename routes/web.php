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
//Edit Tag
Route::post('/app/edit_tag', [AdminController::class, 'editTag']);
//Delete Tag
Route::post('/app/delete_tag', [AdminController::class, 'deleteTag']);
//Get Tags
Route::get('/app/get_tags', [AdminController::class, 'getTags']);
//Add Category
Route::post('app/create_category', [AdminController::class, 'addCategory']);
//Edit Category
Route::post('/app/edit_category', [AdminController::class, 'editCategory']);
//Delete Category
Route::post('/app/delete_category', [AdminController::class, 'deleteCategory']);
//Get Categories
Route::get('/app/get_categories', [AdminController::class, 'getCategories']);





//Upload
Route::post('/app/upload', [AdminController::class, 'upload']);
//Delete Image Upload
Route::post('/app/delete_image', [AdminController::class, 'deleteImage']);


Route::any('{slug}', function () {
    return view('welcome');
});