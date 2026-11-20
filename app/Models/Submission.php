<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = [
        'subject_id',
        'module_id',
        'lab_class_id',
        'student_name',
        'student_nim',
        'file_name',
        'file_path',
        'google_drive_id',
        'file_size',
        'score',
        'feedback',
        'is_hidden',
        'graded_at',
        'graded_by',
    ];

    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false);
    }

    public function scopeHidden($query)
    {
        return $query->where('is_hidden', true);
    }

    protected $casts = [
        'score' => 'decimal:2',
        'graded_at' => 'datetime',
        'is_hidden' => 'boolean',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function labClass()
    {
        return $this->belongsTo(LabClass::class);
    }
}
