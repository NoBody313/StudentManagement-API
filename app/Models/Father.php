<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Father extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone_number',
        'born_place',
        'born_date',
        'occupation',
        'address',
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
