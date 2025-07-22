<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carousel extends Model
{
    use HasFactory;

    protected $fillable = ['image_url', 'tag', 'title'];



    protected $appends = ['updated_timestamp'];

    public function getUpdatedTimestampAttribute()
    {
        return $this->updated_at ? $this->updated_at->timestamp : null;
    }
}
