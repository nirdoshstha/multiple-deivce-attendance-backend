<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class Menu extends BackendBaseModel
{
    protected $table = "menus";
    protected $fillable = ['name', 'permission_id', 'display_name', 'slug', 'rank', 'icon', 'route', 'parent_id', 'is_active', 'status', 'created_by', 'updated_by'];

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function subCategories()
    {
        return $this->hasMany(Menu::class, 'parent_id')->with('subCategories');;
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }
}
