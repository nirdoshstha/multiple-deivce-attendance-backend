<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackendBaseModel extends Model
{
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updator()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }


    public function scopeActive($query)
    {
        return $query->where('status', '0');
    }
}
