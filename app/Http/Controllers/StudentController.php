<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Father;
use App\Models\Grade;
use App\Models\Mother;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $students = Student::with(['father', 'mother'])->get();
            return response()->json([
                'message' => 'All students found',
                'data' => $students
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error fetching students', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email',
                'password' => 'required',
                'nis' => 'required|string',
                'nisn' => 'required|string',
                'class_id' => 'required|exists:classes,id',
                'date_of_birth' => 'required|date',
                'gender' => 'required',
                'father.name' => 'required|string',
                'father.phone_number' => 'required|string',
                'father.born_place' => 'required|string',
                'father.born_date' => 'required|date',
                'father.occupation' => 'required|string',
                'father.address' => 'required|string',
                'mother.name' => 'required|string',
                'mother.phone_number' => 'required|string',
                'mother.born_place' => 'required|string',
                'mother.born_date' => 'required|date',
                'mother.occupation' => 'required|string',
                'mother.address' => 'required|string',
            ]);
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'siswa'
            ]);

            $father = Father::create($request->input('father'));
            $mother = Mother::create($request->input('mother'));

            $student = Student::create([
                'user_id' => $user->id,
                'nis' => $request->nis,
                'nisn' => $request->nisn,
                'class_id' => $request->class_id,
                'date_of_birth' => $request->date_of_birth,
                'place_of_birth' => $request->place_of_birth,
                'gender' => $request->gender,
                'father_id' => $father->id,
                'mother_id' => $mother->id,
            ]);

            return response()->json([
                'message' => 'Student, User, and Parents created successfully',
                'user' => $user,
                'student' => $student->load('class'),
                'father' => $father,
                'mother' => $mother,
            ], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error creating student', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $student = Student::with(['father', 'mother'])->find($id);

            if (!$student) {
                return response()->json(['message' => 'Student not found'], 404);
            }

            return response()->json(['message' => 'Student found', 'data' => $student]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error fetching student', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $student = Student::find($id);

            if (!$student) {
                return response()->json(['message' => 'Student not found'], 404);
            }

            $student->update($request->all());

            return response()->json(['message' => 'Student updated', 'data' => $student]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error updating student', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $student = Student::find($id);

            if (!$student) {
                return response()->json(['message' => 'Student not found'], 404);
            }

            $student->delete();

            return response()->json(['message' => 'Student deleted']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error deleting student', 'error' => $e->getMessage()], 500);
        }
    }

    public function showSchedule()
    {
        $student = Auth::user()->student;
        return response()->json(Schedule::where('class_id', $student->class_id)->get());
    }

    public function showGrades()
    {
        $student = Auth::user()->student;
        return response()->json(Grade::where('student_id', $student->id)->get());
    }

    public function showAttendance()
    {
        $student = Auth::user()->student;
        return response()->json(Attendance::where('student_id', $student->id)->get());
    }
}
