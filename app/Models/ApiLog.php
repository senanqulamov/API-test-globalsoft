<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    public $timestamps = false;
    protected $fillable = ['method', 'url', 'ip_address', 'created_at'];
}
