@if (isset($event))
<div class="modal fade" id="editEventModal{{ $event->id }}" tabindex="-1" role="dialog"
    aria-labelledby="editEventModalLabel{{ $event->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-3 shadow-sm">
            <div class="modal-header bg-light border-bottom-0 py-3 px-4">
                <h5 class="modal-title fw-semibold text-dark" id="editEventModalLabel{{ $event->id }}">
                    Edit Event
                </h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="{{ route('events.update', $event->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-body px-4 py-3">
                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label fw-medium">Title</label>
                        <input type="text" class="form-control" name="title" id="title"
                            value="{{ old('title', $event->title) }}" required>
                    </div>

                    <!-- Location -->
                    <div class="mb-3">
                        <label for="location" class="form-label fw-medium">Location</label>
                        <input type="text" class="form-control" name="location" id="location"
                            value="{{ old('location', $event->location) }}" required>
                    </div>

                    <!-- Date Range -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label fw-medium">Start Date</label>
                            <input type="date" class="form-control" name="start_date" id="start_date"
                                value="{{ old('start_date', optional($event->start_date)->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label fw-medium">End Date</label>
                            <input type="date" class="form-control" name="end_date" id="end_date"
                                value="{{ old('end_date', optional($event->end_date)->format('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">Categories</label>
                        <div id="category-wrapper-{{ $event->id }}">
                            @foreach ($event->categories as $i => $category)
                            <div class="category-group border rounded p-3 mb-3 bg-light">
                                <div class="row g-2 align-items-end">
                                    <input type="hidden" name="categories[{{ $i }}][id]" value="{{ $category->id }}">
                                    <div class="col-md-6">
                                        <label class="form-label">Name</label>
                                        <input type="text" name="categories[{{ $i }}][name]" class="form-control" 
                                            value="{{ $category->name }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Capacity</label>
                                        <input type="number" name="categories[{{ $i }}][capacity]" class="form-control" 
                                            value="{{ $category->capacity }}">
                                    </div>
                                    <div class="col-md-2 d-flex justify-content-end">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-category">×</button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" 
                            data-event-id="{{ $event->id }}" id="add-category-{{ $event->id }}">+ Add Category</button>
                    </div>

                    <!-- Event Code -->
                    <div class="mb-3">
                        <label for="event_code" class="form-label fw-medium">Event Code</label>
                        <input type="text" class="form-control" name="event_code" id="event_code"
                            value="{{ old('event_code', $event->event_code) }}" required>
                    </div>

                    <!-- Image Upload -->
                    <div class="mb-3">
                        <label for="image" class="form-label fw-medium">Upload Image (Optional)</label>
                        <input type="file" class="form-control" name="image" id="image">
                        <small class="form-text text-muted mt-1">
                            Max dimensions: 330×220px.
                            (<a href="https://imageresizer.com/" target="_blank">Resize online</a>)
                        </small>
                        @if ($event->image_url)
                        <div class="mt-2">
                            <p>Current Image:</p>
                            <img src="{{ $event->image_url }}" alt="Current Image" width="100">
                        </div>
                        @endif
                    </div>
                </div>

                <div class="modal-footer border-top-0 px-4 py-3">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let categoryIndex_{{ $event->id }} = {{ count($event->categories) }};

    document.getElementById('add-category-{{ $event->id }}').addEventListener('click', function () {
        const wrapper = document.getElementById('category-wrapper-{{ $event->id }}');
        const html = `
            <div class="category-group border rounded p-3 mb-3 bg-light">
                <div class="row g-2 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" name="categories[${categoryIndex_{{ $event->id }} }][name]" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Capacity</label>
                        <input type="number" name="categories[${categoryIndex_{{ $event->id }} }][capacity]" class="form-control">
                    </div>
                    <div class="col-md-2 d-flex justify-content-end">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-category">×</button>
                    </div>
                </div>
            </div>
        `;
        wrapper.insertAdjacentHTML('beforeend', html);
        categoryIndex_{{ $event->id }}++;
    });

    document.getElementById('category-wrapper-{{ $event->id }}').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-category')) {
            e.target.closest('.category-group').remove();
        }
    });
</script>
@endif
