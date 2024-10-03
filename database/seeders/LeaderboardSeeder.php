<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Activity;

class LeaderboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory()->count(10)->create();

        foreach ($users as $user) {
            Activity::factory()
                ->count(rand(5, 20)) 
                ->create(['user_id' => $user->id]);

            // Update total points for each user
            $totalPoints = $user->activities->count() * 20;
            $user->update(['total_points' => $totalPoints]);
        }

       self::recalculateRanks();

    }

    public function recalculateRanks()
    {
        $users = User::orderBy('total_points', 'desc')->get();
        $rank = 1;

        foreach ($users as $user) {
            $user->update(['rank' => $rank]);
            $rank++;
        }
    }
}
