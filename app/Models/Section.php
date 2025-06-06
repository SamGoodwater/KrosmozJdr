<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Page;
use App\Models\File;

/**
 * Modèle Eloquent Section
 * 
 * Représente une section dynamique appartenant à une page (bloc de contenu, composant Vue).
 * Gère l'ordre, le type, les paramètres dynamiques, la visibilité, l'état, les utilisateurs et fichiers associés.
 * Utilisé pour la construction flexible des pages et la gestion fine des droits d'accès.
 * 
 * Relations : page, users, files, createdBy
 *
 * @property int $id
 * @property int $page_id
 * @property int $order
 * @property string $type
 * @property array<array-key, mixed> $params
 * @property string $is_visible
 * @property string $state
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\File> $files
 * @property-read int|null $files_count
 * @property-read \App\Models\Page $page
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\SectionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section withoutTrashed()
 * @mixin \Eloquent
 */
class Section extends Model
{
    /** @use HasFactory<\\Database\\Factories\\SectionFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Les états possibles pour une section.
     */
    const STATES = [
        'brouillon' => 0,
        'prévisualisation' => 1,
        'publié' => 2,
        'archivé' => 3,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'page_id',
        'order',
        'type',
        'params',
        'is_visible',
        'state',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order' => 'integer',
        'params' => 'array',
    ];

    /**
     * Get the user that created the section.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the page that owns the section.
     */
    public function page()
    {
        return $this->belongsTo(Page::class, 'page_id');
    }

    /**
     * Les utilisateurs associés à cette section.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'section_user');
    }

    /**
     * Les fichiers liés à la section, triés par ordre.
     */
    public function files()
    {
        return $this->belongsToMany(File::class, 'file_section')
            ->withPivot('order')
            ->orderBy('file_section.order');
    }
}
