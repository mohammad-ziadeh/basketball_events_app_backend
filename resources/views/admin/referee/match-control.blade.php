<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl" style="color: #3b1e54;">Match Control</h2>
        @include('components.breadcrumbs', [
            'breadcrumbs' => [
                'Dashboard' => route('dashboard'),
                'Matchups' => route('matchups.index'),
                'Control' => '#',
            ],
        ])
    </x-slot>

    @include('components.alerts')

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5" style="margin-top: 30px;">

        <div class="text-end mb-3">
            <a href="{{ route('scoreboard.show', $matchup->id) }}" target="_blank" class="btn btn-dark">
                <i class="bi bi-tv"></i> View Public Scoreboard
            </a>
        </div>


        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">{{ $matchup->teamA->name }} <span class="text-warning">VS</span>
                        {{ $matchup->teamB->name }}</h5>
                    <small>{{ $matchup->event->title }} | {{ $matchup->event->event_code }} |
                        {{ $matchup->event->location }}</small>
                </div>
                <span class="badge bg-secondary">Round: {{ $matchup->round ?? '—' }}</span>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('referee.assignPlayers', $matchup->id) }}">
                    @csrf
                    <h5 class="mb-3">Select 4 Active Players per Team</h5>
                    <div class="row">
                        @foreach (['A' => $matchup->teamA, 'B' => $matchup->teamB] as $side => $team)
                            <div class="col-md-6">
                                <h6>{{ $team->name }}</h6>
                                @foreach ($team->players as $player)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            name="team_{{ strtolower($side) }}_players[]" value="{{ $player->id }}"
                                            {{ $matchup->activePlayers->where('player_id', $player->id)->where('team_side', $side)->isNotEmpty() ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ $player->user->first_name }}
                                            {{ $player->user->last_name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                    <button class="btn btn-primary mt-3">Assign Players</button>
                </form>
            </div>
        </div>



        <div class="card shadow-sm">
            <div class="card-body d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Match Timer</h5>

                @if ($matchup->start_time)
                    <div class="d-flex align-items-center flex-grow-1">
                        <div class="flex-grow-1 d-flex justify-content-center">
                            <div id="countdown-timer" class="fw-bold text-primary"
                                style="font-family: monospace; min-width: 70px; text-align: center; font-size: 2.5rem; padding: 0.5rem;">
                                10:00
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3 ms-auto">
                            <div class="text-success fw-bold" style="margin-right: 20px">
                                Started at: {{ $matchup->start_time->format('H:i:s') }}
                            </div>
                            <form method="POST" action="{{ route('referee.resetTimer', $matchup->id) }}"
                                id="reset-form-{{ $matchup->id }}"
                                onsubmit="event.preventDefault(); confirmReset({{ $matchup->id }})">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">Reset Timer</button>
                            </form>
                        </div>
                    </div>
                @else
                    <form method="POST" action="{{ route('referee.startTimer', $matchup->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-success">Start Timer</button>
                    </form>
                @endif
            </div>
        </div>



        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">Live Player Stats</h5>
                <div class="row">
                    @foreach (['A' => $matchup->teamA, 'B' => $matchup->teamB] as $side => $team)
                        <div class="col-md-6">
                            <h6 class="text-primary" style="font-size: 1.25rem; text-align: center;">
                                {{ $team->name }}</h6>
                            <h6 class="text-primary" style="font-size: 1.25rem; text-align: center;">
                                {{ $team->points }}</h6>
                            @foreach ($matchup->activePlayers->where('team_side', $side) as $assignment)
                                @php
                                    $player = $assignment->player;
                                    $user = $player->user;
                                    $stat = $matchup->stats->firstWhere('player_id', $player->id);
                                @endphp

                                <div class="border p-3 mb-3 rounded bg-light">
                                    <div style="display: flex; align-items: center;">
                                        <img src="{{ $user->avatar_url }}" alt="Avatar" class="rounded-circle me-2"
                                            style="width: 40px; height: 40px; object-fit: cover;">
                                        <strong style="margin-left: 10px">{{ $user->name }}</strong>
                                    </div>

                                    <form method="POST" action="{{ route('referee.updateStat') }}"
                                        class="row mt-2 gx-2 gy-1 align-items-center player-stat-form">
                                        @csrf
                                        <input type="hidden" name="matchup_id" value="{{ $matchup->id }}">
                                        <input type="hidden" name="player_id" value="{{ $player->id }}">

                                        <div class="col-4">
                                            <label class="form-label">Pts</label>
                                            <input type="number" name="points"
                                                class="form-control form-control-lg points-input"
                                                value="{{ $stat->points ?? 0 }}" min="0" >
                                            <div class="mt-1 d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-success custom-size-btn add-points"
                                                    data-value="1">+1</button>
                                                <button type="button" class="btn btn-sm btn-outline-success custom-size-btn add-points"
                                                    data-value="2">+2</button>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label">AST</label>
                                            <input type="number" name="assists"
                                                class="form-control form-control-lg assists-input"
                                                value="{{ $stat->assists ?? 0 }}" min="0" >
                                            <div class="mt-1" style="display: flex; justify-content: center;">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary custom-size-btn add-assist">+1</button>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label">Fouls</label>
                                            <input type="number" name="fouls"
                                                class="form-control form-control-lg fouls-input"
                                                value="{{ $stat->fouls ?? 0 }}" min="0" >
                                            <div class="mt-1" style="display: flex; justify-content: center;">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger custom-size-btn add-foul">+1</button>
                                            </div>
                                        </div>
                                        <div class="col-12 text-end">
                                            <button class="btn btn-sm btn-outline-primary mt-2">Save</button>
                                        </div>
                                    </form>

                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h5>Set Winning Team</h5>
                <form method="POST" action="{{ route('referee.setWinner', $matchup->id) }}"
                    class="row align-items-center mt-3"
                    style="display: flex; justify-content: space-between; padding: 0 60px;">
                    @csrf

                    <select name="winner_team_id" class="form-select" style="width: 200px; margin-right: 20px;">
                        <option value="{{ $matchup->teamA->id }}"
                            {{ $matchup->winner_team_id == $matchup->teamA->id ? 'selected' : '' }}>
                            {{ $matchup->teamA->name }}
                        </option>
                        <option value="{{ $matchup->teamB->id }}"
                            {{ $matchup->winner_team_id == $matchup->teamB->id ? 'selected' : '' }}>
                            {{ $matchup->teamB->name }}
                        </option>
                    </select>
                    <button class="btn btn-success" style="width: 150px;">Confirm Winner</button>
                </form>
            </div>
        </div>
    </div>

    @if ($matchup->start_time)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const totalSeconds = 10 * 60;
                const matchStart = new Date("{{ $matchup->start_time->toIso8601String() }}");
                const timerDisplay = document.getElementById('countdown-timer');

                if (!timerDisplay) {
                    console.error('Timer element not found!');
                    return;
                }

                function updateTimer() {
                    const now = new Date();
                    const elapsedSeconds = Math.floor((now - matchStart) / 1000);
                    let remaining = totalSeconds - elapsedSeconds;

                    if (remaining < 0) {
                        remaining = 0;
                        clearInterval(timerInterval);
                    }

                    const minutes = Math.floor(remaining / 60).toString().padStart(2, '0');
                    const seconds = (remaining % 60).toString().padStart(2, '0');
                    timerDisplay.textContent = `${minutes}:${seconds}`;
                }

                updateTimer();
                const timerInterval = setInterval(updateTimer, 1000);
            });
        </script>
    @endif
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">

    <script>
        function confirmReset(id) {
            Swal.fire({
                title: "Are you sure?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, reset it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('reset-form-' + id).submit();
                }
            });
        }


        $(document).ready(function() {
            setTimeout(() => {
                $('#successMessage').fadeOut('slow');
                $('#errorMessage').fadeOut('slow');
            }, 3000);
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.add-points').forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest('form');
                    const pointsInput = form.querySelector('.points-input');
                    const addValue = parseInt(this.getAttribute('data-value'));
                    pointsInput.value = parseInt(pointsInput.value) + addValue;
                });
            });

            document.querySelectorAll('.add-assist').forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest('form');
                    const assistInput = form.querySelector('.assists-input');
                    assistInput.value = parseInt(assistInput.value) + 1;
                });
            });

            document.querySelectorAll('.add-foul').forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest('form');
                    const foulInput = form.querySelector('.fouls-input');
                    foulInput.value = parseInt(foulInput.value) + 1;
                });
            });
        });
    </script>
<style>
    .custom-size-btn {
    min-width: 80px;   /* make button longer */
    padding: 0.5rem 1.2rem;  /* increase height and horizontal padding */
    font-size: 1rem;   /* keep font normal size */
}
</style>
</x-app-layout>
