<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware(JwtMiddleware::class)->group(function () {
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // 🔹 ADMIN
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/admin/students', [AdminController::class, 'indexStudents']);
        Route::get('/admin/teachers', [AdminController::class, 'indexTeachers']);
        Route::apiResource('students', StudentController::class);
        Route::apiResource('classes', ClassController::class);
        Route::apiResource('subjects', SubjectController::class);
        Route::apiResource('schedules', ScheduleController::class);
        Route::apiResource('teachers', TeacherController::class);
    });

    // 🔹 GURU (Nilai & Absensi)
    Route::middleware(['auth', 'role:admin,teacher'])->group(function () {
        Route::apiResource('grades', GradeController::class);
        Route::apiResource('attendance', AttendanceController::class);
    });
    // 🔹 SISWA (Lihat Data Sendiri)
    Route::prefix('student')->middleware(['auth', 'role:student'])->group(function () {
        Route::get('/schedule', [StudentController::class, 'showSchedule']);
        Route::get('/grades', [StudentController::class, 'showGrades']);
        Route::get('/attendance', [StudentController::class, 'showAttendance']);
    });
});
