<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function indexStudents()
    {
        return response()->json(Student::all());
    }

    public function indexTeachers()
    {
        return response()->json(Teacher::all());
    }
}
