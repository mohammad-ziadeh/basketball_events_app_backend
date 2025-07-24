<div class="modal fade" id="editGalleryModal{{ $gallery->id }}" tabindex="-1" role="dialog"
    aria-labelledby="editGalleryModalLabel{{ $gallery->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background-color: #fff; border-radius: 8px;">
            <div class="modal-header border-bottom-0" style="padding: 1.5rem 1.5rem 0;">
                <h5 class="modal-title" id="editGalleryModalLabel{{ $gallery->id }}"
                    style="color: #000; font-weight: 600;">
                    Edit Gallery Item
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #000;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('gallery.update', $gallery->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-body" style="padding: 0 1.5rem 1rem;">

                    <div class="form-group mb-3">
                        <label for="event_id" class="form-label" style="color: #000;">Event</label>
                        <select name="event_id" class="form-control" required>
                            <option value="">Select Event</option>
                            @foreach ($events as $event)
                                <option value="{{ $event->id }}"
                                    {{ old('event_id', $gallery->event_id) == $event->id ? 'selected' : '' }}>
                                    {{ $event->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="file" class="form-label" style="color: #000;">Image or Video (leave blank to keep current)</label>
                        <input type="file" class="form-control" name="file"
                            accept="image/jpeg,image/jpg,image/png,video/mp4,video/mov,video/avi" />
                        <div class="mt-2">
                            @if($gallery->type === 'photo' && $gallery->photo)
                                <img src="{{ asset('storage/' . $gallery->photo) }}" alt="Current Image" width="150" />
                            @elseif($gallery->type === 'video' && $gallery->video)
                                <video width="150" controls>
                                    <source src="{{ asset('storage/' . $gallery->video) }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            @endif
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-top-0 d-flex justify-content-end gap-2"
                    style="padding: 0 1.5rem 1.5rem;">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Update Gallery
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
