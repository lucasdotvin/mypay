<?php

namespace Database\Seeders;

use App\Enums;
use App\Models;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Enums\Role::cases() as $item) {
            Models\Role::updateOrCreate(
                ['slug' => $item],
                [],
            );
        }
    }
}
