<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl" style="color: #3b1e54;">{{ __('Event Matchups') }}</h2>
        @include('components.breadcrumbs', [
            'breadcrumbs' => ['Dashboard' => route('dashboard'), 'Matchups' => route('matchups.index')],
        ])
    </x-slot>

    @include('components.alerts')

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" style="margin-top: 50px;">

        {{-- Filter Form --}}
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
                        <option value="#SUMMER" {{ request('event_code') == '#SUMMER' ? 'selected' : '' }}>#SUMMER</option>
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
                        <option value="upcoming" {{ request('filter') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="past" {{ request('filter') == 'past' ? 'selected' : '' }}>Past</option>
                        <option value="ongoing" {{ request('filter') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="round" class="form-control">
                        <option value="">All Rounds</option>
                        @foreach ($availableRounds as $round)
                            <option value="{{ $round }}" {{ request('round') == $round ? 'selected' : '' }}>{{ $round }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>

        {{-- Manage Groups Section --}}
        <div class="card shadow-sm mt-5">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Manage Groups</h5>
                <button class="btn btn-light btn-sm" data-toggle="modal" data-target="#createGroupModal">
                    <i class="bi bi-plus-circle"></i> Add Group
                </button>
            </div>
            <div class="card-body">
                @foreach ($events as $event)
                    @foreach ($event->categories as $category)
                        <div class="mb-4">
                            <h6 class="text-primary mb-3 d-flex justify-content-between align-items-center"
                                style="cursor:pointer" data-toggle="collapse"
                                href="#categoryGroups{{ $category->id }}" role="button" aria-expanded="false"
                                aria-controls="categoryGroups{{ $category->id }}">
                                {{ $category->name }}
                                <i class="bi bi-chevron-down"></i>
                            </h6>
                            <div class="collapse" id="categoryGroups{{ $category->id }}">
                                @if ($category->groups->isEmpty())
                                    <p class="text-muted">No groups added yet.</p>
                                @else
                                    <ul class="list-group">
                                        @foreach ($category->groups as $group)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span><strong>{{ $group->name }}</strong></span>
                                                <div>
                                                    <button class="btn btn-sm btn-outline-warning me-2"
                                                        data-toggle="modal"
                                                        data-target="#editGroupModal{{ $group->id }}"
                                                        aria-label="Edit Group {{ $group->name }}">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <form method="POST" id="delete-group-form-{{ $group->id }}"
                                                        action="{{ route('groups.destroy', $group->id) }}" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            onclick="confirmGroupDelete({{ $group->id }})"
                                                            class="btn btn-sm btn-outline-danger"
                                                            aria-label="Delete Group {{ $group->name }}">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </li>

                                            @include('admin.tables.groups.groups-edit-modal', [
                                                'group' => $group,
                                                'categories' => $categories,
                                            ])
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>

        {{-- Matchups Section --}}
        @foreach ($events as $event)
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center bg-dark text-white">
                    <div>
                        <h5 class="mb-0">{{ $event->title }}</h5>
                        <small>{{ $event->event_code }} | {{ $event->location }} |
                            {{ $event->start_date->format('Y-m-d') }} - {{ $event->end_date->format('Y-m-d') }}</small>
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
                                    ->when(request('round'), fn($matchups) => $matchups->where('round', request('round')));
                                $matchupsByGroup = $categoryMatchups->groupBy('group_id');
                            @endphp

                            <div class="category-matchups-card mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                    <h5 class="mb-0">{{ $category->name }}</h5>
                                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal"
                                        data-target="#createMatchupModal{{ $event->id }}_category_{{ $category->id }}">
                                        + Create Matchup
                                    </button>
                                </div>

                                @if ($categoryMatchups->isEmpty())
                                    <p class="text-muted">No matchups in this category.</p>
                                @else
                                    @foreach ($matchupsByGroup as $groupId => $groupMatchups)
                                        @php
                                            $group = $category->groups->firstWhere('id', $groupId);
                                        @endphp

                                        <div class="group-section mb-3">
                                            <h6 class="group-title mb-2 text-secondary">
                                                {{ $group ? $group->name : 'Ungrouped' }}
                                            </h6>

                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover align-middle mb-0">
                                                    <thead class="table-light">
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
                                                        @foreach ($groupMatchups as $matchup)
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
                                                                <td>{{ optional($matchup->match_time)->format('Y-m-d H:i') ?? '—' }}</td>
                                                                <td style="background-color: lightblue">{{ $matchup->round ?? '—' }}</td>
                                                                <td>
                                                                    <a href="{{ route('referee.control', $matchup->id) }}"
                                                                        class="btn btn-sm btn-success">Control</a>
                                                                    <button type="button" class="btn btn-sm btn-warning"
                                                                        data-toggle="modal"
                                                                        data-target="#editMatchupModal{{ $matchup->id }}">Edit</button>

                                                                    <form method="POST" id="delete-form-{{ $matchup->id }}"
                                                                        action="{{ route('matchups.destroy', $matchup->id) }}"
                                                                        class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="button"
                                                                            onclick="confirmDelete({{ $matchup->id }})"
                                                                            class="btn btn-sm btn-danger">
                                                                            Delete
                                                                        </button>
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
                                        </div>
                                    @endforeach
                                @endif

                                @include('admin.tables.matchups.matchup-create-modal', [
                                    'event' => $event,
                                    'teams' => $category->teams,
                                    'category' => $category,
                                    'modalId' => "createMatchupModal{$event->id}_category_{$category->id}",
                                ])
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        @endforeach

        {{-- Modals for Creating Groups --}}
        @include('admin.tables.groups.groups-create-modal')

        {{-- Pagination --}}
        @include('components.pagination', ['pag' => $events])
    </div>

    <style>
        .category-matchups-card {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 1rem 1.25rem;
            border-radius: 0.375rem;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
        }

        .group-section {
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #eee;
        }

        .group-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .group-title {
            font-weight: 600;
            color: #555;
            margin-bottom: 0.5rem;
        }
    </style>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: "Are you sure?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("delete-form-" + id).submit();
                }
            });
        }

        function confirmGroupDelete(id) {
            Swal.fire({
                title: "Delete this group?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("delete-group-form-" + id).submit();
                }
            });
        }
    </script>
</x-app-layout>
