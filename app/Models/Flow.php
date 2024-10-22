<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flow extends Model
{
    use HasFactory;

    public function users()
    {
      return $this->hasMany(StudentFlow::class);
    }

    public function bot()
    {
      return $this->belongsTo(BotFlow::class);
    }
}