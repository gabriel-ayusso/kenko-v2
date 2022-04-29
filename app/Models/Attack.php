<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attack extends Model
{
    protected $fillable = ['email', 'name', 'phone', 'reason', 'url', 'ip', 'user_agent'];
}
