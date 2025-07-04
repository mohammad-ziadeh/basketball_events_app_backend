<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $matchup->teamA->name }} vs {{ $matchup->teamB->name }} - Scoreboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bitcount+Grid+Double:wght@100..900&display=swap"
        rel="stylesheet">

    <style>
        
        body {
            background-color: #000;
            color: #fff;
            font-family: 'Arial Black', sans-serif;
            text-align: center;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 95vh;
        }

        .scoreboard {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 80%;
            max-width: 1500px;
        }

        .team {
            flex: 1;
            text-transform: uppercase;
            font-size: 2rem;
        }

        .score-box {
            border: 4px solid #fff;
            display: inline-block;
            font-family: "Bitcount Grid Double", sans-serif;
            font-weight: 300;
            padding: 40px 80px;
            font-size: 6rem;
            margin-top: 20px;
        }

        .timer {
            font-size: 6rem;
            color: red;
            font-weight: 400;
            margin: 0 40px;
            margin-bottom: 10%;
        }

        .fouls {
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 1.5rem;
        }

        .fouls-box {
            border: 3px solid #fff;
            display: inline-block;
            padding: 10px 30px;
            font-family: "Bitcount Grid Double", sans-serif;
            font-weight: 300;
            font-size: 4rem;
            margin-top: 5px;
        }

        h1 {
            font-size: 3rem;
            margin-bottom: 120px;
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    @php
        // Safely calculate fouls for each team based on player team_id
        $teamAFouls = $matchup->stats->filter(function ($stat) use ($matchup) {
            return $stat->player && $stat->player->team_id === $matchup->teamA->id;
        })->sum('fouls');

        $teamBFouls = $matchup->stats->filter(function ($stat) use ($matchup) {
            return $stat->player && $stat->player->team_id === $matchup->teamB->id;
        })->sum('fouls');
    @endphp

    <div class="container">
        <h1>{{ $matchup->event->title }} - Pool B Game 1</h1>

        <div class="scoreboard">
            <div class="team">
                <div>{{ $matchup->teamA->name }}</div>
                <div class="score-box">{{ $matchup->teamA->points }}</div>
                <div class="fouls">FOULS</div>
                <div class="fouls-box">{{ $teamAFouls }}</div>
            </div>

            <div class="timer" id="live-timer">10:00</div>

            <div class="team">
                <div>{{ $matchup->teamB->name }}</div>
                <div class="score-box">{{ $matchup->teamB->points }}</div>
                <div class="fouls">FOULS</div>
                <div class="fouls-box">{{ $teamBFouls }}</div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const totalSeconds = 600;
            const start = new Date("{{ $matchup->start_time?->toIso8601String() ?? '' }}");
            const display = document.getElementById('live-timer');

            if (start && display) {
                function updateClock() {
                    const now = new Date();
                    const elapsed = Math.floor((now - start) / 1000);
                    let remaining = totalSeconds - elapsed;
                    if (remaining < 0) remaining = 0;

                    const mins = String(Math.floor(remaining / 60)).padStart(2, '0');
                    const secs = String(remaining % 60).padStart(2, '0');

                    display.textContent = `${mins}:${secs}`;
                }

                updateClock();
                setInterval(updateClock, 1000);
            }
        });

        setInterval(() => {
            location.reload();
        }, 4000);
    </script>
</body>

</html>
