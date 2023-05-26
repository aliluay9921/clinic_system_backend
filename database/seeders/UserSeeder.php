<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            "user_name" => "ali_luay",
            "name" => "ali luay",
            "password" => bcrypt("11111111"),
            "user_type" => 0,
            "phone_number" => "009647713982401",
        ]);
    }
}
