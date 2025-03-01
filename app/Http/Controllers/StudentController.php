<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Classes;
use App\Models\Father;
use App\Models\Grade;
use App\Models\Mother;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $students = Student::with(['classes', 'user'])->get();
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
        $request->validate([
            'user.name' => 'required|string',
            'user.email' => 'required|email',
            'user.password' => 'required|string',
            'nis' => 'required|string',
            'nisn' => 'required|string',
            'class_id' => 'nullable|exists:classes,id',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'required|string',
            'gender' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->user['name'],
                'email' => $request->user['email'],
                'password' => Hash::make($request->user['password']),
                'role' => 'siswa',
            ]);

            $student = Student::create([
                'user_id' => $user->id,
                'nis' => $request->nis,
                'nisn' => $request->nisn,
                'class_id' => $request->class_id,
                'date_of_birth' => $request->date_of_birth,
                'place_of_birth' => $request->place_of_birth,
                'gender' => $request->gender,
            ]);

            DB::commit();
            return response()->json([
                'message' => 'Student and User created successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ],
                'student' => [
                    'id' => $student->id,
                    'user_id' => $student->user_id,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                    ],
                    'nis' => $student->nis,
                    'nisn' => $student->nisn,
                    'date_of_birth' => $student->date_of_birth,
                    'place_of_birth' => $student->place_of_birth,
                    'class_id' => $student->class_id,
                    'class' => $student->class,  // Pastikan class data di-include sesuai dengan DTO
                    'gender' => $student->gender,
                    'created_at' => $student->created_at,
                    'updated_at' => $student->updated_at,
                ]
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error creating student', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $student = Student::with(['user', 'classes'])->find($id);

            if (!$student) {
                return response()->json(['message' => 'Student not found'], 404);
            }

            $responseData = [
                'message' => 'Student found',
                'id' => $student->id,
                'user_id' => $student->user_id,
                'nis' => $student->nis,
                'nisn' => $student->nisn,
                'date_of_birth' => $student->date_of_birth,
                'place_of_birth' => $student->place_of_birth,
                'class_id' => $student->class_id,
                'gender' => $student->gender,
                'created_at' => $student->created_at,
                'updated_at' => $student->updated_at,
            ];

            $responseData['user'] = $student->user;
            $responseData['classes'] = $student->classes;

            return response()->json($responseData);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error fetching student', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $student = Student::find($id);

        if ($student) {
            $class = Classes::find($request->input('class_id'));
            if (!$class) {
                return response()->json(['message' => 'Class not found'], 404);
            }
            $student->update($request->only([
                'nis',
                'nisn',
                'date_of_birth',
                'place_of_birth',
                'gender',
                'class_id'
            ]));

            if ($student->user) {
                $student->user->update($request->only(['name', 'password' => bcrypt($request->input('password'))]));
            }

            if ($student->father) {
                $student->father->update($request->only([
                    'name',
                    'phone_number',
                    'born_place',
                    'born_date',
                    'occupation',
                    'address'
                ]));
            }

            if ($student->mother) {
                $student->mother->update($request->only([
                    'name',
                    'phone_number',
                    'born_place',
                    'born_date',
                    'occupation',
                    'address'
                ]));
            }

            return response()->json([
                'message' => 'Student, User, Father, and Mother updated successfully',
                'student' => $student,
                'user' => $student->user
            ]);
        }

        return response()->json(['message' => 'Student not found'], 404);
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

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $schedules = Schedule::with(['classes', 'teacher.user', 'subject'])
            ->where('class_id', $student->class_id)
            ->get();

        return response()->json([
            'schedules' => $schedules
        ]);
    }


    public function showGrades()
    {
        $student = Auth::user()->student;
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $grades = Grade::with(['student.user', 'subject', 'teacher.user'])
            ->where('student_id', $student->id)
            ->get();

        return response()->json([
            'grades' => $grades
        ]);
    }

    public function showAttendance()
    {
        $student = Auth::user()->student;
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $attendances = Attendance::with([
            'student',
            'student.user',
            'schedule.classes',
            'schedule.teacher',
            'schedule.teacher.user',
            'schedule.subject'
        ])
            ->where('student_id', $student->id)
            ->get();

        return response()->json([
            'attendances' => $attendances
        ]);
    }
}
