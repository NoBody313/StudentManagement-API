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
        $schedule = Schedule::get();
        return response()->json([
            'message' => 'All schedule found',
            'schedule' => $schedule
        ]);
    }

    public function show($id): JsonResponse
    {
        $schedule = Schedule::find($id);
        if (!$schedule) {
            return response()->json(['message' => 'Jadwal tidak ditemukan'], 404);
        }
        return response()->json($schedule);
    }

    public function store(Request $request)
    {
        // $request->validate([
        //     'day' => 'required',
        //     'start_time' => 'required',
        //     'end_time' => 'required'
        // ]);

        $schedule = Schedule::create($request->all());

        return response()->json([
            'message' => 'Jadwal berhasil ditambahkan',
            'schedule' => $schedule
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $schedule = Schedule::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Jadwal tidak ditemukan'], 404);
        }
        return response()->json(['message' => 'Jadwal berhasil diperbarui']);
    }

    public function destroy($id)
    {
        Schedule::findOrFail($id)->delete();
        return response()->json(['message' => 'Jadwal berhasil dihapus']);
    }
}
