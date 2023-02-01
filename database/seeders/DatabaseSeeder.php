<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Type;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $user = User::factory()->create([
            'name'=> 'Piotrek S'
        ]);
        Course::factory(4)->create([
            'user_id' => $user->id
        ]);

    }
}
