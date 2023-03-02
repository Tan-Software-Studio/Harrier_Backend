<?php

namespace App\Models\Admin;

use App\Mail\ContactMail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class ContactUs extends Model
{
    use HasFactory;
    protected $table="contact_us";

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'is_read'
    ];

    protected $hidden = [
        
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public static function boot() {
  
        parent::boot();
  
        static::created(function ($item) {
                
            $adminEmail = config('mail.contact_mail.address');
            Mail::to($adminEmail)->send(new ContactMail($item));
        });
    }
}
