<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Modules\Specialization;
use App\Models\Modules\Scenario;
use App\Models\Modules\Campaign;
use Illuminate\Support\Facades\Storage;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'keyword',
        'slug',
        'order_num',
        "page_id",
        'is_dropdown',
        'is_public',
        "is_visible",
        'is_editable',
        "uniqid",
        'created_by',
        'image',

    ];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at'];


    public function page(): \Illuminate\Database\Eloquent\Relations\BelongsTo | int
    {
        if ($this->page_id && $this->page_id > 0 && is_int($this->page_id)) {
            return $this->belongsTo(Page::class);
        } else {
            return $this->page_id;
        }
    }

    public function sections(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Section::class)->orderBy('order_num');
    }

    public function campaigns(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Campaign::class);
    }

    public function scenarios(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Scenario::class);
    }

    public function specialization(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }

    public function imagePath(): string
    {
        return Storage::url($this->image);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
