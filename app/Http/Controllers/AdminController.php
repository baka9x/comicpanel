<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;

class AdminController extends Controller
{
    public function addTag(Request $request){
    	//Validate request
    	$this->validate($request,[
    		'tagName' => 'required'
    	]);
    	return Tag::create([
    		'tagName' => $request->tagName,
    	]);
    }


    public function getTag(Request $request){
    	return Tag::orderBy('id', 'DESC')->get();
    }
}
