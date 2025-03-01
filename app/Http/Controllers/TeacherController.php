<?php

namespace App\Http\Controllers;

use App\Http\Resources\TeacherResource;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\Teacher;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Validator;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with('user')->get();
        return response()->json([
            'message' => 'All teachers found',
            'data' => $teachers
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user.name' => 'required|string|max:255',
            'user.email' => 'required|email|unique:users,email',
            'user.password' => 'required|string|min:8',
            'nip' => 'required|unique:teachers,nip',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->user['name'],
            'email' => $request->user['email'],
            'password' => bcrypt($request->user['password']),
            'role' => 'guru',
        ]);

        $teacher = Teacher::create([
            'user_id' => $user->id,
            'nip' => $request->nip,
        ]);

        return new TeacherResource($teacher);
    }


    public function show($id): JsonResponse
    {
        try {
            $teacher = Teacher::with('user')->find($id);
            if (!$teacher) {
                return response()->json(['message' => 'Teacher not found'], 404);
            }
            return response()->json([
                'message' => 'Teacher found',
                'id' => $teacher->id,
                'user_id' => $teacher->user_id,
                'user' => $teacher->user,
                'nip' => $teacher->nip,
                'created_at' => $teacher->created_at,
                'updated_at' => $teacher->updated_at,
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error fetching student', 'error' => $e->getMessage()], 500);
        }
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user.name' => 'required|string|max:255',
            'user.email' => 'required|email|unique:users,email,' . $id,
            'user.password' => 'nullable|string|min:8',
            'nip' => 'required|unique:teachers,nip,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $teacher = Teacher::find($id);
        if (!$teacher) {
            return response()->json(['error' => 'Teacher not found'], 404);
        }

        $user = $teacher->user;

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        $teacher->nip = $request->nip;
        $teacher->save();

        return new TeacherResource($teacher);
    }


    public function destroy($id)
    {
        $teacher = Teacher::find($id);

        if (!$teacher) {
            return response()->json(['message' => 'Teacher not found'], 404);
        }

        $teacher->delete();

        return response()->json(['message' => 'Teacher deleted successfully']);
    }

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
