<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTrack extends Model
{
    use HasFactory;
     protected $fillable = [
        'empCode',
        'is_tracking_active',
        // Add other fields here if needed
    ];
    protected $guarde =['id'];
    protected $table = 'user_trackers';
     public function user()
    {
        return $this->belongsTo(User::class, 'empCode', 'empCode');
    }
}
