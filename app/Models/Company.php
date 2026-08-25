<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;
    protected $table = 'companies';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'authorized_person',
        'logo',
        'pan',
        'status',
        'created_by',
        'updated_by'
    ];



    public function users()
    {
        return $this->morphToMany(
            User::class,
            'userable',
            'userables',
            'userable_id',
            'user_id'
        )
            ->withPivot('role')
            ->withTimestamps();
    }

    // Vendor.php

}
