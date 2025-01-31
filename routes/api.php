<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\IzinController;
use App\Http\Controllers\Api\KaryawanController;
use App\Http\Controllers\Api\OfficeController;
use App\Http\Controllers\Api\ShiftController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::get('/office/get/{id}', [OfficeController::class, 'get']);
Route::get('/shift/get/{id}', [ShiftController::class, 'get']);


Route::post('/karyawan/login', [KaryawanController::class, 'login']);
Route::patch('/karyawan/update/', [KaryawanController::class, 'update']);
Route::patch('/karyawan/updatePassword/', [KaryawanController::class, 'updatePassword']);
Route::patch('/karyawan/sendLocation/', [KaryawanController::class, 'sendLocation']);

Route::post('/izin/create', [IzinController::class, 'create']);
Route::patch('/izin/update/', [IzinController::class, 'update']);
Route::get('/izin/get/{id_karyawan}', [IzinController::class, 'get']);
Route::get('/izin/detail/{id}', [IzinController::class, 'detail']);
Route::delete('/izin/delete/{id}', [IzinController::class, 'delete']);


Route::post('/attendance/create', [AttendanceController::class, 'create']);
Route::get('/attendance/get/{id_karyawan}', [AttendanceController::class, 'get']);
Route::get('/attendance/detail/{id}', [AttendanceController::class, 'detail']);
