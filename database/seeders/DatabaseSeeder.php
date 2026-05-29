<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Todo;
use App\Models\Achievement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User seeders

        // User
        User::create([
            'username' => 'user1',
            'email' => 'user1@onward.com',
            'password' => Hash::make('user1'),
            'first_name' => 'user1',
            'last_name' => 'user1',
            'sex' => 'male',
            'date_of_birth' => '2000-01-01',
            'role' => 'user'
        ]);

        // Admin
        User::create([
            'username' => 'admin',
            'email' => 'admin@onward.com',
            'password' => Hash::make('admin'),
            'first_name' => 'admin',
            'last_name' => 'admin',
            'sex' => 'male',
            'date_of_birth' => '2000-01-01',
            'role' => 'admin'
        ]);


        // Todos Seeder

        // Low priority
        Todo::create([
            'id' => 1,
            'user_id' => 1,
            'task' => 'Low effort',
            'description' => 'Test task low',
            'start_date' => '2000-01-01',
            'end_date' => '2030-01-01',
            'is_completed' => False,
            'completed_at' => NULL,
            'priority' => 1,
            'status' => 2,
            'created_at' => '2000-01-01 00:00:01',
            'updated_at' => '2000-01-01 00:00:01'
        ]);

        // Medium priority
        Todo::create([
            'id' => 2,
            'user_id' => 1,
            'task' => 'Mid effort',
            'description' => 'Test task mid',
            'start_date' => '2000-01-01',
            'end_date' => '2027-01-01',
            'is_completed' => False,
            'completed_at' => NULL,
            'priority' => 2,
            'status' => 2,
            'created_at' => '2000-01-01 00:00:01',
            'updated_at' => '2000-01-01 00:00:01'
        ]);

        // High priority
        Todo::create([
            'id' => 3,
            'user_id' => 1,
            'task' => 'High effort',
            'description' => 'Test task high',
            'start_date' => '2000-01-01',
            'end_date' => '2026-12-12',
            'is_completed' => False,
            'completed_at' => NULL,
            'priority' => 3,
            'status' => 2,
            'created_at' => '2000-01-01 00:00:01',
            'updated_at' => '2000-01-01 00:00:01'
        ]);
            
        // Completed
        Todo::create([
            'id' => 4,
            'user_id' => 1,
            'task' => 'High effort',
            'description' => 'Test task high',
            'start_date' => '2000-01-01',
            'end_date' => '2020-12-12',
            'is_completed' => True,
            'completed_at' => NULL,
            'priority' => 3,
            'status' => 2,
            'created_at' => '2000-01-01 00:00:01',
            'updated_at' => '2000-01-01 00:00:01'
        ]);


        // Achievement Seeder

        // Registered
        Achievement::create([
            'name' => 'First Time Joined',
            'description' => 'You\'ve Registered!',
            'icon' => 'registered.png',
            'type' => 'registered',
            'target_value' => 1
        ]);

        // Tasks Created
        Achievement::create([
            'name' => 'First Task',
            'description' => 'You\'ve Created Your First Task!',
            'icon' => 'tasks_created.png',
            'type' => 'tasks_created',
            'target_value' => 1
        ]);

        Achievement::create([
            'name' => 'Five Tasks',
            'description' => 'You\'ve Created Five Tasks!',
            'icon' => 'tasks_created.png',
            'type' => 'tasks_created',
            'target_value' => 5
        ]);

        Achievement::create([
            'name' => 'Ten Tasks',
            'description' => 'You\'ve Created Ten Tasks!',
            'icon' => 'tasks_created.png',
            'type' => 'tasks_created',
            'target_value' => 10
        ]);


        // Tasks Completed
        Achievement::create([
            'name' => 'First Task Completed',
            'description' => 'You\'ve Completed Your First Task!',
            'icon' => 'tasks_completed.png',
            'type' => 'tasks_completed',
            'target_value' => 1
        ]);

        Achievement::create([
            'name' => 'Five Tasks Completed',
            'description' => 'You\'ve Completed Five Tasks!',
            'icon' => 'tasks_completed.png',
            'type' => 'tasks_completed',
            'target_value' => 5
        ]);

        Achievement::create([
            'name' => 'Ten Tasks Completed',
            'description' => 'You\'ve Completed Ten Tasks!',
            'icon' => 'tasks_completed.png',
            'type' => 'tasks_completed',
            'target_value' => 10
        ]);


        // Streak
        Achievement::create([
            'name' => '3 Days in a Row',
            'description' => '3 Consecutive day login!',
            'icon' => 'streak_count.png',
            'type' => 'streak_count',
            'target_value' => 3
        ]);

        Achievement::create([
            'name' => '7 Days in a Row',
            'description' => '7 Consecutive day login!',
            'icon' => 'streak_count.png',
            'type' => 'streak_count',
            'target_value' => 7
        ]);

        Achievement::create([
            'name' => '30 Days in a Row',
            'description' => '30 Consecutive day login!',
            'icon' => 'streak_count.png',
            'type' => 'streak_count',
            'target_value' => 30
        ]);
    }
}
