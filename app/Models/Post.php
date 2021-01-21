<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'place',
        'body',
        'source',
        'active',
        'comment',
        'badge'
    ];


    public function author(){
        return $this->belongsTo(Author::class);
    }

    public function files(){
        return $this->hasMany(File::class);
    }

    public function getIsActiveAttribute(){
        return $this->active;
    }
}
