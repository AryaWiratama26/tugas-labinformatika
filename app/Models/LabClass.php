<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabClass extends Model
{
    protected $fillable = ['name', 'is_hidden'];

    protected $casts = ['is_hidden' => 'boolean'];

    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false);
    }

    public function scopeHidden($query)
    {
        return $query->where('is_hidden', true);
    }
}
