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
