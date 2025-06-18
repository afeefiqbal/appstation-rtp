<?php

namespace App\Http\Controllers;

use App\Models\AdSlot;
use App\Models\Bid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs\PlaceBidJob;

class BidController extends Controller
{
    // Place a bid (queued)
    public function store(Request $request, $slotId)
    {
        $request->validate([
            'bid_amount' => 'required|numeric|min:0.01',
        ]);

        $slot = AdSlot::findOrFail($slotId);
       
        if ($slot->status !== 'open') {
        return response()->json(['message' => 'Slot not open for bidding.'], 400);
        }

        if ($request->bid_amount < $slot->min_bid_price) {
            return response()->json(['message' => 'Bid amount is below the minimum allowed.'], 400);
        }

        // Dispatch to queue
        PlaceBidJob::dispatch(Auth::user(), $slot->id, $request->bid_amount);

        return response()->json(['message' => 'Bid submitted successfully']);
    }

    // Show authenticated user's bid history
    public function history()
    {
        $bids = Auth::user()->bids()->with('adSlot')->latest()->get();
        return response()->json($bids);
    }
}
