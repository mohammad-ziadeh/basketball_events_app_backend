<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matchup extends Model
{
    protected $fillable = [
        'event_id',
        'team_a_id',
        'category_id',
        'team_b_id',
        'match_time',
        'group_id',
        'location',
        'round',
        'start_time',
        'duration',
        'winner_team_id',
    ];

    protected $casts = [
        'match_time' => 'datetime',
        'start_time' => 'datetime',
        'duration' => 'string',
    ];

    public function teamA()
    {
        return $this->belongsTo(Team::class, 'team_a_id');
    }

    public function teamB()
    {
        return $this->belongsTo(Team::class, 'team_b_id');
    }

    public function event()
    {
        return $this->belongsTo(NextEvent::class, 'event_id');
    }



    public function activePlayers()
    {
        return $this->hasMany(MatchupPlayer::class);
    }

    public function stats()
    {
        return $this->hasMany(MatchStat::class);
    }

    public function winner()
    {
        return $this->belongsTo(Team::class, 'winner_team_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
