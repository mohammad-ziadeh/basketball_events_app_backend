<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = ['photo', 'video', 'type', 'event_id'];

    public function event()
    {
        return $this->belongsTo(NextEvent::class);
    }
}
