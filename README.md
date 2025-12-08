# Laravel 12 Browser Screenshot

## 1. Introduction

This project allows users to take screenshots of websites directly from a browser URL and store them in the Laravel application.

✅ **Key Features:**

- Take a screenshot of any valid URL.
- Store screenshots in storage and save the path in the database.
- View all previously taken screenshots.
- Soft delete screenshots and update their status to 'deleted'.
- Keep proper database and file structure.
- Easy and clean UI using Tailwind CSS.

---

## 2. Project Setup

### Step 1: Create Laravel 12 Project
```bash
composer create-project laravel/laravel laravel12-screen-shot "12.*"
cd laravel12-screen-shot

```
Step 2: Setup Environment
```
cp .env.example .env

```
Generate application key:
```
php artisan key:generate
```
Step 3: Install Spatie Browsershot
```
composer require spatie/browsershot
```
Browsershot is a PHP wrapper for Puppeteer, which automates Chrome for taking screenshots.

Step 4: Install Node.js dependencies
```
npm install puppeteer
```
Puppeteer downloads headless Chrome automatically.
Note: Node.js is only required for Puppeteer, not for Laravel itself.

Step 5: Setup Storage Link
```
php artisan storage:link
```
This creates public/storage pointing to storage/app/public.

Step 5: Create Storage Folder for Screenshots
```
cd storage/app/public
mkdir screenshots
cd ../../../
```
Now screenshots will be saved in storage/app/public/screenshots.

Step 6: Configure Database in .env
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=browser_screen_shot
DB_USERNAME=root
DB_PASSWORD=
```
✅ Make sure after setup .env, run:
```
php artisan migrate
```
This will automatically create the browser_screen_shot database in MySQL.

Step 7: Database Migration

1️⃣ Create Migration File
```

php artisan make:migration create_screenshots_table

```
Migration file will be generated in:

laravel12-screen-shot/database/migrations/

2️⃣ Migration File Content

```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the 'screenshots' table
        Schema::create('screenshots', function (Blueprint $table) {
            $table->id(); // Primary key (auto-increment)
            $table->string('url'); // Store the website URL for which screenshot is taken
            $table->string('image_path'); // Store the path of the saved screenshot image
            $table->enum('status', ['active','inactive', 'deleted'])->default('active'); 
            // Status of the screenshot (active, inactive, deleted)

            $table->foreignId('created_by')->nullable(); // User ID who created the screenshot (optional)
            $table->foreignId('updated_by')->nullable(); // User ID who last updated the screenshot (optional)

            $table->timestamps(); // Adds created_at and updated_at columns automatically
            $table->softDeletes(); // Adds deleted_at column for soft deleting records
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the 'screenshots' table if exists
        Schema::dropIfExists('screenshots');
    }
};

```
Run Migration:
```

php artisan migrate

```
Step 8: Model — Screenshot

1️⃣ Create Model
```
php artisan make:model Screenshot
```

File location: laravel12-screen-shot/app/Models/Screenshot.php

2️⃣ Model File with Comments
```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // For using model factories (optional)
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // For soft deleting records (deleted_at)

class Screenshot extends Model
{
    use HasFactory, SoftDeletes; 
    // HasFactory: Allows using factories for testing or seeding
    // SoftDeletes: Enables soft delete functionality (deleted_at column)

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'url',          // Website URL for which the screenshot is taken
        'image_path',   // Path of the saved screenshot in storage
        'status',       // Status of the screenshot (active, inactive, deleted)
        'created_by',   // Optional: ID of the user who created the screenshot
        'updated_by',   // Optional: ID of the user who last updated the screenshot
    ];
}


```
Step 9: Controller — ScreenshotController

1️⃣ Create Controller
```

php artisan make:controller ScreenshotController

```
File location: laravel12-screen-shot/app/Http/Controllers/ScreenshotController.php

2️⃣ Controller File with Comments
```

<?php

namespace App\Http\Controllers;

use App\Models\Screenshot;               // Import Screenshot model for database operations
use Illuminate\Http\Request;             // Import Request class to handle form submissions
use Spatie\Browsershot\Browsershot;     // Import Browsershot package to take browser screenshots
use Illuminate\Support\Str;             // Import Str helper to generate random strings

class ScreenshotController extends Controller
{
    /**
     * Display a listing of all active screenshots.
     */
    public function index()
    {
        // Fetch all screenshots where status is 'active', ordered by latest first
        return view('screenshots.index', [
            'screenshots' => Screenshot::where('status', 'active')->latest()->get()
        ]);
    }

