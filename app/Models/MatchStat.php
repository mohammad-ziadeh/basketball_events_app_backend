<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchStat extends Model
{
    protected $fillable = ['matchup_id', 'player_id', 'points', 'assists', 'fouls'];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function matchup()
    {
        return $this->belongsTo(Matchup::class);
    }
}
