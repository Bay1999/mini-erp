<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hasUser = User::where("email", "admin@mail.com")->exists();

        if (!$hasUser) {
            User::create([
                "name" => "Administrator",
                "email" => "admin@mail.com",
                "password" => Hash::make('123321'),
                "remember_token" => Str::random(60),
                "email_verified_at" => now(),
            ]);
        }

        $hasOperator = User::where("email", "operator@mail.com")->exists();

        if (!$hasOperator) {
            User::create([
                "name" => "Operator",
                "email" => "operator@mail.com",
                "password" => Hash::make('123321'),
                "remember_token" => Str::random(60),
                "email_verified_at" => now(),
            ]);
        }
    }
}
