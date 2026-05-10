<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['subject_id', 'name', 'description', 'deadline', 'image_path'];

    protected $casts = [
        'deadline' => 'datetime',
    ];

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
