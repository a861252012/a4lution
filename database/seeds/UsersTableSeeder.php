<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class)->create([
            'id' => 1,
            'name' => 'Admin',
            'email' => 'admin@argon.com',
            'role_id' => 1,
            'picture' => '../argon/img/theme/team-3.jpg'
        ]);

        factory(App\User::class)->create([
            'id' => 2,
            'name' => 'Creator',
            'email' => 'creator@argon.com',
            'role_id' => 2,
            'picture' => '../argon/img/theme/team-4.jpg'
        ]);

        factory(App\User::class)->create([
            'id' => 3,
            'name' => 'Member',
            'email' => 'member@argon.com',
            'role_id' => 3,
            'picture' => '../argon/img/theme/team-5.jpg'
        ]);
    }
}
