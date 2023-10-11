<?php

namespace App\Models;

use App\Models\Emp\EmpOfficeLocation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\File;

class GuestMater extends Model
{
    use HasFactory, Notifiable, SoftDeletes;
    
    protected $table="guest_master";

    
    public function guestMaster() {
        return $this->belongsTo(User::class, 'email', 'email');
    }
}