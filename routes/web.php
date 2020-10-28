<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\AdminCheck;

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

Route::prefix('app')->middleware([AdminCheck::class])->group(function () {

	//Add Tag
	Route::post('/create_tag', [AdminController::class, 'addTag']);
	//Edit Tag
	Route::post('/edit_tag', [AdminController::class, 'editTag']);
	//Delete Tag
	Route::post('/delete_tag', [AdminController::class, 'deleteTag']);
	//Get Tags
	Route::get('/get_tags', [AdminController::class, 'getTags']);
	//Add Category
	Route::post('create_category', [AdminController::class, 'addCategory']);
	//Edit Category
	Route::post('/edit_category', [AdminController::class, 'editCategory']);
	//Delete Category
	Route::post('/delete_category', [AdminController::class, 'deleteCategory']);
	//Get Categories
	Route::get('/get_categories', [AdminController::class, 'getCategories']);
	//Get Users
	Route::get('/get_users', [AdminController::class, 'getUsers']);
	//Add User
	Route::post('/create_user', [AdminController::class, 'addUser']);
	//Edit User
	Route::post('/edit_user', [AdminController::class, 'editUser']);
	//Delete User
	Route::post('/delete_user', [AdminController::class, 'deleteUser']);
	//Edit User
	Route::post('/change_password_user', [AdminController::class, 'changePasswordUser']);
	//Admin Login
	Route::post('/admin_login', [AdminController::class, 'adminLogin']);
	//Upload
	Route::post('/upload', [AdminController::class, 'upload']);
	//Delete Image Upload
	Route::post('/delete_image', [AdminController::class, 'deleteImage']);

	//Add Role
	Route::post('/create_role', [AdminController::class, 'addRole']);
	//Edit Role
	Route::post('/edit_role', [AdminController::class, 'editRole']);
	//Delete Role
	Route::post('/delete_role', [AdminController::class, 'deleteRole']);
	//Get Roles
	Route::get('/get_roles', [AdminController::class, 'getRoles']);
	//Assign Role
	Route::post('/assign_roles', [AdminController::class, 'assignRoles']);


});

Route::post('createBlog', [AdminController::class, 'uploadEditorImage']);

Route::get('slug',  [AdminController::class, 'slug']);



//Adminpanel
Route::get('/', [AdminController::class, 'index']);
Route::get('/logout', [AdminController::class, 'logout']);
Route::get('{slug}', [AdminController::class, 'index']);

Route::get('/install', [AdminController::class, 'install']);

// Route::get('/', function () {
//     return view('welcome');
// });
// Route::any('{slug}', function () {
//     return view('welcome');
// });