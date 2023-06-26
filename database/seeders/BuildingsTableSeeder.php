<?php

namespace Database\Seeders;

use App\Models\Building;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BuildingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $buildings = [
            [
                'id' => 1,
                'name' => 'Town Hall',
                'description' => 'The administrative center of your city.',
                'cost' => 1000,
                'construction_time' => 7200,
                'requirements' => [],
            ],
            [
                'id' => 2,
                'name' => 'Farm',
                'description' => 'Produces food for your city.',
                'cost' => 300,
                'construction_time' => 3600,
                'requirements' => ['TownHall'],
            ],
            [
                'id' => 3,
                'name' => 'Barracks',
                'description' => 'Trains your city\'s soldiers.',
                'cost' => 500,
                'construction_time' => 5400,
                'requirements' => ['TownHall'],
            ],
            [
                'id' => 4,
                'name' => 'Magic Laboratory',
                'description' => 'A place to study and enhance your magical abilities.',
                'cost' => 800,
                'construction_time' => 10800,
                'requirements' => ['TownHall', 'Farm'],
            ],
        ];

        foreach ($buildings as $building) {
            Building::create($building);
        }
    }

}
