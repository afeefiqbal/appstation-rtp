<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WinningBid extends Model
{
    protected $fillable = ['user_id', 'ad_slot_id','won_at','bid_id'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
