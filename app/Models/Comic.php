<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Comic extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'content', 'jsonData', 'user_id', 'thumbnail', 'cover', 'artist', 'views'];

    // public function setSlugAttribute($title){
    //     $this->attributes['slug'] = $this->uniqueSlug($title);
    // }
    // private function uniqueSlug($title){
    //     $slug = Str::slug($title, '-');
    //     $count = Blog::where('slug', 'LIKE', "{$slug}%")->count();
    //     $newCount = $count > 0 ? ++$count : '';
    //     return $newCount > 0 ? "$slug-$newCount" : $slug;
    // }

    
    public function cat(){
        return $this->belongsToMany('App\Models\Category', 'comiccategories');
    }

}
