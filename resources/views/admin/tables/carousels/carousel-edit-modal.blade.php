@if (isset($carousel))
<div class="modal fade" id="editCarouselModal{{ $carousel['id'] }}" tabindex="-1" role="dialog"
    aria-labelledby="editCarouselModalLabel{{ $carousel['id'] }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-3 shadow-sm">
            <div class="modal-header bg-light border-bottom-0 py-3 px-4">
                <h5 class="modal-title fw-semibold text-dark" id="editCarouselModalLabel{{ $carousel['id'] }}">
                    Edit Carousel Item
                </h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('carousels.update', $carousel['id']) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label for="tag" class="form-label fw-medium">Tag</label>
                        <input type="text" class="form-control" name="tag" value="{{ old('tag', $carousel['tag']) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="title" class="form-label fw-medium">Title</label>
                        <input type="text" class="form-control" name="title" value="{{ old('title', $carousel['title']) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label fw-medium">New Image (Optional)</label>
                        <input type="file" class="form-control" name="image">
                        <small class="form-text text-muted mt-1">
                            Leave blank to keep current image.
                        </small>
                        <div class="mt-2">
                            <p>Current Image:</p>
                            <img src="{{ $carousel['image_url'] }}" alt="Current" width="100" style="border-radius: 4px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 py-3">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif