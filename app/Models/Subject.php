<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name', 'code', 'is_hidden'];

    protected $casts = ['is_hidden' => 'boolean'];

    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false);
    }

    public function scopeHidden($query)
    {
        return $query->where('is_hidden', true);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function modules()
    {
        return $this->hasMany(Module::class);
    }
}
