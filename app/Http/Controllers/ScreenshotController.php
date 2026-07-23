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
    public function index(Request $request)
    {
        $query = Screenshot::where('status', 'active');

        if ($request->filled('search')) {
            $query->where('url', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('viewport')) {
            $query->where('viewport', $request->viewport);
        }

        if ($request->filled('format')) {
            $query->where('format', $request->format);
        }

        if ($request->filled('is_full_page')) {
            $query->where('is_full_page', (bool) $request->is_full_page);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('sort')) {
            $direction = 'desc';
            if ($request->sort === 'date_asc') {
                $query->orderBy('created_at', 'asc');
            } elseif ($request->sort === 'downs') {
                $query->orderBy('download_count', 'desc');
            } elseif ($request->sort === 'url') {
                $query->orderBy('url', 'asc');
            } else {
                $query->latest();
            }
        } else {
            $query->latest();
        }

        $screenshots = $query->paginate(12)->withQueryString();

        return view('screenshots.index', [
            'screenshots' => $screenshots
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
        $request->validate([
            'url' => 'required|url',
            'viewport' => 'nullable|string|in:desktop,tablet,mobile,custom',
            'format' => 'nullable|string|in:png,jpeg,webp',
            'quality' => 'nullable|integer|min:1|max:100',
            'is_full_page' => 'nullable|boolean',
            'width' => 'nullable|integer|min:320|max:2560',
            'height' => 'nullable|integer|min:240|max:1440',
            'delay' => 'nullable|integer|min:0|max:60'
        ]);

        $viewport = $request->viewport ?? 'desktop';
        $format = $request->format ?? 'png';
        $quality = $request->quality ?? 90;
        $isFullPage = $request->has('is_full_page');
        $delay = (int) ($request->delay ?? 0);

        $width = $request->width;
        $height = $request->height;

        switch ($viewport) {
            case 'mobile':
                $width = 375;
                $height = 812;
                break;
            case 'tablet':
                $width = 768;
                $height = 1024;
                break;
            case 'desktop':
            default:
                $width = $width ?? 1366;
                $height = $height ?? 768;
                break;
        }

        $fileName = Str::random(20) . '.' . $format;
        $path = storage_path('app/public/screenshots/' . $fileName);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $browsershot = Browsershot::url($request->url)
            ->windowSize((int) $width, (int) $height)
            ->timeout(180);

        if ($delay > 0) {
            $browsershot->setDelay($delay * 1000);
        }

        if ($isFullPage) {
            $browsershot->fullPage();
        }

        if ($format === 'png') {
            $browsershot->setScreenshotType('png');
        } elseif ($format === 'jpeg') {
            $browsershot->setScreenshotType('jpeg', $quality);
        } elseif ($format === 'webp') {
            $browsershot->setScreenshotType('webp', $quality);
        }

        $browsershot->save($path);

        $fileNameForDb = 'screenshots/' . $fileName;

        Screenshot::create([
            'url' => $request->url,
            'image_path' => $fileNameForDb,
            'status' => 'active',
            'viewport' => $viewport,
            'format' => $format,
            'quality' => $quality,
            'is_full_page' => $isFullPage ? 1 : 0,
            'width' => $width,
            'height' => $height
        ]);

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

    /**
     * Generate PDF report of screenshots.
     */
    public function generatePdf(Request $request)
    {
        $query = Screenshot::where('status', 'active');

        if ($request->filled('search')) {
            $query->where('url', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('viewport')) {
            $query->where('viewport', $request->viewport);
        }
        if ($request->filled('format')) {
            $query->where('format', $request->format);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $screenshots = $query->latest()->paginate(50);

        $html = view('screenshots.pdf', compact('screenshots'))->render();

        $pdfPath = storage_path('app/public/reports/screenshot-report-' . now()->format('Ymd-His') . '.pdf');

        if (!file_exists(dirname($pdfPath))) {
            mkdir(dirname($pdfPath), 0755, true);
        }

        Browsershot::html($html)
            ->timeout(180)
            ->format('A4')
            ->showBackground()
            ->save($pdfPath);

        return response()->download($pdfPath)->deleteFileAfterSend(true);
    }

    /**
     * Download a screenshot and increment the download count.
     */
    public function download($id)
    {
        // Find screenshot by ID
        $screenshot = Screenshot::findOrFail($id);

        // Increment download count
        $screenshot->increment('download_count');

        // Serve the file for download
        return response()->download(public_path('storage/' . $screenshot->image_path));
    }
}