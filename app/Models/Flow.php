<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flow extends Model
{
    use HasFactory;

    protected $fillable = [
      'start_date',
      'number',    
      'bot_flow_id',
      'day',
  ];

    public function users()
    {
      return $this->hasMany(StudentFlow::class);
    }

    public function bot()
    {
      return $this->belongsTo(BotFlow::class);
    }

    public function flowDays()
    {
      return $this->hasMany(FlowDay::class);
    }
}