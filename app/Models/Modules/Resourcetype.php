<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;

class Resourcetype extends Model
{
    protected $fillable = [
        'name',
        'uniqid',
    ];
    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function resources()
    {
        return $this->hasMany(Resource::class, 'resourcetpe_id', 'id');
    }
}
