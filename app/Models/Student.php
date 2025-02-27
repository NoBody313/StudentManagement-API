<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'nis',
        'nisn',
        'class_id',
        'date_of_birth',
        'place_of_birth',
        'gender',
        'father_id',
        'mother_id',
    ];

    public function father()
    {
        return $this->belongsTo(Father::class);
    }

    public function mother()
    {
        return $this->belongsTo(Mother::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function classes(): BelongsTo {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function grades(): HasMany {
        return $this->hasMany(Grade::class);
    }

    public function attendance(): HasMany {
        return $this->hasMany(Attendance::class);
    }
}
