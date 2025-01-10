<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CateParent extends Model
{
    protected $fillable = ['name'];

    public function children()
    {
        return $this->hasMany(CateChild::class, 'parent_id');
    }
}
