<div class="modal fade" id="createGroupModal" tabindex="-1" role="dialog" aria-labelledby="createGroupLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('groups.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Group</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="group-name">Group Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}  (Event: {{ $category->event ? $category->event->title : 'No Event' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Create</button>
                </div>
            </div>
        </form>
    </div>
</div>
