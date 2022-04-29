<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaService extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'name', 'value', 'cost'];

    protected $table = 'ca_services';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
}
