<?php

namespace Database\Seeders\Entity;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Entity\Attribute;
use App\Models\User;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            'state' => 'playable',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
        ];

        $attributes = [
            [
                'name' => 'Force',
                'description' => 'Mesure la puissance physique et la capacité à infliger des dégâts.',
                'image' => null,
            ],
            [
                'name' => 'Intelligence',
                'description' => 'Mesure la capacité à comprendre, apprendre et utiliser la magie.',
                'image' => null,
            ],
            [
                'name' => 'Agilité',
                'description' => 'Mesure la rapidité, la souplesse et la capacité à esquiver.',
                'image' => null,
            ],
            [
                'name' => 'Chance',
                'description' => 'Mesure la capacité à provoquer des événements favorables.',
                'image' => null,
            ],
            [
                'name' => 'Sagesse',
                'description' => 'Mesure l\'expérience, la réflexion et la résistance mentale.',
                'image' => null,
            ],
            [
                'name' => 'Vitalité',
                'description' => 'Mesure la santé et la résistance physique.',
                'image' => null,
            ],
        ];
        foreach ($attributes as $attr) {
            Attribute::create(array_merge($defaults, $attr));
        }
    }
}
