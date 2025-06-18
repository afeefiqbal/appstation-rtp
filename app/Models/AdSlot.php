<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdSlot extends Model
{
    protected $fillable = ['name','start_time', 'end_time', 'min_bid_price', 'status'];
    
    public function bids()
    {
        return $this->hasMany(Bid::class);
    }

}
