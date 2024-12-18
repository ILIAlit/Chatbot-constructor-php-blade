<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFlow extends Model
{
    use HasFactory;

    protected $fillable = [
      'name',
      'chat_id',
    ];

    public function flow()
    {
      return $this->belongsTo(Flow::class);
    }
}