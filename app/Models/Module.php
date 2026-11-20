<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['subject_id', 'name', 'description', 'deadline', 'image_path', 'is_hidden'];

    protected $appends = ['is_expired'];

    protected $casts = [
        'deadline' => 'datetime',
        'is_hidden' => 'boolean',
    ];

    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false);
    }

    public function scopeHidden($query)
    {
        return $query->where('is_hidden', true);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->deadline !== null && now()->isAfter($this->deadline);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
