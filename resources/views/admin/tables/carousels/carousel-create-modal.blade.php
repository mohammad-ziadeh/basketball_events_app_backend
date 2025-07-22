<div class="modal fade" id="createCarouselModal" tabindex="-1" aria-labelledby="createCarouselModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-3 shadow-sm">
      <div class="modal-header bg-light border-bottom-0 py-3 px-4">
        <h5 class="modal-title fw-semibold text-dark" id="createCarouselModalLabel">
          Add New Carousel Item
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('carousels.store') }}" enctype="multipart/form-data" novalidate>
        @csrf
        <div class="modal-body px-4 py-4">
          <div class="mb-4">
            <label for="tag" class="form-label fw-medium">Tag <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="tag" name="tag" value="{{ old('tag') }}" required aria-describedby="tagHelp">
            <div id="tagHelp" class="form-text">A short label or category for the carousel item.</div>
          </div>

          <div class="mb-4">
            <label for="title" class="form-label fw-medium">Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required aria-describedby="titleHelp">
            <div id="titleHelp" class="form-text">
              <em>"TOP TEAMS FROM AROUND THE WORLD COMPETE THE WORLD COMPETE"  (this is the max length)</em>.
            </div>
          </div>

          <div class="mb-4">
            <label for="image" class="form-label fw-medium">Upload Image (330×220px) <span class="text-danger">*</span></label>
            <input class="form-control" type="file" id="image" name="image" accept="image/jpeg, image/png" required aria-describedby="imageHelp">
            <div id="imageHelp" class="form-text mt-1">
              Max size: 5MB. Use smaller images for better performance. <a href="https://imageresizer.com/" target="_blank" rel="noopener">Resize online</a>.
            </div>
            <ul class="mt-2 small text-muted">
              <li><strong>Recommended Dimensions:</strong>
                <ul>
                  <li>Width: <strong>800–1200 px</strong></li>
                  <li>Height: <strong>500–900 px</strong> <br><em>(Keep aspect ratio close to 16:9 or 4:3)</em></li>
                </ul>
              </li>
              <li><strong>File Format:</strong> JPEG or PNG only. Optimize for web with compression but maintain quality.</li>
            </ul>
          </div>
        </div>
        <div class="modal-footer border-top-0 px-4 py-3">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Item</button>
        </div>
      </form>
    </div>
  </div>
</div>
