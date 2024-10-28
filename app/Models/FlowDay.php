<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlowDay extends Model
{

  protected $fillable = [
    'number',    
    'flow_id',
    'text',
];

    public function flow()
    {
      return $this->belongsTo(Flow::class);
    }

    public function messages()
    {
      return $this->hasMany(MessageDaysFlow ::class);
    }
}