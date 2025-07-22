<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl" style="color: #3b1e54;">{{ __('Carousel') }}</h2>
        @include('components.breadcrumbs', [
            'breadcrumbs' => ['Dashboard' => route('dashboard'), 'Carousel' => route('carousels.index')],
        ])
    </x-slot>

    @include('components.alerts')

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" style="margin-top: 50px">
        <div class="p-4 sm:p-8 bg-white shadow rounded-lg">

            <button type="button" class="btn btn-success mb-3" data-toggle="modal" data-target="#createCarouselModal">
                Add New Carousel
            </button>

            <div class="d-flex flex-wrap gap-4 justify-content-start">
                @forelse ($carousels as $carousel)
                    <div class="card position-relative shadow-sm" style="width: 330px;">
                        <img style="width: 330px; height: 220px; object-fit: cover;"
                            src="{{ $carousel['image_url'] }}"
                            class="card-img-top" alt="Carousel Image">

                        <div class="card-body">
                            <h5 class="card-title">{{ $carousel['title'] }}</h5>
                            <p class="card-text">
                                <strong>Tag:</strong> {{ $carousel['tag'] }}
                            </p>

                            <div class="d-flex justify-content-between mt-3 flex-wrap gap-2">
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal"
                                    data-target="#editCarouselModal{{ $carousel['id'] }}">
                                    Edit
                                </button>
                                <form id="delete-form-{{ $carousel['id'] }}"
                                    action="{{ route('carousels.destroy', $carousel['id']) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="confirmDelete({{ $carousel['id'] }})">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">No carousel items found.</p>
                @endforelse
            </div>
        </div>
    </div>

    @include('admin.tables.carousels.carousel-create-modal')
    @foreach ($carousels as $carousel)
        @include('admin.tables.carousels.carousel-edit-modal', ['carousel' => $carousel])
    @endforeach

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "This action cannot be undone!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.querySelector('#delete-form-' + id).submit();
                }
            });
        }

        $(document).ready(function () {
            setTimeout(() => {
                $('#successMessage, #errorMessage').fadeOut('slow');
            }, 3000);
        });
    </script>
</x-app-layout>