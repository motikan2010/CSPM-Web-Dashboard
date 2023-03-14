<?php

namespace Database\Seeders;

use App\Models\CloudType;
use Illuminate\Database\Seeder;

class CloudTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CloudType::create([
            'id' => 1,
            'name' => 'AWS'
        ]);
    }
}
