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
            ['name' => 'Pekerjaan Kantor', 'slug' => 'kantor', 'color' => 'red'],
            ['name' => 'Sidejobs', 'slug' => 'sidejobs', 'color' => 'green'],
            ['name' => 'Learning', 'slug' => 'learning', 'color' => 'blue'],
            ['name' => 'Another Sidejobs', 'slug' => 'another-sidejobs', 'color' => 'yellow'],
        ];

        foreach ($groups as $group) {
            TaskGroup::updateOrCreate(
                ['slug' => $group['slug']],
                $group
            );
        }
    }
}
