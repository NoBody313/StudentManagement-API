<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        return response()->json(Attendance::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'status' => 'required|in:hadir,izin,sakit,alpha'
        ]);

        Attendance::create($request->all());
        return response()->json(['message' => 'Absensi berhasil ditambahkan']);
    }
}
