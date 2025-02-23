<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = ['class_id', 'teacher_id', 'subject_id', 'day', 'start_time', 'end_time'];

    public function class(): BelongsTo {
        return $this->belongsTo(Classes::class);
    }

    public function teacher(): BelongsTo {
        return $this->belongsTo(Teacher::class);
    }

    public function subject(): BelongsTo {
        return $this->belongsTo(Subject::class);
    }
}
