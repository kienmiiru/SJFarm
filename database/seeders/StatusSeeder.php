<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Status::insert([
            ['id' => 1, 'status' => 'pending'],
            ['id' => 2, 'status' => 'approved'],
            ['id' => 3, 'status' => 'rejected'],
        ]);
    }
}
