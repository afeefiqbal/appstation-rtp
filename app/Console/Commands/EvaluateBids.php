<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AdSlot;
use App\Models\Bid;
use App\Models\WinningBid;

use Illuminate\Support\Facades\DB;

class EvaluateBids extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rtb:evaluate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Evaluate closed ad-slot bids and pick winners';

    /**
     * Execute the console command.
     */
     public function handle(): int
    {
        
        // process in small chunks to keep memory low
        AdSlot::where('status', 'closed')
            ->where('end_time', '<=', now())
            ->chunkById(50, function ($slots) {
               
                foreach ($slots as $slot) {
                    DB::transaction(function () use ($slot) {
                        // lock row so two overlapping schedulers never double‑award
                        $slot->refresh();
                        if ($slot->status !== 'closed') {
                            return; // another worker already took it
                        }

                        $winner = Bid::where('ad_slot_id', $slot->id)
                                     ->orderByDesc('bid_amount')
                                     ->orderBy('created_at')   // earlier = winner on tie
                                     ->first();

                        if (!$winner) {                      // no bids – just mark closed
                            $slot->update(['status' => 'awarded']);
                            return;
                        }

                        WinningBid::create([
                            'ad_slot_id' => $slot->id,
                            'user_id'    => $winner->user_id,
                            'bid_id'     => $winner->id,
                            'won_at'     => now(),
                        ]);

                        $slot->update(['status' => 'awarded']);
                    });
                }
            });

        return 0;
    }

}
