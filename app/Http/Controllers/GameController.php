<?php

namespace App\Http\Controllers;

use App\Jobs\CompleteConstruction;
use App\Models\Building;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class GameController
{

    public function index()
    {
        $user = auth()->user();

        // Fetch the player's existing buildings
        $playerBuildings = $user->buildings()->get()->toArray();
        $buildings = Building::all()->toArray();

        // Fetch the player's ongoing constructions
        $ongoingConstructions = $user->constructions()
            ->where('end_time', '>', Carbon::now())
            ->get(['building_id', 'end_time'])
            ->map(function ($construction) {
                $construction->building_id = (string)$construction->building_id;
                return $construction;
            })
            ->toArray();

        // Pass the data to the view
        return Inertia::render('Game', [
            'user' => [
                'name' => $user->name,
                'gold' => $user->gold,
                'totalBuildings' => 99999999,
                'totalExperience' => 99999999,
            ],
            'buildings' => Building::all(),
            'playerBuildings' => $playerBuildings ?? [], // Return an empty array if null
            'ongoingConstructions' => $ongoingConstructions ?? [], // Return an empty array if null
            'authToken' => $user->createToken('authToken')->plainTextToken,
        ]);
    }

    public function startConstruction(Request $request) {

        $buildingId = $request->input('building_id');

        // Retrieve building data (This could come from a config file, database, etc.)
        $building = Building::find($buildingId);

        // Fetch the player data from the database
        $user = User::with('buildings')->find(auth()->id());

        // Check if the player has enough resources
        if($user->gold < $building->cost) {
            return response()->json(['error' => 'Insufficient resources'], 400);
        }

        // Check if the player meets the building requirements
        if ($this->meetsBuildingRequirements($user, $building->requirements) === false) {
            return response()->json(['error' => 'Building requirements not met'], 400);
        }

        // If checks pass, deduct the cost from player's resources
        $user->gold -= $building->cost;
        $user->save();

        // Calculate end time
        $endTime = Carbon::now()->addSeconds($building->construction_time);

        // Insert a new record into the constructions table
        DB::table('constructions')->insert([
            'user_id' => $user->id,
            'building_id' => $buildingId,
            'start_time' => Carbon::now(),
            'end_time' => $endTime,
        ]);

        dispatch(
            (new CompleteConstruction($buildingId, $user->id))->delay($endTime)
        );

        // Return the end time to the client
        return response()->json(['end_time' => $endTime]);
    }

    private function meetsBuildingRequirements($user, $requirements) {
        foreach ($requirements as $requirement) {
            $hasBuilding = $user->buildings()
                ->wherePivot('building_id', $requirement)
                ->exists();
            if (!$hasBuilding) {
                return false;
            }
        }
        return true;
    }

    public function endConstruction(Request $request)
    {
        // Validate the request data
        $request->validate([
            'building_id' => 'required|integer|exists:buildings,id',
        ]);

        $user = auth()->user();

        // Fetch the ongoing construction for the provided building_id
        $construction = $user->constructions()
            ->where('building_id', $request->building_id)
            ->where('end_time', '<=', Carbon::now())
            ->first();

        // Check if the construction exists and it has completed
        if ($construction && Carbon::now()->greaterThanOrEqualTo($construction->end_time)) {
            // The construction has completed, add the building to the user's buildings
            $user->buildings()->attach($request->building_id); // Use attach method here

            // Delete the construction record
            $construction->delete();

            return response()->json([
                'message' => 'Construction completed successfully.',
            ], 200);
        } else {
            // The construction either doesn't exist or hasn't completed yet
            return response()->json([
                'message' => 'Construction not found or not yet completed.',
            ], 404);
        }
    }

}
