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
            $students = Student::with(['father', 'mother', 'classes', 'user'])->get();
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
            'name' => 'required|string',
            'user.email' => 'required|email',
            'user.password' => 'required',
            'nis' => 'required|string',
            'nisn' => 'required|string',
            'class_id' => 'nullable|exists:classes,id',
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

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->input('user.email'),
                'password' => Hash::make($request->input('user.password')),
                'role' => 'siswa'
            ]);

            $father = Father::create($request->input('father'));
            $mother = Mother::create($request->input('mother'));

            $student = Student::create([
                'user_id' => $user->id,
                'nis' => $request->nis,
                'nisn' => $request->nisn,
                'class_id' => $request->input('classId'),
                'date_of_birth' => $request->date_of_birth,
                'place_of_birth' => $request->input('placeOfBirth'),
                'gender' => $request->gender,
                'father_id' => $father->id,
                'mother_id' => $mother->id,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Student, User, and Parents created successfully',
                'user' => $user,
                'student' => [
                    'id' => $student->id,
                    'user_id' => $student->user_id,
                    'user' => $user,
                    'nis' => $student->nis,
                    'nisn' => $student->nisn,
                    'date_of_birth' => $student->date_of_birth,
                    'place_of_birth' => $student->place_of_birth,
                    'class_id' => $student->class_id,
                    'class' => $student->class,
                    'gender' => $student->gender,
                    'father_id' => [
                        'id' => $father->id,
                        'name' => $father->name,
                        'phone_number' => $father->phone_number,
                        'born_place' => $father->born_place,
                        'born_date' => $father->born_date,
                        'occupation' => $father->occupation,
                        'address' => $father->address,
                    ],
                    'mother_id' => [
                        'id' => $mother->id,
                        'name' => $mother->name,
                        'phone_number' => $mother->phone_number,
                        'born_place' => $mother->born_place,
                        'born_date' => $mother->born_date,
                        'occupation' => $mother->occupation,
                        'address' => $mother->address,
                    ],
                    'created_at' => $student->created_at,
                    'updated_at' => $student->updated_at,
                ],
                'father' => $father,
                'mother' => $mother,
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error creating student', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $student = Student::with(['father', 'mother', 'user', 'classes'])->find($id);

            if (!$student) {
                return response()->json(['message' => 'Student not found'], 404);
            }

            return response()->json([
                'message' => 'Student found',
                'id' => $student->id,
                'user_id' => $student->user_id,
                'user' => $student->user,
                'nis' => $student->nis,
                'nisn' => $student->nisn,
                'date_of_birth' => $student->date_of_birth,
                'place_of_birth' => $student->place_of_birth,
                'class_id' => $student->class_id,
                'gender' => $student->gender,
                'father_id' => $student->father->id,
                'mother_id' => $student->mother->id,
                'created_at' => $student->created_at,
                'updated_at' => $student->updated_at,
                'user' => $student->user,
                'father' => $student->father,
                'mother' => $student->mother,
            ]);
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
