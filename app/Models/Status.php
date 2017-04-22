<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table = 'status';

    protected $guarded = [
      'id', 'name', 'display_name'
    ];

    public function scopeOfName($query, $type)
    {
      return $query->where('name', $type)->get()->first();
    }
}
