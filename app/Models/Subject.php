<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function teachers(): BelongsToMany {
        return $this->belongsToMany(Teacher::class, 'teacher_subject');
    }

    public function grades(): HasMany {
        return $this->hasMany(Grade::class);
    }
}
