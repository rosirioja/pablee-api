<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $guarded = [
      'id', 'created_at', 'modified_at'
    ];
}
