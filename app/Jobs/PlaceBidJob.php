<?php

namespace App\Jobs;

use App\Models\AdSlot;
use App\Models\Bid;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class PlaceBidJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, Dispatchable;

    public function __construct(
        public User $user,
        public int  $slotId,
        public float $amount
    ) {}

    public function handle(): void
    {
         try {
                DB::transaction(function () {
                    /** @var AdSlot $slot */
                    $slot = AdSlot::lockForUpdate()->findOrFail($this->slotId); 
                    if ($slot->status !== 'open') {
                        throw new \Exception('Slot not open for bidding.');
                    }

                    if ($this->amount < $slot->min_bid_price) {
                        throw new \Exception('Bid amount is below the minimum allowed.');
                    }

                    Bid::create([
                        'user_id'    => $this->user->id,
                        'ad_slot_id' => $slot->id,
                        'bid_amount' => $this->amount,
                    ]);
                });
            } catch (\Throwable $e) {
               
                \Log::error('Bid Placement Failed', [
                    'error' => $e->getMessage(),
                    'user_id' => $this->user->id,
                    'slot_id' => $this->slotId,
                    'amount' => $this->amount,
                ]);

        }
    }
}
