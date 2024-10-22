<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlowDay extends Model
{
    public function flow()
    {
      return $this->belongsTo(Flow::class);
    }
}