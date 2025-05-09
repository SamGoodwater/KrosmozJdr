<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Classe extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'official_id',
        'dofusdb_id',
        'uniqid',
        'name',
        'description_fast',
        'description',
        'passif',
        'life',
        'life_dice',
        'specificity',
        'usable',
        'dofus_version',
        'is_visible',
        'created_by',
        'image',
        'icon',
        'auto_update',
    ];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function spells(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Spell::class);
    }

    public function capabilities(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Capability::class);
    }

    public function attributes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Attribute::class);
    }

    public function npcs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Npc::class);
    }

    public function imagePath(): string
    {
        return Storage::disk('modules')->url($this->image);
    }
    public function iconPath(): string
    {
        return Storage::disk('modules')->url($this->icon);
    }
}
