<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="{{ $modalId }}Label"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="{{ $modalId }}Label">
                    Create Matchup for {{ $event->title }} - {{ $category->name }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>

            <form method="POST" action="{{ route('matchups.store') }}">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">
                <input type="hidden" name="category_id" value="{{ $category->id }}">

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="team_a_{{ $modalId }}" class="form-label">Team A</label>
                            <select name="team_a_id" id="team_a_{{ $modalId }}" class="form-control select2"
                                required>
                                <option value="">Select Team A</option>
                                @foreach ($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}
                                        ({{ $team->pivot->status }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="team_b_{{ $modalId }}" class="form-label">Team B</label>
                            <select name="team_b_id" id="team_b_{{ $modalId }}" class="form-control select2"
                                required>
                                <option value="">Select Team B</option>
                                @foreach ($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}
                                        ({{ $team->pivot->status }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="match_time_{{ $modalId }}" class="form-label">Match Time</label>
                            <input type="datetime-local" name="match_time" id="match_time_{{ $modalId }}"
                                class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label for="location_{{ $modalId }}" class="form-label">Location</label>
                            <input type="text" name="location" id="location_{{ $modalId }}"
                                class="form-control">
                        </div>

                        <div class="col-md-12">
                            <label for="round_{{ $modalId }}" class="form-label">Round</label>
                            <input type="text" name="round" id="round_{{ $modalId }}" class="form-control"
                                placeholder="e.g., Semi Final">
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Create Matchup</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#team_a_{{ $modalId }}, #team_b_{{ $modalId }}').select2({
            dropdownParent: $('#{{ $modalId }}')
        });
    });
</script>
