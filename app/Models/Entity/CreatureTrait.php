<?php

namespace App\Models\Entity;

use App\Models\Concerns\HasEntityImageMedia;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

/** Référentiel des traits permanents de créature. */
class CreatureTrait extends Model implements HasMedia
{
    /** @use HasFactory<\\Database\\Factories\\Entity\\CreatureTraitFactory> */
    use HasEntityImageMedia, HasFactory, SoftDeletes;
    public const STATE_RAW = 'raw'; public const STATE_DRAFT = 'draft'; public const STATE_PLAYABLE = 'playable'; public const STATE_ARCHIVED = 'archived';
    public const MEDIA_PATH = 'images/entity/creature-traits';
    public const MEDIA_FILE_PATTERN_IMAGES = 'image-[id]-[name]';
    protected $fillable = ['name','description','state','read_level','write_level','image','created_by'];
    protected $casts = ['read_level'=>'integer','write_level'=>'integer'];
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
    public function creatures() { return $this->belongsToMany(Creature::class, 'creature_creature_trait'); }
    public function breeds() { return $this->belongsToMany(Breed::class, 'breed_creature_trait')->withPivot('level')->withTimestamps(); }
    public function specializations() { return $this->belongsToMany(Specialization::class, 'creature_trait_specialization')->withPivot('level')->withTimestamps(); }
}
