<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index()
    {
        return response()->json(Grade::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'score' => 'required|integer|min:0|max:100'
        ]);

        Grade::create($request->all());
        return response()->json(['message' => 'Nilai berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $grade = Grade::findOrFail($id);
        $grade->update($request->all());
        return response()->json(['message' => 'Nilai berhasil diperbarui']);
    }

    public function destroy($id)
    {
        Grade::findOrFail($id)->delete();
        return response()->json(['message' => 'Nilai berhasil dihapus']);
    }
}
