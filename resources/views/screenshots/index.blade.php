<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>All Screenshots</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .lightbox {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.9);
            z-index: 50;
            align-items: center;
            justify-content: center;
        }
        .lightbox.active {
            display: flex;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">

    <!-- Top Navbar -->
    <div class="bg-indigo-600 text-white p-4 text-center font-bold text-xl">
        Screenshot Gallery
    </div>

    <div class="max-w-7xl mx-auto p-6">

        <!-- Actions -->
        <div class="flex justify-between items-center mb-4">
            <a href="{{ route('screenshots.create') }}"
               class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                + New Screenshot
            </a>
            <a href="{{ route('screenshots.pdf', request()->query()) }}"
               class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
               target="_blank">
                Download PDF Report
            </a>
        </div>

        <!-- Search & Filters -->
        <form action="{{ route('screenshots.index') }}" method="GET" class="bg-white p-4 rounded shadow mb-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search URL..."
                       class="border rounded p-2 text-sm">

                <select name="viewport" class="border rounded p-2 text-sm">
                    <option value="">All Viewports</option>
                    <option value="desktop" @selected(request('viewport') === 'desktop')>Desktop</option>
                    <option value="tablet" @selected(request('viewport') === 'tablet')>Tablet</option>
                    <option value="mobile" @selected(request('viewport') === 'mobile')>Mobile</option>
                    <option value="custom" @selected(request('viewport') === 'custom')>Custom</option>
                </select>

                <select name="format" class="border rounded p-2 text-sm">
                    <option value="">All Formats</option>
                    <option value="png" @selected(request('format') === 'png')>PNG</option>
                    <option value="jpeg" @selected(request('format') === 'jpeg')>JPEG</option>
                    <option value="webp" @selected(request('format') === 'webp')>WEBP</option>
                </select>

                <select name="sort" class="border rounded p-2 text-sm">
                    <option value="">Newest First</option>
                    <option value="date_asc" @selected(request('sort') === 'date_asc')>Oldest First</option>
                    <option value="downs" @selected(request('sort') === 'downs')>Most Downloaded</option>
                    <option value="url" @selected(request('sort') === 'url')>URL (A-Z)</option>
                </select>

                <select name="is_full_page" class="border rounded p-2 text-sm">
                    <option value="">All Pages</option>
                    <option value="1" @selected(request('is_full_page') === '1')>Full Page Only</option>
                    <option value="0" @selected(request('is_full_page') === '0')>Viewport Only</option>
                </select>

                <button type="submit" class="bg-indigo-600 text-white rounded px-4 py-2 text-sm hover:bg-indigo-700">
                    Apply Filters
                </button>
            </div>

            @if(request()->filled('search') || request()->filled('viewport') || request()->filled('format') || request()->filled('sort'))
                <a href="{{ route('screenshots.index') }}" class="text-sm text-red-600 hover:underline">Clear Filters</a>
            @endif
        </form>

        <!-- Gallery Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($screenshots as $shot)
                <div class="relative bg-white shadow p-4 rounded flex flex-col items-center">
                    <!-- Delete Button -->
                    <form action="{{ route('screenshots.destroy', $shot->id) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this screenshot?')"
                          class="absolute top-2 right-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-700">
                            &times;
                        </button>
                    </form>

                    <!-- URL -->
                    <p class="text-sm text-gray-600 break-all">{{ $shot->url }}</p>

                    <!-- Image -->
                    <img src="{{ asset('storage/' . $shot->image_path) }}" alt="Screenshot"
                         class="mt-3 rounded border max-h-48 cursor-pointer hover:opacity-90"
                         data-lightbox="{{ asset('storage/' . $shot->image_path) }}">

                    <!-- Info Tags -->
                    <div class="flex flex-wrap gap-2 mt-2 justify-center">
                        <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ ucfirst($shot->viewport) }}</span>
                        <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ strtoupper($shot->format) }}</span>
                        @if($shot->is_full_page)
                            <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded">Full Page</span>
                        @endif
                        <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $shot->width }}x{{ $shot->height }}</span>
                    </div>

                    <!-- Timing -->
                    <p class="text-xs text-gray-500 mt-1">{{ $shot->created_at->format('M d, Y H:i') }}</p>

                    <!-- Actions -->
                    <div class="flex gap-2 mt-3">
                        <a href="{{ route('screenshots.download', $shot->id) }}"
                           class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                            Download
                        </a>
                    </div>

                    <p class="text-xs text-gray-500 mt-1">Downloads: {{ $shot->download_count }}</p>
                </div>
            @empty
                <p class="text-center col-span-full text-gray-500">
                    No screenshots found.
                </p>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $screenshots->links() }}
        </div>

    </div>

    <!-- Lightbox -->
    <div id="lightbox" class="lightbox">
        <button id="closeLightbox" class="absolute top-4 right-4 text-white text-3xl">&times;</button>
        <img id="lightboxImage" src="" class="max-h-full max-w-full object-contain">
    </div>

    <script>
        const images = document.querySelectorAll('img[data-lightbox]');
        const lightbox = document.getElementById('lightbox');
        const lightboxImage = document.getElementById('lightboxImage');
        const closeLightbox = document.getElementById('closeLightbox');

        images.forEach(img => {
            img.addEventListener('click', () => {
                lightboxImage.src = img.dataset.lightbox;
                lightbox.classList.add('active');
            });
        });

        closeLightbox.addEventListener('click', () => {
            lightbox.classList.remove('active');
            lightboxImage.src = '';
        });

        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) {
                lightbox.classList.remove('active');
                lightboxImage.src = '';
            }
        });
    </script>

</body>

</html>
