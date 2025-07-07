<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'location', 'coach_id', 'points','email', 'phone'];

    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function events()
    {
        return $this->belongsToMany(NextEvent::class, 'event_team', 'team_id', 'event_id')
            ->withPivot('category_id')
            ->withTimestamps();
    }

    public function categoryInEvent($eventId)
    {
        return $this->events()
            ->where('event_id', $eventId)
            ->first()
            ->pivot
            ->category_id ?? null;
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'event_team', 'team_id', 'category_id')
            ->withPivot('event_id')
            ->withTimestamps();
    }
}
