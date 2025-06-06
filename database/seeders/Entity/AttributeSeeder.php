<?php

namespace Database\Seeders\Entity;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Entity\Attribute;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributes = [
            [
                'name' => 'Force',
                'description' => 'Mesure la puissance physique et la capacité à infliger des dégâts.',
                'usable' => 1,
                'is_visible' => 'guest',
                'image' => null,
            ],
            [
                'name' => 'Intelligence',
                'description' => 'Mesure la capacité à comprendre, apprendre et utiliser la magie.',
                'usable' => 1,
                'is_visible' => 'guest',
                'image' => null,
            ],
            [
                'name' => 'Agilité',
                'description' => 'Mesure la rapidité, la souplesse et la capacité à esquiver.',
                'usable' => 1,
                'is_visible' => 'guest',
                'image' => null,
            ],
            [
                'name' => 'Chance',
                'description' => 'Mesure la capacité à provoquer des événements favorables.',
                'usable' => 1,
                'is_visible' => 'guest',
                'image' => null,
            ],
            [
                'name' => 'Sagesse',
                'description' => 'Mesure l\'expérience, la réflexion et la résistance mentale.',
                'usable' => 1,
                'is_visible' => 'guest',
                'image' => null,
            ],
            [
                'name' => 'Vitalité',
                'description' => 'Mesure la santé et la résistance physique.',
                'usable' => 1,
                'is_visible' => 'guest',
                'image' => null,
            ],
        ];
        foreach ($attributes as $attr) {
            Attribute::create($attr);
        }
    }
}
