<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BotFlow extends Model
{
    use HasFactory;

    protected $fillable = [
      'token',
      'name',
      'day',
      'start_date',
      'time_message_send',
  ];

    public function flows()
    {
      return $this->hasMany(Flow::class);
    }
}