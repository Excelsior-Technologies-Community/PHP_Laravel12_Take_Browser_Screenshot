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
