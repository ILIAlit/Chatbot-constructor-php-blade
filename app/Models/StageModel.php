<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StageModel extends Model
{
    use HasFactory;

    public function chain() {
        return $this->belongsTo(ChainModel::class, 'chain_model_id');
    }
}