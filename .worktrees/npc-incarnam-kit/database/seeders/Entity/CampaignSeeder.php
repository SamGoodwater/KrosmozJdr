<?php

namespace Database\Seeders\Entity;

use App\Models\Entity\Campaign;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Campaign::factory(5)->create();
    }
}
