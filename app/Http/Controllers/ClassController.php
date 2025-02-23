<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        return response()->json(Classes::all());
    }

    public function show($id)
    {
        $classes = Classes::find($id);
        if (!$classes) {
            return response()->json(['message' => 'Kelas tidak ditemukan'], 404);
        }
        return response()->json($classes);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        Classes::create($request->all());
        return response()->json(['message' => 'Kelas berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $class = Classes::findOrFail($id);
        $class->update($request->all());
        return response()->json(['message' => 'Kelas berhasil diperbarui']);
    }

    public function destroy($id)
    {
        Classes::findOrFail($id)->delete();
        return response()->json(['message' => 'Kelas berhasil dihapus']);
    }
}
