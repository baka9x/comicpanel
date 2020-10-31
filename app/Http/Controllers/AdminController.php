<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Tag;
use App\Models\Category;
use App\Models\Role;
use App\Models\Blog;
use App\Models\Blogcategory;
use App\Models\Blogtag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{


    //---LOGIN SYSTEM------//
    public function index(Request $request){
        if (!Auth::check() && $request->path() != 'login') {
            return redirect('/login');
        }

        if (!Auth::check() && $request->path() == 'login') {

            return view('welcome');
        }
        // you are already logged in... so check for if you are an admin user..
        $user = Auth::user();
        if ($user->role->isAdmin==0) {
            return redirect('/login');
        }
        if ($request->path() == 'login') {
            return redirect('/');
        }
        //return response()->json($user);
        //return view('welcome');
        return $this->checkForPermission($user, $request);
    }

    public function checkForPermission($user, $request)
    {
        $permission = json_decode($user->role->permission);

        $hasPermission = false;
        if (!$permission) {
            return view('welcome');
        }
        foreach ($permission as $p) {
            if ($p->name == $request->path()) {
                if ($p->read || $p->write || $p->delete || $p->update) { 
                    $hasPermission = true;
                }
            }
        }
        if ($hasPermission) {
            return view('welcome');
        }
        return view('notfound');
    }


    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }


    //----USER FUNCIONS----//

    public function addUser(Request $request)
    {
        // validate request
        $this->validate($request, [
            'fullName' => 'required',
            'email' => 'bail|required|email|unique:users',
            'password' => 'bail|required|min:6',
            'role_id' => 'required',
        ]);
        $password = bcrypt($request->password);
        $user = User::create([
            'fullName' => $request->fullName,
            'email' => $request->email,
            'password' => $password,
            'role_id' => $request->role_id,
        ]);
        return $user;
    }
    public function editUser(Request $request){
        //Validate request
        $this->validate($request,[
            'fullName' => 'required',
            'email' => "bail|required|email|unique:users,email,$request->id",//Change new email or stays its
            'role_id' => 'required',
            'id'     => 'required',

        ]);
        return User::where('id', $request->id)->update([
            'fullName' => $request->fullName,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ]);
    }

    public function changePasswordUser(Request $request){
        //Validate request
        $this->validate($request,[
            'password' => 'bail|required|min:6',
            'rePassword' => 'required',
            'id'     => 'required',
        ]);
        $password = bcrypt($request->password);
        return User::where('id', $request->id)->update([
            'password' => $password,
        ]);
    }
    public function deleteUser(Request $request){
        //Validate request
        $this->validate($request,[
            'id'     => 'required',
        ]);
        return User::where(
                ['id', $request->id],
                ['role_id', '!=', 1],
                ['role_id', '!=', 2],
            )->delete();
    }

    public function getUsers(){
        return User::orderBy('id', 'DESC')->get();
    }


    public function adminLogin(Request $request)
    {
        // validate request
        $this->validate($request, [
            'email' => 'bail|required|email',
            'password' => 'bail|required|min:6',
        ]);
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            if ($user->role->isAdmin == 0) {
                Auth::logout();
                return response()->json([
                    'msg' => 'Incorrect login details',
                ], 401);
            }
            return response()->json([
                'msg' => 'You are logged in',
                'user' => $user,
            ]);
        } else {
            return response()->json([
                'msg' => 'Incorrect login details',
            ], 401);
        }
    }

	//----TAG FUNCIONS----//

    public function addTag(Request $request){
    	//Validate request
    	$this->validate($request,[
    		'tagName' => 'required'
    	]);
    	return Tag::create([
    		'tagName' => $request->tagName,
    	]);
    }
    public function editTag(Request $request){
    	//Validate request
    	$this->validate($request,[
    		'tagName' => 'required',
    		'id'	 => 'required',
    	]);
    	return Tag::where('id', $request->id)->update([
    		'tagName' => $request->tagName,
    	]);
    }
    public function deleteTag(Request $request){
    	//Validate request
    	$this->validate($request,[
    		'id'	 => 'required',
    	]);
    	return Tag::where('id', $request->id)->delete();
    }
    public function getTags(Request $request){
    	return Tag::orderBy('id', 'DESC')->get();
    }


    //--CATEGORY FUNCTIONS----//
    public function addCategory(Request $request){
    	//Validate request
    	$this->validate($request,[
    		'categoryName' => 'required',
    		'iconImage' => 'required'
    	]);
    	return Category::create([
    		'categoryName' => $request->categoryName,
    		'iconImage' => $request->iconImage,
    	]);
    }
    public function editCategory(Request $request){
    	//Validate request
    	$this->validate($request,[
    		'categoryName' => 'required',
    		'iconImage' => 'required',
    		'id'	 => 'required',
    	]);
    	return Category::where('id', $request->id)->update([
    		'categoryName' => $request->categoryName,
    		'iconImage' => $request->iconImage,
    	]);
    }
    public function deleteCategory(Request $request){
    	//Validate request
    	$this->validate($request,[
    		'id'	 => 'required',
    	]);
    	$filename = $request->iconImage;
    	$this->deleteFileFromServer($filename);
    	return Category::where('id', $request->id)->delete();
    }
    public function getCategories(Request $request){
    	return Category::orderBy('id', 'DESC')->get();
    }

    //---UPLOAD FUNCTIONS----//

    public function upload(Request $request){
    	$this->validate($request,[
    		'file'	 => 'required|mimes:jpeg,bmp,png',
    	]);
    	$picName = time().'.'.$request->file->extension();
    	$request->file->move(public_path('uploads'), $picName);
    	return $picName;
    }
    public function deleteImage(Request $request){
    	$filename = $request->imageName;
    	$this->deleteFileFromServer($filename);
    	return 'done';
    }
    public function deleteFileFromServer($filename){
    	$filePath = public_path().'/uploads/'.$filename;
    	if(file_exists($filePath)){
    		@unlink($filePath);
    	}
    	return;
    }
    //Upload image from Editor.js
    public function uploadEditorImage(Request $request){
        $this->validate($request,[
            'image'   => 'required|mimes:jpeg,bmp,png',
        ]);
        $picName = time().'.'.$request->image->extension();
        $request->image->move(public_path('uploads'), $picName);
        return response()->json([
            'success' => 1,
            'file' => [
                'url' => 'http://127.0.0.1:8000/uploads/'.$picName,
            ],
        ]);    
        return $picName;
    }




    //----ROLE FUNCIONS----//

    public function addRole(Request $request){
        //Validate request
        $this->validate($request,[
            'roleName' => 'required',

        ]);
        return Role::create([
            'roleName' => $request->roleName,
        ]);
    }
    public function editRole(Request $request){
        //Validate request
        $this->validate($request,[
            'roleName' => 'required',
            'id'     => 'required',
        ]);
        return Role::where('id', $request->id)->update([
            'roleName' => $request->roleName,
        ]);
    }
    public function deleteRole(Request $request){
        //Validate request
        $this->validate($request,[
            'id'     => 'required',
        ]);
        return Role::where('id', $request->id)->delete();
    }
    public function getRoles(Request $request){
        return Role::all();
    }

    public function assignRoles(Request $request)
    {
        $this->validate($request, [
            'permission' => 'required',
            'id' => 'required',
        ]);
        return Role::where('id', $request->id)->update([
            'permission' => $request->permission,
        ]);
    }


    //----BLOG FUNCIONS----//
    public function slug(){
        $title = 'This is a nice title changed';
        return Blog::create([
            'title' => $title,
            'post' => 'some post',
            'user_id' => 1,
            'metaDescription' => 'aead',
            'post_excerpt' => 'aead', 
        ]);
        return $title;
    }

   

    public function createBlog(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'post' => 'required',
            'post_excerpt' => 'required',
            'metaDescription' => 'required',
            'jsonData' => 'required',
            'category_id' => 'required',
            'tag_id' => 'required',
        ]);
        $categories = $request->category_id;
        $tags = $request->tag_id;

        $blogCategories = [];
        $blogTags = [];
        DB::beginTransaction();
        try {
            $blog = Blog::create([
                'title' => $request->title,
                'slug' => $request->title,
                'post' => $request->post,
                'post_excerpt' => $request->post_excerpt,
                'user_id' => Auth::user()->id,
                'metaDescription' => $request->metaDescription,
                'jsonData' => $request->jsonData,
            ]);
            // insert blog categories
            foreach ($categories as $c) {
                array_push($blogCategories, ['category_id' => $c, 'blog_id' => $blog->id]);
            }
            Blogcategory::insert($blogCategories);
            // insert blog tags
            foreach ($tags as $t) {
                array_push($blogTags, ['tag_id' => $t, 'blog_id' => $blog->id]);
            }
            Blogtag::insert($blogTags);
            DB::commit();
            return 'done';
        } catch (\Throwable $th) {
            DB::rollback();
            return 'not done';
        }
    }

    // update blog
    public function updateBlog(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'post' => 'required',
            'post_excerpt' => 'required',
            'metaDescription' => 'required',
            'jsonData' => 'required',
            'category_id' => 'required',
            'tag_id' => 'required',
        ]);
        $categories = $request->category_id;
        $tags = $request->tag_id;
        $blogCategories = [];
        $blogTags = [];

        DB::beginTransaction();
        try {
            $blog = Blog::where('id', $id)->update([
                'title' => $request->title,
                'slug' => $request->title,
                'post' => $request->post,
                'post_excerpt' => $request->post_excerpt,
                'user_id' => Auth::user()->id,
                'metaDescription' => $request->metaDescription,
                'jsonData' => $request->jsonData,
            ]);


            // insert blog categories
            foreach ($categories as $c) {
                array_push($blogCategories, ['category_id' => $c, 'blog_id' => $id]);
            }
            // delete all previous categories
            Blogcategory::where('blog_id', $id)->delete();
            Blogcategory::insert($blogCategories);
            // insert blog tags
            foreach ($tags as $t) {
                array_push($blogTags, ['tag_id' => $t, 'blog_id' => $id]);
            }
            Blogtag::where('blog_id', $id)->delete();
            Blogtag::insert($blogTags);
            DB::commit();
            return 'done';
        } catch (\Throwable $e) {

            DB::rollback();
            return 'not done';
        }
    }

    public function blogdata()
    {
        return Blog::with(['tag', 'cat'])->orderBy('id', 'desc')->get();
    }
    public function deleteBlog(Request $request)
    {
        try{
           Blog::where('id', $request->id)->delete();
           Blogcategory::where('blog_id', $request->id)->delete();
           Blogtag::where('blog_id', $request->id)->delete();
           return 'done';
        }catch (\Throwable $e) {
            return 'not done';
        }
    }
    public function singleBlogItem(Request $request, $id){
        return Blog::with(['tag', 'cat'])->where('id', $id)->first();
    }
}
