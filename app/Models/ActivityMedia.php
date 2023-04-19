<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'media',
    ];

    public function activity() {
        return $this->belongsTo(Activity::class, 'activity_id', 'id');
    }

    public function getMediaPictAttribute() {
        return asset('storage/tmp/uploads/' . $this->media);
    }
}
