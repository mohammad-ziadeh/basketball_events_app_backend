<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchupPlayer extends Model
{
    protected $fillable = ['matchup_id', 'player_id', 'team_side'];

    public function matchup()
    {
        return $this->belongsTo(Matchup::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
