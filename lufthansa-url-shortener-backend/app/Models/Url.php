<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Url extends Model
{
    protected $fillable = ['original_url', 'short_url', 'expiry_time', 'access_count'];
    
    protected $table = 'urls';

}
