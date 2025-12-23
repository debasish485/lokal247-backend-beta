<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Recruiter;

class JobPost extends Model
{
    use SoftDeletes;

    /**
     * Mass-assignable fields
     */
    protected $fillable = [
        'uuid',
        'slug',
        'recruiter_id',
        'category_id',
        'title',
        'description',
        'work_type',
        'city',
        'locality',
        'shift_timing',
        'start_time',
        'end_time',
        'pay_type',
        'pay_amount',
        'number_of_workers',
        'gender_preference',
        'start_date',
        'is_active',
    ];

    /**
     * Auto-generate UUID and slug on create/update
     */
protected static function boot()
{
    parent::boot();

    static::creating(function ($model) {

        // UUID
        if (empty($model->uuid)) {
            $model->uuid = Str::uuid()->toString();
        }

        // Slug
        if (empty($model->slug) && !empty($model->title) && !empty($model->recruiter_id)) {

            // Get recruiter number
            $recruiter = Recruiter::find($model->recruiter_id);

            if ($recruiter && $recruiter->phone) {

                // Last 4 digits of recruiter number
                $lastFour = substr($recruiter->phone, -4);

                // Random 2-digit number
                $randomTwo = rand(10, 99);

                // Build slug
                $slugBase = "{$model->title}-recruiter-{$lastFour}{$randomTwo}";

                $model->slug = Str::slug($slugBase);
            }
        }
    });
}

    /**
     * Relationships
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function recruiter()
    {
        return $this->belongsTo(Recruiter::class);
    }
}
