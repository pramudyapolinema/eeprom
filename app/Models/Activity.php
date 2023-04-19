<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'title',
        'description',
    ];

    public function author() {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function medias() {
        return $this->hasMany(ActivityMedia::class, 'activity_id', 'id');
    }
}
