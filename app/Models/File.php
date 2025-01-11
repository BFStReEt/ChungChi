<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class File extends Model
{
    use HasFactory;
    protected $fillable = ['parent_id', 'child_id', 'year_id', 'name', 'path', 'mime_type', 'description'];
    protected $table = 'files';

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
