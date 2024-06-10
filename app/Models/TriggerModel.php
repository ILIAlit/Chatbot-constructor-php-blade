<?php

namespace App\Models;

use DefStudio\Telegraph\Models\TelegraphBot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TriggerModel extends Model
{
    use HasFactory;

    public function bots()
    {
        return $this->belongsToMany(TelegraphBot::class);
    }
}