<?php

namespace Database\Seeders;

use App\Models\TaskGroup;
use Illuminate\Database\Seeder;

class TaskGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            ['name' => 'Works', 'slug' => 'works', 'color' => 'red'],
        ];

        foreach ($groups as $group) {
            TaskGroup::updateOrCreate(
                ['slug' => $group['slug']],
                $group
            );
        }
    }
}