<div class="modal fade" id="createGalleryModal" tabindex="-1" role="dialog" aria-labelledby="createGalleryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background-color: #fff; border-radius: 8px;">
            <div class="modal-header border-bottom-0" style="padding: 1.5rem 1.5rem 0;">
                <h5 class="modal-title" id="createGalleryModalLabel" style="color: #000; font-weight: 600;">
                    Add New Gallery Item
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #000;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('gallery.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body" style="padding: 0 1.5rem 1rem;">

                    <div class="form-group mb-3">
                        <label for="event_id" class="form-label" style="color: #000;">Event</label>
                        <select name="event_id" class="form-control" required>
                            <option value="">Select Event</option>
                            @foreach ($events as $event)
                                <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>
                                    {{ $event->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="file" class="form-label" style="color: #000;">Image or Video</label>
                        <input type="file" class="form-control" name="file" required
                            accept="image/jpeg,image/jpg,image/png,video/mp4,video/mov,video/avi" />
                    </div>

                </div>

                <div class="modal-footer border-top-0 d-flex justify-content-end gap-2"
                    style="padding: 0 1.5rem 1.5rem;">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-success">
                        Create Gallery
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .form-control {
        font-size: 1rem;
        color: #000;
    }

    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
    }

    .btn {
        font-weight: 500;
    }
</style>
