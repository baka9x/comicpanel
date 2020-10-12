<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\Category;

class AdminController extends Controller
{

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
}
