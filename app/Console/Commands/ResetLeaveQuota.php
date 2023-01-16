<?php

namespace App\Console\Commands;

use App\Models\LeaveQuota;
use App\Models\LeaveType;
use Illuminate\Console\Command;

class ResetLeaveQuota extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:reset-quota';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for reset leave quota';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $leave_types = LeaveType::get();

        foreach ($leave_types as $type) {
            LeaveQuota::where('leave_type_id', $type->id)->update(['quota' => $type->quota]);
        }
        
        return Command::SUCCESS;
    }
}
