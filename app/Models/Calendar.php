<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    protected $table = 'calendars';

    protected $fillable = [
        'title',
        'date',
        'image',
        'description',
        'is_holiday',
        'created_by',
        'updated_by'
    ];
}
