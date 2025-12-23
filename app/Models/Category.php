<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use SoftDeletes;

    /**
     * Mass-assignable columns
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'category_icon',
        'image',
        'is_active',
    ];

    /**
     * Auto-generate uuid & slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }

            if (empty($model->slug) && !empty($model->name)) {
                $model->slug = Str::slug($model->name);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('name')) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    /**
     * Accessor — full image URL
     */
    public function getImageUrlAttribute()
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : null;
    }

    /**
     * Accessor — full icon URL
     */
    public function getIconUrlAttribute()
    {
        return $this->category_icon
            ? asset('storage/' . $this->category_icon)
            : null;
    }

    public function jobPosts(){
        return $this->hasMany(JobPost::class);
    }
}
