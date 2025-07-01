<?php

namespace App\Models;

use App\Traits\HasUserRelations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory, HasUserRelations, Searchable;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';

    protected $fillable = [
        'title',
        'content',
        'slug',
        'excerpt',
        'image',
        'status',
        'published_at',
        'created_by',
        'updated_by',
        'deleted_by',
        'uuid',
    ];
    protected $casts = [
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
        'published_at' => 'datetime',
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

    // scout searchable
    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
        ];
    }
}
