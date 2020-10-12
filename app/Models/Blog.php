<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'title', 'post', 'slug', 'user_id', 'featuredImage', 'metaDescription', 'views', 'post_exerpt'];
}
