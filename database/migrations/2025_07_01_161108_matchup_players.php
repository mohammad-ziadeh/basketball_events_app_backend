<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('matchup_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matchup_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_id')->constrained('players')->onDelete('cascade');
            $table->enum('team_side', ['A', 'B']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matchup_players');
    }
};
