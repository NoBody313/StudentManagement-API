<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'mentor', 'date', 'time'];

    // public function classes()
    // {
    //     return $this->belongsTo(Classes::class, 'class_id');
    // }

    // public function teacher(): BelongsTo
    // {
    //     return $this->belongsTo(Teacher::class);
    // }

    // public function subject(): BelongsTo
    // {
    //     return $this->belongsTo(Subject::class);
    // }
}
