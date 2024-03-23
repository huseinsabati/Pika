<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\comment;
use App\Models\like;
use Illuminate\Notifications\Notifiable;

class post extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'body',
        'user_id',
        'image'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function comment(){
        return $this->hasMany(comment::class);
    }
    public function like(){
        return $this->hasMany(like::class);
    }
}
