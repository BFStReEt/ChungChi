<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'parent_id', 'type'];

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }
}
