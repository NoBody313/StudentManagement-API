<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Grade;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function storeGrade(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'score' => 'required|integer|min:0|max:100'
        ]);

        Grade::create($request->all());
        return response()->json(['message' => 'Nilai berhasil ditambahkan']);
    }

    // Input Absensi Siswa
    public function storeAttendance(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'status' => 'required|in:hadir,izin,sakit,alpha'
        ]);

        Attendance::create($request->all());
        return response()->json(['message' => 'Absensi berhasil dicatat']);
    }
}
