<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaCustomer extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'name', 'email', 'phone'];

    protected $table = 'ca_customers';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
}
