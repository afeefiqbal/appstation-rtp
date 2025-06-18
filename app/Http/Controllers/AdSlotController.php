<?php

namespace App\Http\Controllers;
use App\Models\AdSlot;
use App\Models\Bid;
use App\Models\WinningBid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdSlotController extends Controller
{
     // List all ad slots with optional status filter
    public function index(Request $request)
    {
        $status = $request->query('status');

        $slots = AdSlot::when($status, fn($q) => $q->where('status', $status))->get();
        $slotCount = $slots->count();
        $message = $slotCount > 0
            ? 'Ad slots fetched successfully.'
            : 'No ad slots found.';
        return response()->json([
            'message' => $message,
            'count' => $slotCount, 
            'data' => $slots,
        ]);
    }
   public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:ad_slots,name',
            'start_time' => 'required|date|after_or_equal:today',
            'end_time' => 'required|date|after:start_time',
            'min_bid_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Failed',
                'errors' => $validator->errors()
            ], 422); 
        }

        try {
            $slot = AdSlot::create([
                'name' => $request->name,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'min_bid_price' => $request->min_bid_price,
                'status' => 'upcoming', // Set initial status
            ]);

            return response()->json([
                'message' => 'Ad slot created successfully',
                'slot' => $slot
            ], 201); 

        } catch (Throwable $e) {
            \Log::error('Error creating ad slot: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'message' => 'An error occurred while creating the ad slot.',
                'error' => $e->getMessage() 
        ], 500); 
        }
    
    }
    public function update(Request $request, $id)
    {
        try {
            $adSlot = AdSlot::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name'              => 'nullable|string|max:255',
                'start_time'        => 'nullable|date|after_or_equal:today',
                'end_time'          => 'nullable|date|after_or_equal:start_time', 
                'min_bid_price'     => 'nullable|numeric|min:0', 
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation Failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            $adSlot->update($validator->validated());

            return response()->json([
                'message' => 'Ad Slot updated successfully',
                'data'    => $adSlot 
            ], 200); 

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
          
            return response()->json([
                'message' => 'Ad Slot not found.',
                'error'   => $e->getMessage() 
            ], 404); 
        } catch (Throwable $e) {
            
            \Log::error('Error updating ad slot: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all(),
                'ad_slot_id' => $id
            ]);

            return response()->json([
                'message' => 'An unexpected error occurred while updating the ad slot.',
                'error'   => $e->getMessage() // Remove this line in production
            ], 500); 
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $adSlot = AdSlot::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:upcoming,open,closed,awarded',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation Failed',
                    'errors' => $validator->errors()
                ], 422); 
            }

            $adSlot->status = $validator->validated()['status']; 
            $adSlot->save();

            return response()->json([
                'message' => 'Ad Slot status updated successfully',
                'data'    => $adSlot
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Ad Slot not found.',
                'error'   => $e->getMessage() 
            ], 404); 
        } catch (Throwable $e) {
            \Log::error('Error updating ad slot status: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all(),
                'ad_slot_id' => $id
            ]);

            return response()->json([
                'message' => 'An unexpected error occurred while updating the ad slot status.',
                'error'   => $e->getMessage() 
            ], 500); 
        }
    }
    // View all bids for a particular slot
    public function bids($id)
    {
        $slot = AdSlot::findOrFail($id);
        $bids = $slot->bids()->with('user')->orderByDesc('bid_amount')->get();
        return response()->json($bids);
    }

    // View winning bid for a slot
    public function winner($id)
    {
        $winner = WinningBid::where('ad_slot_id', $id)->with('user')->first();
        if (!$winner) {
            return response()->json(['message' => 'Not awarded yet'], 404);
        }
        return response()->json($winner);
    }
}
