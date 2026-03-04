<?php

namespace Database\Seeders;

use App\Models\DiaryEntry;
use App\Models\User;
use Crypt;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory(3)->create(
            ['password' => Hash::make('test')]
        )->each(function (User $user) {
            $user->diaryEntries()->create([
                'title' => Crypt::encryptString('test entry'),
                'description' => Crypt::encryptString("Hello $user->name, test entry"),
                'rating' => 1
            ]);
        });
    }
}
