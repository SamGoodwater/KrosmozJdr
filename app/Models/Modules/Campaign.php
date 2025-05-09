<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use App\Models\Page;
use App\Models\User;

class Campaign extends Model
{
    use HasFactory, SoftDeletes;

    const STATE = [
        'draft' => 0,
        'published' => 1,
        'archived' => 2
    ];

    protected $fillable = [
        'name',
        'description',
        'keyword',
        'slug',
        'state',
        'is_public',
        'uniqid',
        'is_visible',
        'created_by',
        'image'
    ];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function getPathFiles()
    {
        return \DB::table('file_campaign')
            ->where('campaign_id', $this->id)
            ->pluck('file');
    }

    public function setPathFiles(array|string|null $files): void
    {
        if (!$files) {
            return;
        }
        $files = is_array($files) ? $files : [$files];

        $data = array_map(function ($file) {
            return [
                'campaign_id' => $this->id,
                'file' => $file
            ];
        }, $files);

        \DB::table('file_campaign')->insert($data);
    }

    public function pages(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Page::class);
    }

    public function scenarios(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Scenario::class);
    }

    public function mobs(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Mob::class);
    }

    public function npcs(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Npc::class);
    }

    public function spells(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Spell::class);
    }

    public function shops(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Shop::class);
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Item::class);
    }

    public function resources(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Resource::class);
    }

    public function consumables(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Consumable::class);
    }

    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function panoplies(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Panoply::class);
    }

    public function imagePath(): string
    {
        return Storage::disk('modules')->url($this->image);
    }

    public function filesPath(): \Illuminate\Support\Collection
    {
        $files = $this->getPathFiles();
        $files = $files->map(function ($file) {
            return Storage::disk('modules')->url($file);
        });
        return $files;
    }
}
