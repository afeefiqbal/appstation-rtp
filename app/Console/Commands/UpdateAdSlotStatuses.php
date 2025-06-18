<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AdSlot;

class UpdateAdSlotStatuses extends Command
{
    protected $signature = 'rtb:update-statuses';
    protected $description = 'Update ad slot statuses based on start and end times';

    public function handle(): int
    {
        // Open slots where start_time has passed and status is not open
        AdSlot::where('status', 'upcoming')
            ->where('start_time', '<=', now())
            ->update(['status' => 'open']);

        // Close slots where end_time has passed and status is open
        AdSlot::where('status', 'open')
            ->where('end_time', '<=', now())
            ->update(['status' => 'closed']);

        return 0;
    }
}
