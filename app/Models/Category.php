<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    use HasFactory;
    protected $fillable = ['event_id', 'name', 'capacity'];

    public function event()
    {
        return $this->belongsTo(NextEvent::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'event_team', 'category_id', 'team_id')
            ->withPivot('event_id', 'status')
            ->withTimestamps();
    }


    public function hasCapacity()
    {
        $assignedTeamsCount = $this->teams()->count();

        return $assignedTeamsCount < $this->capacity;
    }
}
