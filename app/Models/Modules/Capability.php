<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Capability extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'effect',
        'level',
        'pa',
        'po',
        'po_editable',
        'time_before_use_again',
        'casting_time',
        'duration',
        'element',
        'is_magic',
        'ritual_available',
        'powerful',
        'usable',
        'is_visible',
        'created_by',
        'image'
    ];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function specializations(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Specialization::class);
    }

    public function classes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Classe::class);
    }

    public function mobs(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Mob::class);
    }

    public function npcs(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Npc::class);
    }

    public function imagePath(): string
    {
        return Storage::disk('modules')->url($this->image);
    }
}
