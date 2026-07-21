<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    //
    protected $fillable = [
        'logo',
        'fav_icon',
        'slogan',
        'email',
        'phone',
        'mobile',
        'address',
        'facebook',
        'twitter',
        'youtube',
        'linkedin',
        'instagram',
        'viber',
        'whatsapp',
        'google_map',
        'recaptcha_key',
        'recaptcha_secret',
        'created_by',
        'updated_by'
    ];
}
