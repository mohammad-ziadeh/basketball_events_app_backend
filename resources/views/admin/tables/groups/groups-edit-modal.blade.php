<div class="modal fade" id="editGroupModal{{ $group->id }}" tabindex="-1" role="dialog"
    aria-labelledby="editGroupLabel{{ $group->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('groups.update', $group->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Group</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Group Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $group->name }}" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id" class="form-control" required>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ $group->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}  (Event: {{ $category->event ? $category->event->title : 'No Event' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
