<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Screenshots</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<!-- Top Navbar -->
<div class="bg-indigo-600 text-white p-4 text-center font-bold text-xl">
    Screenshot Gallery
</div>

<div class="max-w-6xl mx-auto p-6">

    <!-- Button -->
    <div class="text-right mb-4">
        <a href="/screenshots/create"
           class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            + New Screenshot
        </a>
    </div>

    <!-- Screenshot Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($screenshots as $shot)
            <div class="relative bg-white shadow p-4 rounded">

                <!-- Delete Button at Top Right -->
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

                <p class="text-sm text-gray-600 break-all">{{ $shot->url }}</p>
                <img src="{{ asset('storage/'.$shot->image_path) }}"
                     class="mt-3 rounded border">
            </div>
        @empty
            <p class="text-center col-span-2 text-gray-500">
                No screenshots available.
            </p>
        @endforelse
    </div>

</div>

</body>
</html>
