<?php

namespace App\Models;

use App\Traits\HasUserRelations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Page extends Model
{
    /** @use HasFactory<\Database\Factories\PageFactory> */
    use HasFactory, HasUserRelations, Searchable;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';

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

    // scout searchable
    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug
        ];
    }
}
