<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use PhpParser\Builder\FunctionLike;

class Recruiter extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'email_verified_at',
        'phone',
        'alternate_phone',
        'company_name',
        'city',
        'company_address',
        'company_logo',
        'industry',
        'designation',
        'accepted_terms',
        'is_blocked',
        'signup_ip',
        'last_login_ip',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    protected $hidden = [
        'password',
    ];
public function jobs()
{
    return $this->hasMany(JobPost::class);
}


}
