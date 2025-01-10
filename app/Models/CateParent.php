<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CateParent extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    protected $table = 'cate_parent';
    public function children()
    {
        return $this->hasMany(CateChild::class, 'parent_id');
    }
}
