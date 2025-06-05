<?php

namespace Database\Seeders;

use App\Models\Request;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $requests = Request::factory()->count(100)->make()->sortBy('requested_date')->values();
        foreach ($requests as $request) {
            $request->save();
        }
    }
}
