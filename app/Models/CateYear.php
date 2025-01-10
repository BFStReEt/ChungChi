<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CateYear extends Model
{
    protected $fillable = ['child_id', 'year'];

    public function child()
    {
        return $this->belongsTo(CateChild::class, 'child_id');
    }

    public function files()
    {
        return $this->hasMany(File::class, 'year_id');
    }
}
