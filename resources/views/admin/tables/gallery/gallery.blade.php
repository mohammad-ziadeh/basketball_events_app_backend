<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl" style="color: #3b1e54;">{{ __('Gallery') }}</h2>
        @include('components.breadcrumbs')
    </x-slot>

    @include('components.alerts')

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" style="margin-top: 50px">
        <div class="p-4 sm:p-8 bg-white shadow rounded-lg">

            <!-- Search / Filter Form -->
            <form method="GET" action="{{ route('gallery.index') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" name="title" class="form-control" placeholder="Search Title"
                            value="{{ request('title') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="event_id" class="form-control">
                            <option value="">All Events</option>
                            @foreach ($events as $event)
                                <option value="{{ $event->id }}"
                                    {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                    {{ $event->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="type" class="form-control">
                            <option value="">All types</option>
                            <option value="photo" {{ request('type') == 'photo' ? 'selected' : '' }}>Photo</option>
                            <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Video</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('gallery.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>

            <button type="button" class="btn btn-success mb-3" data-toggle="modal" data-target="#createGalleryModal">
                Add New Gallery Item
            </button>

            <div class="gallery">
                @foreach ($galleries as $gallery)
                    <figure>
                        @if ($gallery->type === 'photo' && $gallery->photo)
                            <img src="{{ asset('storage/' . $gallery->photo) }}"
                                alt="{{ $gallery->event->title ?? 'Gallery Image' }}">
                        @elseif ($gallery->type === 'video' && $gallery->video)
                            <video controls>
                                <source src="{{ asset('storage/' . $gallery->video) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @else
                            <div
                                style="width: 100%; height: 100%; background: #ccc; display:flex; align-items:center; justify-content:center;">
                                No media
                            </div>
                        @endif

                        <figcaption>
                            <div style="display: flex; justify-content: space-between;">
                                <h3 style="color: black">Event: {{ $gallery->event->title ?? 'No Event' }}</h3>
                                <p style="color: grey">Created: {{ $gallery->created_at->format('Y-m-d') }}</p>
                            </div>


                            <div style="margin-top: 0.5rem;">
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                    data-target="#editGalleryModal{{ $gallery->id }}">
                                    Edit
                                </button>
                                <form action="{{ route('gallery.destroy', $gallery->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm"
                                        onclick="confirmDelete({{ $gallery->id }})">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </figcaption>
                    </figure>

                    @include('admin.tables.gallery.gallery-edit-modal', ['gallery' => $gallery])
                @endforeach
            </div>

            @include('components.pagination', ['pag' => $galleries])
        </div>
    </div>

    @include('admin.tables.gallery.gallery-create-modal')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: "Are you sure?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.querySelector('form[action$="/' + id + '"]').submit();
                }
            });
        }
    </script>
    <style>
        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(20rem, 1fr));
            gap: 0.75rem;
        }

        .gallery figure {
            overflow: hidden;
            border-radius: 0.5rem;
            box-shadow: 0 2px 8px rgb(0 0 0 / 0.15);
            background: #fff;
            display: block;
        }

        .gallery img,
        .gallery video {
            width: 100%;
            height: 20.5rem;
            object-fit: cover;
            border-radius: 0.5rem 0.5rem 0 0;
            transition: none;
        }

        .gallery figure:hover img,
        .gallery figure:hover video {
            transform: none;
        }

        .gallery figcaption {
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
            color: #3b1e54;
            border-radius: 0 0 0.5rem 0.5rem;
            background: #f9f9f9;
        }

        .gallery figcaption h3 {
            margin: 0 0 0.3rem 0;
            font-size: 1rem;
        }

        .gallery figcaption p {
            margin: 0 0 0.5rem 0;
        }

        .gallery figcaption div {
            margin-top: 0.5rem;
        }

        .gallery button {
            margin-right: 0.25rem;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
    </style>
</x-app-layout>
