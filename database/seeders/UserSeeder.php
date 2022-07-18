<?php

namespace Database\Seeders;

use App\Enums;
use App\Models;
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
        User::factory()
            ->for(Models\Role::whereSlug(Enums\Role::Store)->first())
            ->count(10)
            ->create();

        User::factory()
            ->for(Models\Role::whereSlug(Enums\Role::Person)->first())
            ->count(10)
            ->create(['balance' => 100]);
    }
}
