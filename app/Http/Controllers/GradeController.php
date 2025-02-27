<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index()
    {
        $grades = Grade::with(['student.user', 'subject', 'teacher.user'])->get();

        if ($grades->isEmpty()) {
            return response()->json(['message' => 'Tidak ada data nilai ditemukan'], 404);
        }

        return response()->json([
            'message' => 'Semua data nilai ditemukan',
            'data' => $grades
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'remark' => 'required|string',
            'score' => 'required|integer|min:0|max:100',
            'studentId' => 'required|exists:students,id',
            'subjectId' => 'required|exists:subjects,id',
            'teacherId' => 'required|exists:teachers,id'
        ]);

        Grade::create([
            'remark' => $request->remark,
            'score' => $request->score,
            'student_id' => $request->studentId,
            'subject_id' => $request->subjectId,
            'teacher_id' => $request->teacherId,
        ]);
        return response()->json(['message' => 'Nilai berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'remark' => 'required|string',
            'score' => 'required|integer|min:0|max:100',
            'studentId' => 'required|exists:students,id',
            'subjectId' => 'required|exists:subjects,id',
            'teacherId' => 'required|exists:teachers,id'
        ]);

        $grade = Grade::findOrFail($id);
        $grade->update([
            'remark' => $request->remark,
            'score' => $request->score,
            'student_id' => $request->studentId,
            'subject_id' => $request->subjectId,
            'teacher_id' => $request->teacherId,
        ]);
        return response()->json(['message' => 'Nilai berhasil diperbarui']);
    }

    public function destroy($id)
    {
        Grade::findOrFail($id)->delete();
        return response()->json(['message' => 'Nilai berhasil dihapus']);
    }
}
