<?php

namespace Database\Seeders\Entity;

use App\Models\Entity\Breed;
use App\Models\Entity\Creature;
use App\Models\Entity\Npc;
use App\Models\User;
use App\Support\Creature\CreatureSize;
use App\Support\Npc\NpcRole;
use Illuminate\Database\Seeder;

/**
 * PNJ locaux de démonstration (sans réseau).
 */
class NpcSeeder extends Seeder
{
    public function run(): void
    {
        $authorId = User::query()->where('role', '>=', User::ROLE_ADMIN)->value('id')
            ?? User::query()->value('id');

        $iop = Breed::query()->where('name', 'like', '%Iop%')->first();

        $this->makeNpc([
            'name' => 'Garde Iop d’Astrub',
            'description' => 'Sentinelle de la milice, brusque mais loyal.',
            'location' => 'Astrub',
            'hostility' => 2,
            'level' => '8',
            'npc_role' => NpcRole::GUARD,
            'size' => CreatureSize::MOYEN,
            'breed_id' => $iop?->id,
            'story' => 'Il tient le pont depuis trop d’étés pour se souvenir du premier.',
        ], $authorId);

        $this->makeNpc([
            'name' => 'Marchande de Bonta',
            'description' => 'Tenancière d’étal, sans classe de combat.',
            'location' => 'Bonta',
            'hostility' => 1,
            'level' => '4',
            'npc_role' => NpcRole::MERCHANT,
            'size' => CreatureSize::MOYEN,
            'breed_id' => null,
            'story' => 'Elle vend plus de rumeurs que de tissus.',
        ], $authorId);

        $this->makeNpc([
            'name' => 'Aubergiste du Coq de Brume',
            'description' => 'Accueille les voyageurs, oriente les quêtes.',
            'location' => 'Amakna',
            'hostility' => 0,
            'level' => '3',
            'npc_role' => NpcRole::SOCIAL,
            'size' => CreatureSize::MOYEN,
            'breed_id' => null,
            'historical' => 'L’auberge existe depuis la première foire aux Bouftous.',
        ], $authorId);
    }

    /**
     * @param  array<string, mixed>  $attrs
     */
    private function makeNpc(array $attrs, ?int $authorId): void
    {
        if (Npc::query()->whereHas('creature', fn ($q) => $q->where('name', $attrs['name']))->exists()) {
            return;
        }

        $creature = Creature::factory()->create([
            'name' => $attrs['name'],
            'description' => $attrs['description'] ?? '',
            'location' => $attrs['location'] ?? null,
            'hostility' => $attrs['hostility'] ?? 2,
            'level' => $attrs['level'] ?? '1',
            'state' => Npc::STATE_PLAYABLE,
            'created_by' => $authorId,
        ]);

        Npc::factory()->create([
            'creature_id' => $creature->id,
            'npc_role' => $attrs['npc_role'] ?? null,
            'size' => $attrs['size'] ?? CreatureSize::MOYEN,
            'breed_id' => $attrs['breed_id'] ?? null,
            'story' => $attrs['story'] ?? null,
            'historical' => $attrs['historical'] ?? null,
            'state' => Npc::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);
    }
}
