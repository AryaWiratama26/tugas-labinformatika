<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name', 'code'];

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function modules()
    {
        return $this->hasMany(Module::class);
    }
}
