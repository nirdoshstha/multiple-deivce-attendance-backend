<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'image',
        'phone',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    // polymorphic many to many relation

    // public function userable()
    // {
    //     return $this->morphTo();
    // }


    public function vendors()
    {
        return $this->morphedByMany(
            Vendor::class,
            'userable',
            'userables',
            'user_id',
            'userable_id'
        )
            ->withPivot('role')
            ->withTimestamps();
    }

    public function companies()
    {
        return $this->morphedByMany(
            Company::class,
            'userable',
            'userables',
            'user_id',
            'userable_id'
        )
            ->withPivot('role')
            ->withTimestamps();
    }

    public function company()
    {
        return $this->belongsTo(Staff::class, 'company_id', 'id');
    }
}
