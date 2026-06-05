<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes; // Add SoftDeletes trait here

    protected $fillable = [
        'name',
        'category',
        'price',
        'stock',
        'image_path',
        'user_id',
        'delete_remarks',     // Added tracking fields
        'deleted_by_user_id'  // Added tracking fields
    ];

    // Link showing the user who registered the product
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // UPDATED: Renamed to match the exact eager loading relationship call inside your ReportsController
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by_user_id');
    }
}