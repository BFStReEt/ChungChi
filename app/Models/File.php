<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = ['parent_id', 'child_id', 'year_id', 'name', 'path', 'mime_type', 'description'];

    public function parent()
    {
        return $this->belongsTo(CateParent::class, 'parent_id');
    }

    public function child()
    {
        return $this->belongsTo(CateChild::class, 'child_id');
    }

    public function year()
    {
        return $this->belongsTo(CateYear::class, 'year_id');
    }
}
