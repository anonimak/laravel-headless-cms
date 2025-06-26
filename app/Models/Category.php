<?php

namespace App\Models;

use App\Traits\HasUserRelations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory, HasUserRelations;
    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'uuid',
    ];
    protected $casts = [
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
        'parent_id' => 'integer',
    ];

    // soft delete support
    use \Illuminate\Database\Eloquent\SoftDeletes;

    // timestamps support
    public $timestamps = true;

    // pivot table for many-to-many relationship with posts category_post
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'category_post', 'category_id', 'post_id')
            ->withTimestamps()
            ->withSoftDeletes();
    }

    // self-referential relationship for parent categories
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // self-referential relationship for child categories
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
