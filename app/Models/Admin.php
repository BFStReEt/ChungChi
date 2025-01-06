<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Admin extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'admin';
    protected $primaryKey = 'id';

    protected $fillable = [
        'username',
        'password',
        'email',
        'display_name',
        'avatar',
        'skin',
        'depart_id',
        'is_default',
        'lastlogin',
        'code_reset',
        'menu_order',
        'status',
        'phone',
        'created_at',
        'updated_at'
    ];
    public function department()
    {
        // return $this->hasOne(Department::class,'id','depart_id');
        return $this->belongsTo(Department::class, 'depart_id', 'id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'admin_role');
    }
    public function hasPermission($permission)
    {
        foreach ($this->roles as $role) {
            if ($role->permissions->where('slug', $permission)->count() > 0) {
                return true;
            }
        }
        return false;
    }
}
