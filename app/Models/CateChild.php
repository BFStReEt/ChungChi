<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CateChild extends Model
{
    protected $fillable = ['parent_id', 'name'];

    public function parent()
    {
        return $this->belongsTo(CateParent::class, 'parent_id');
    }

    public function years()
    {
        return $this->hasMany(CateYear::class, 'child_id');
    }
}
