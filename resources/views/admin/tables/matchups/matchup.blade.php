<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl" style="color: #3b1e54;">{{ __('Event Matchups') }}</h2>
        @include('components.breadcrumbs', [
            'breadcrumbs' => ['Dashboard' => route('dashboard'), 'Matchups' => route('matchups.index')],
        ])
    </x-slot>

    @include('components.alerts')

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" style="margin-top: 50px;">
        <form method="GET" action="{{ route('matchups.index') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="title" class="form-control" placeholder="Search Event Title"
                        value="{{ request('title') }}">
                </div>
                <div class="col-md-2">
                    <select name="event_code" class="form-control">
                        <option value="">All Event Codes</option>
                        <option value="#3X3WT" {{ request('event_code') == '#3X3WT' ? 'selected' : '' }}>#3X3WT</option>
                        <option value="#SUMMER" {{ request('event_code') == '#SUMMER' ? 'selected' : '' }}>#SUMMER
                        </option>
                        <option value="#FINAL" {{ request('event_code') == '#FINAL' ? 'selected' : '' }}>#FINAL</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="sort" class="form-control">
                        <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Sort ASC</option>
                        <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Sort DESC</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="filter" class="form-control">
                        <option value="">All Dates</option>
                        <option value="upcoming" {{ request('filter') == 'upcoming' ? 'selected' : '' }}>Upcoming
                        </option>
                        <option value="past" {{ request('filter') == 'past' ? 'selected' : '' }}>Past</option>
                        <option value="ongoing" {{ request('filter') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="round" class="form-control">
                        <option value="">All Rounds</option>
                        @foreach ($availableRounds as $round)
                            <option value="{{ $round }}" {{ request('round') == $round ? 'selected' : '' }}>
                                {{ $round }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>

        @foreach ($events as $event)
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center bg-dark text-white">
                    <div>
                        <h5 class="mb-0">{{ $event->title }}</h5>
                        <small>{{ $event->event_code }} | {{ $event->location }} |
                            {{ $event->start_date->format('Y-m-d') }} -
                            {{ $event->end_date->format('Y-m-d') }}</small>
                    </div>
                </div>

                <div class="card-body">
                    @if ($event->categories->isEmpty())
                        <p class="text-muted">No categories or matchups found for this event.</p>
                    @else
                        @foreach ($event->categories as $category)
                            @php
                                $categoryMatchups = optional($event->matchups)
                                    ->where('category_id', $category->id)
                                    ->when(
                                        request('round'),
                                        fn($matchups) => $matchups->where('round', request('round')),
                                    );
                            @endphp

                            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                <h5 class="mb-0">{{ $category->name }}</h5>
                                <button class="btn btn-sm btn-primary" data-toggle="modal"
                                    data-target="#createMatchupModal{{ $event->id }}_category_{{ $category->id }}">
                                    + Create Matchup
                                </button>
                            </div>

                            @if ($category->matchups->isEmpty())
                                <p class="text-muted mb-4">No matchups in this category.</p>
                            @else
                                <div class="table-responsive mb-4">
                                    <table class="table table-bordered table-hover align-middle">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Team A</th>
                                                <th class="text-center">VS</th>
                                                <th>Team B</th>
                                                <th>Match Time</th>
                                                <th>Round</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($category->matchups as $matchup)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('referee.control', $matchup->id) }}"
                                                            class="text-decoration-none">
                                                            {{ $matchup->teamA->name }}
                                                        </a>
                                                    </td>
                                                    <td class="text-center fw-bold">VS</td>
                                                    <td>
                                                        <a href="{{ route('referee.control', $matchup->id) }}"
                                                            class="text-decoration-none">
                                                            {{ $matchup->teamB->name }}
                                                        </a>
                                                    </td>
                                                    <td>{{ optional($matchup->match_time)->format('Y-m-d H:i') ?? '—' }}
                                                    </td>
                                                    <td style="background-color: lightblue">
                                                        {{ $matchup->round ?? '—' }}
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('referee.control', $matchup->id) }}"
                                                            class="btn btn-sm btn-success">Control</a>

                                                        <button class="btn btn-sm btn-warning" data-toggle="modal"
                                                            data-target="#editMatchupModal{{ $matchup->id }}">Edit</button>

                                                        <form method="POST" id="delete-form-{{ $matchup->id }}"
                                                            action="{{ route('matchups.destroy', $matchup->id) }}"
                                                            class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                onclick="confirmDelete({{ $matchup->id }})"
                                                                class="btn btn-sm btn-danger">Delete</button>
                                                        </form>
                                                    </td>
                                                </tr>

                                                @include('admin.tables.matchups.matchup-edit-modal', [
                                                    'matchup' => $matchup,
                                                    'teams' => $event->teams,
                                                ])
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            @include('admin.tables.matchups.matchup-create-modal', [
                                'event' => $event,
                                'teams' => $category->teams,
                                'category' => $category,
                                'modalId' => "createMatchupModal{$event->id}_category_{$category->id}",
                            ])
                        @endforeach
                    @endif
                </div>
            </div>
        @endforeach

        @include('components.pagination', ['pag' => $events])
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js"></script>
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: "Are you sure?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.querySelector('#delete-form-' + id).submit();
                }
            });
        }

        $(document).ready(function() {
            $('select').select2({
                width: '100%'
            });

            $('.category-select').on('change', function() {
                const categoryId = $(this).val();
                const eventId = $(this).data('event');

                const teamASelect = $(`.team-select-${eventId}.team-a`);
                const teamBSelect = $(`.team-select-${eventId}.team-b`);

                teamASelect.html('<option>Loading...</option>');
                teamBSelect.html('<option>Loading...</option>');

                if (categoryId) {
                    $.get(`/admin/category-teams/${categoryId}`, function(data) {
                        let options = '<option value="">Select Team</option>';
                        data.forEach(team => {
                            options += `<option value="${team.id}">${team.name}</option>`;
                        });
                        teamASelect.html(options);
                        teamBSelect.html(options);
                    });
                } else {
                    teamASelect.html('<option value="">Select Team A</option>');
                    teamBSelect.html('<option value="">Select Team B</option>');
                }
            });
        });
    </script>
</x-app-layout>
