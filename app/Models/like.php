<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\comment;
use App\Models\post;
use Illuminate\Notifications\Notifiable;

class like extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'user_id',
        'post_id'
    ];
}
