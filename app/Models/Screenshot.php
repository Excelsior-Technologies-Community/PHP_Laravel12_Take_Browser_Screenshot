<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Screenshot extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'url',
        'image_path',
        'status',
        'created_by',
        'updated_by',
        'download_count'
    ];
}