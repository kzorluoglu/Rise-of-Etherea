<?php

namespace App\Jobs;

use App\Models\Construction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class CompleteConstruction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected int $buildingId;
    protected int $userId;

    public function __construct(int $buildingId, int $userId)
    {
        $this->buildingId = $buildingId;
        $this->userId = $userId;
    }
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Fetch the ongoing construction for the provided building_id
        $construction = Construction::where('building_id', $this->buildingId)
            ->where('user_id', $this->userId)
            ->where('end_time', '<=', Carbon::now())
            ->first();

        // Check if the construction exists and it has completed
        if ($construction && Carbon::now()->greaterThanOrEqualTo($construction->end_time)) {

            $user = \App\Models\User::with('buildings')->find($this->userId);
            // The construction has completed, add the building to the user's buildings
            $user->buildings()->attach($this->buildingId); // Use attach method here

            // Delete the construction record
            $construction->delete();
        }
    }
}
