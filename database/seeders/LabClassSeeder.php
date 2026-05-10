<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LabClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = ['1233D', '1233E', '1231E', '1233F', '1231B', '1231C'];

        foreach ($classes as $class) {
            \App\Models\LabClass::create(['name' => $class]);
        }
    }
}
