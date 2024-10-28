<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageDaysFlow extends Model
{

    protected $fillable = [
        'time_send',    
        'flow_day_id',
        'text'
    ];
    
    public function flowDay()
    {
      return $this->belongsTo(FlowDay::class);
    }
}