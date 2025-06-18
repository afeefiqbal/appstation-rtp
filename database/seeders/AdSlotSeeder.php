<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdSlot;
use Carbon\Carbon;

class AdSlotSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $adSlots = [
            [
                'name' => 'Upcoming Ad Slot',
                'start_time' => $now->copy()->addHours(2),
                'end_time' => $now->copy()->addHours(4),
                'status' => 'upcoming',
                'min_bid_price' => 100,
     
            ],
            [
                'name' => 'Open Ad Slot',
                'start_time' => $now->copy()->subHour(),
                'end_time' => $now->copy()->addHours(1),
                'status' => 'open',
                'min_bid_price' => 150,
           
            ],
             [
                'name' => 'Open Ad Slot',
                'start_time' => $now->copy()->subHour(),
                'end_time' => $now->copy()->addHours(1),
                'status' => 'open',
                'min_bid_price' => 100,
           
            ],
            [
                'name' => 'Closed Ad Slot',
                'start_time' => $now->copy()->subHours(3),
                'end_time' => $now->copy()->subHour(),
                'status' => 'closed',
                'min_bid_price' => 200,
              
            ],
            [
                'name' => 'Awarded Ad Slot',
                'start_time' => $now->copy()->subDays(1),
                'end_time' => $now->copy()->subHours(20),
                'status' => 'awarded',
                'min_bid_price' => 250,
             
            ],
            
        ];

        foreach ($adSlots as $slot) {
            AdSlot::create($slot);
        }
    }
}
