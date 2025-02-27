<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with([
            'student.user',
            'schedule.classes',
            'schedule.subject'
        ])->get();
        return response()->json([
            'message' => 'All attendance records found',
            'data' => $attendances
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'schedule_id' => 'required|exists:schedules,id',
            'status' => 'required|in:Present,Absent,Sick,Permission'
        ]);

        $attendance = Attendance::create($request->all());

        return response()->json([
            'message' => 'Absensi berhasil ditambahkan',
            'data' => $attendance
        ]);
    }
}
