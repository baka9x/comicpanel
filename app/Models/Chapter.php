<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'chapterTitle', 'chapterThumbnail', 'chapterContent', 'comic_id', 'chapterViews'];


    public function comic(){
        return $this->belongsTo('App\Models\Comic', 'comic_id');
    }
}
