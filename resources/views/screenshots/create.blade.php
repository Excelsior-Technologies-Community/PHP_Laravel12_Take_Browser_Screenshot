<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Take Screenshot</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md">
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
        <input type="url" name="url" required
               placeholder="https://example.com"
               class="w-full border rounded p-2 focus:ring focus:ring-indigo-300">

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

</body>
</html>
