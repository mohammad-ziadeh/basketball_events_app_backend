<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl" style="color: #3b1e54;">Category Team Control</h2>
        @include('components.breadcrumbs', [
            'breadcrumbs' => [
                'Dashboard' => route('dashboard'),
                'Events' => route('events.index'),
                'Category Teams' => '#',
            ],
        ])
    </x-slot>

    @include('components.alerts')

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5 mt-8" style="margin-top: 20px">
        @foreach ($categories as $category)
            <div class="card shadow-sm" style="margin-top: 10px">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">{{ $category->name }}</h5>
                        <small>Category ID: {{ $category->id }}</small> |
                        <small>Capacity: {{ $category->capacity }}</small>
                    </div>
                    <span class="badge bg-secondary">{{ $category->teams->count() }} Team(s)</span>
                </div>

                <div class="card-body" >
                    @if ($category->teams->isEmpty())
                        <div class="alert alert-info mb-0">No teams assigned to this category.</div>
                    @else
                        <ul class="list-group">
                            @foreach ($category->teams as $team)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $team->name }}</strong>
                                        <span
                                            class="badge bg-{{ $team->pivot->status === 'approved' ? 'success' : ($team->pivot->status === 'denied' ? 'danger' : 'warning') }} ms-2">
                                            {{ ucfirst($team->pivot->status) }}
                                        </span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#editStatusModal" data-team-id="{{ $team->id }}"
                                        data-category-id="{{ $category->id }}"
                                        data-current-status="{{ $team->pivot->status }}"
                                        aria-label="Edit status for {{ $team->name }}">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Edit Status Modal -->
    <div class="modal fade" id="editStatusModal" tabindex="-1" aria-labelledby="editStatusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" id="editStatusForm" class="modal-content">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title" id="editStatusModalLabel">Edit Team Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="teamIdInput" name="teamId">
                    <input type="hidden" id="categoryIdInput" name="categoryId">

                    <div class="mb-3">
                        <label for="statusSelect" class="form-label">Status</label>
                        <select class="form-select" id="statusSelect" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="denied">Denied</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveStatusBtn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"
        integrity="sha384-7qAoOXltbVP82dhxHAUje59V5r2YsVfBafyUDxEdApLPmcdhBPg1DKg1ERo0BZlK" crossorigin="anonymous">
    </script>
    <script>
        var editStatusModal = document.getElementById('editStatusModal');
        var statusSelect = document.getElementById('statusSelect');
        var teamIdInput = document.getElementById('teamIdInput');
        var categoryIdInput = document.getElementById('categoryIdInput');
        var editStatusForm = document.getElementById('editStatusForm');
        var saveStatusBtn = document.getElementById('saveStatusBtn');

        editStatusModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var teamId = button.getAttribute('data-team-id');
            var categoryId = button.getAttribute('data-category-id');
            var currentStatus = button.getAttribute('data-current-status');

            teamIdInput.value = teamId;
            categoryIdInput.value = categoryId;
            statusSelect.value = currentStatus;

            editStatusForm.action = `/event/category-teams/${teamId}/${categoryId}`;
        });

        editStatusForm.addEventListener('submit', function() {
            saveStatusBtn.disabled = true;
            saveStatusBtn.innerHTML = 'Saving...';
        });
    </script>

    {{-- 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" /> --}}
</x-app-layout>
