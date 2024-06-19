<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class Dealer extends Model
{
    use HasFactory;
    protected $table = 'dealer';
    
     public function user()
    {
        return $this->belongsTo(User::class, 'emp_code', 'empCode');
    }
}
