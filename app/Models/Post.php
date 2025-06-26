<?php

namespace App\Models;

use App\Traits\HasUserRelations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory, HasUserRelations;
    protected $fillable = [
        'title',
        'slug',
        'body',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'uuid',
    ];
    protected $casts = [
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
    ];
    // soft delete support
    use \Illuminate\Database\Eloquent\SoftDeletes;
    // timestamps support
    public $timestamps = true;
    // pivot table for many-to-many relationship with categories post_category
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_post', 'post_id', 'category_id')
            ->withTimestamps()
            ->withSoftDeletes();
    }
}
