<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category_id', 'price', 'stock', 'image_url', 'description', 'user_id'];

    // Relationship with Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
