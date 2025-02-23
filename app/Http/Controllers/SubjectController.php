<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Exception;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        return response()->json(Subject::all());
    }

    public function show($id)
    {
        $subject = Subject::find($id);
        if (!$subject) {
            return response()->json(['message' => 'Mata pelajaran tidak ditemukan'], 404);
        }
        return response()->json($subject);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        Subject::create($request->all());
        return response()->json(['message' => 'Mata pelajaran berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $subject = Subject::findOrFail($id);
            $subject->update([
                'name' => $request->name
            ]);

            return response()->json([
                'message' => 'Mata pelajaran berhasil diperbarui',
                'subject' => $subject
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error updating subject',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy($id)
    {
        Subject::findOrFail($id)->delete();
        return response()->json(['message' => 'Mata pelajaran berhasil dihapus']);
    }
}
