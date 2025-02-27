<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedule = Schedule::with(['classes', 'teacher.user', 'subject'])->get();
        return response()->json([
            'message' => 'All schedule found',
            'schedule' => $schedule
        ]);
    }

    public function show($id): JsonResponse
    {
        $schedule = Schedule::with(['classes', 'teacher.user', 'subject'])->find($id);
        if (!$schedule) {
            return response()->json(['message' => 'Jadwal tidak ditemukan'], 404);
        }
        return response()->json($schedule);
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'day' => 'required',
            'start_time' => 'required',
            'end_time' => 'required'
        ]);

        $schedule = Schedule::create($request->all());

        return response()->json([
            'message' => 'Jadwal berhasil ditambahkan',
            'schedule' => $schedule
        ]);
    }

    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        $class = Classes::find($request->input('class_id'));
        $teacher = Teacher::find($request->input('teacher_id'));
        $subject = Subject::find($request->input('subject_id'));

        if (!$class || !$teacher || !$subject) {
            return response()->json(['message' => 'Invalid class, teacher, or subject ID'], 400);
        }

        $schedule->update($request->all());
        return response()->json(['message' => 'Jadwal berhasil diperbarui']);
    }

    public function destroy($id)
    {
        Schedule::findOrFail($id)->delete();
        return response()->json(['message' => 'Jadwal berhasil dihapus']);
    }
}
