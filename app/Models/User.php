<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table="users";
    protected $fillable = [
        'role',
        'name',
        'email',
        'email_verified_at',
        'password',
        'is_request',
        'is_login',
        'status',
        'expire_datetime',
        'old_email'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime:Y-m-d H:i:s',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

 

    public function isAdmin()
    {
        if ($this->role == roleAdmin()) {
            return true;
        } else {
            return false;
        }
    }

    public function isGuest()
    {
        if ($this->role == roleGuest()) {
            return true;
        } else {
            return false;
        }
    }

    public function isEmp()
    {
        if ($this->role == roleEmp()) {
            return true;
        } else {
            return false;
        }
    }

    public function isLogin()
    {
        if ($this->is_login == true) {
            return true;
        } else {
            return false;
        }
    }

    public function isRequest()
    {
        if ($this->is_request == true) {
            return true;
        } else {
            return false;
        }
    }

    public function isActive()
    {
        if ($this->status == true) {
            return true;
        } else {
            return false;
        }
    }

    public function guestMaster() {
        return $this->hasOne(GuestMater::class, 'email', 'email');
    }
}
