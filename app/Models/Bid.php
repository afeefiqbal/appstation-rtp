<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    protected $fillable = ['user_id', 'ad_slot_id', 'bid_amount'];
    public function adSlot()
    {
        return $this->belongsTo(AdSlot::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
