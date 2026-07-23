<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Take Screenshot</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-2xl">
    <h2 class="text-2xl font-bold text-center mb-4">Take Website Screenshot</h2>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-3">
            {{ session('success') }}
        </div>
    @endif

    <!-- Error Message -->
    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-2 rounded mb-3">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Screenshot Form -->
    <form action="/screenshots" method="POST" class="space-y-4">
        @csrf

        <!-- URL Input -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Website URL</label>
            <input type="url" name="url" required
                   placeholder="https://example.com"
                   class="w-full border rounded p-2 focus:ring focus:ring-indigo-300">
        </div>

        <!-- Viewport Preset -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Viewport Preset</label>
            <select name="viewport" id="viewportSelect"
                    class="w-full border rounded p-2 focus:ring focus:ring-indigo-300">
                <option value="desktop">Desktop (1366x768)</option>
                <option value="tablet">Tablet (768x1024)</option>
                <option value="mobile">Mobile (375x812)</option>
                <option value="custom">Custom</option>
            </select>
        </div>

        <!-- Custom Width/Height -->
        <div id="customSize" class="grid grid-cols-2 gap-4 hidden">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Width (px)</label>
                <input type="number" name="width" min="320" max="2560"
                       class="w-full border rounded p-2 focus:ring focus:ring-indigo-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Height (px)</label>
                <input type="number" name="height" min="240" max="1440"
                       class="w-full border rounded p-2 focus:ring focus:ring-indigo-300">
            </div>
        </div>

        <!-- Format & Quality -->
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Image Format</label>
                <select name="format" id="formatSelect"
                        class="w-full border rounded p-2 focus:ring focus:ring-indigo-300">
                    <option value="png">PNG</option>
                    <option value="jpeg">JPEG</option>
                    <option value="webp">WEBP</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quality (1-100)</label>
                <input type="number" name="quality" min="1" max="100" value="90"
                       class="w-full border rounded p-2 focus:ring focus:ring-indigo-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Delay (seconds)</label>
                <input type="number" name="delay" min="0" max="60" value="0"
                       class="w-full border rounded p-2 focus:ring focus:ring-indigo-300">
            </div>
        </div>

        <!-- Full Page Toggle -->
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_full_page" id="is_full_page" value="1"
                   class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
            <label for="is_full_page" class="text-sm text-gray-700">Capture Full Page</label>
        </div>

        <!-- Submit Button -->
        <button class="w-full bg-indigo-600 text-white p-2 rounded hover:bg-indigo-700">
            Capture Screenshot
        </button>
    </form>

    <!-- Link to Screenshot List -->
    <div class="text-center mt-4">
        <a href="/screenshots" class="text-indigo-600 hover:underline">
            View Screenshots
        </a>
    </div>
</div>

<script>
const viewportSelect = document.getElementById('viewportSelect');
const customSize = document.getElementById('customSize');

viewportSelect.addEventListener('change', function() {
    if (this.value === 'custom') {
        customSize.classList.remove('hidden');
    } else {
        customSize.classList.add('hidden');
    }
});
</script>

</body>
</html>
