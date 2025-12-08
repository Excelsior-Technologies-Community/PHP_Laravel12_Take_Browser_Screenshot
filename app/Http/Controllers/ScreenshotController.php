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