    /**
     * Show the form to create a new screenshot.
     */
    public function create()
    {
        // Return the 'create' view
        return view('screenshots.create');
    }

    /**
     * Store a newly created screenshot in the database.
     */
    public function store(Request $request)
    {
        // Validate the input URL: required and must be a valid URL format
        $request->validate([
            'url' => 'required|url'
        ]);

        // Generate a random filename for the screenshot image
        $fileName = Str::random(20) . '.png';

        // Define the storage path to save the screenshot
        $path = storage_path('app/public/screenshots/' . $fileName);

        // Use Browsershot to capture a screenshot of the given URL
        Browsershot::url($request->url)
            ->windowSize(1366, 768)  // Set browser window size for screenshot
            ->save($path);           // Save screenshot to storage path

        // Save screenshot details to database
        Screenshot::create([
            'url' => $request->url,
            'image_path' => 'screenshots/' . $fileName
        ]);

        // Redirect back to form with a success message
        return redirect()->back()->with('success', 'Screenshot Taken Successfully');
    }

    /**
     * Soft delete a screenshot by updating status and using soft deletes.
     */
    public function destroy($id)
    {
        // Find screenshot by ID or fail if not found
        $screenshot = Screenshot::findOrFail($id);

        // Update status to 'deleted' instead of removing the record
        $screenshot->status = 'deleted';
        $screenshot->save();

        // Soft delete the record (fills 'deleted_at' column)
        $screenshot->delete();

        // Redirect to index page with success message
        return redirect()->route('screenshots.index')
            ->with('success', 'Screenshot deleted successfully!');
    }
}


```
Step 10: Web Routes

File: routes/web.php
```
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScreenshotController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file contains all web routes for the Laravel12-Screen-Shot project.
| Routes define which controller methods handle which URLs. 
| Middleware 'web' is applied by default for session, CSRF protection, etc.
|
*/

// Route to display all active screenshots
// URL: /screenshots
// Controller: ScreenshotController@index
// Named route: screenshots.index
Route::get('/screenshots', [ScreenshotController::class, 'index'])->name('screenshots.index');

// Route to show the form for creating a new screenshot
// URL: /screenshots/create
// Controller: ScreenshotController@create
// Named route: screenshots.create
Route::get('/screenshots/create', [ScreenshotController::class, 'create'])->name('screenshots.create');

// Route to handle form submission and store new screenshot in database
// URL: /screenshots (POST)
// Controller: ScreenshotController@store
// Named route: screenshots.store
Route::post('/screenshots', [ScreenshotController::class, 'store'])->name('screenshots.store');

// Route to soft delete a screenshot (change status to 'deleted' and use soft delete)
// URL: /screenshots/{id} (DELETE)
// Controller: ScreenshotController@destroy
// Named route: screenshots.destroy
Route::delete('/screenshots/{id}', [ScreenshotController::class, 'destroy'])->name('screenshots.destroy');

// Default home route for the application
// URL: /
// Returns the default welcome view
Route::get('/', function () {
    return view('welcome');
});


```
Step 11: Blade Views

1️⃣ create.blade.php

```
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


```
2️⃣ index.blade.php
```
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

```
Step 12: Project Folder Structure
```

laravel12-screen-shot/
│
├── app/
│   ├── Http/Controllers/ScreenshotController.php
│   └── Models/Screenshot.php
│
├── database/migrations/
│   └── 2025_12_08_create_screenshots_table.php
│
├── resources/views/screenshots/
│   ├── create.blade.php
│   └── index.blade.php
│
├── storage/app/public/screenshots/
├── routes/web.php
├── .env
├── composer.json
└── package.json

```
Step 13: Running the Project
Start Laravel server:
```

php artisan serve

```
Open browser:
```
http://127.0.0.1:8000/screenshots
```

Capture Screenshot
Navigate to /screenshots/create.

```
Enter a valid website URL (e.g., https://www.google.com) in the form.

Click Take Screenshot.

Screenshot will be saved in storage/app/public/screenshots and listed on the index page.

Database record created.

```

View & Soft Delete

Go to /screenshots.
```

Click X to mark screenshot as deleted (status = deleted).

Record is soft-deleted (deleted_at set) and no longer visible.

```
✅ Project Ready!

Your Laravel 12 Browser Screenshot project is now fully set up and running. 🎉
