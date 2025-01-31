<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArrayResource;
use App\Http\Resources\ListResource;
use App\Models\Attendance;
use App\Models\Shifts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    public function create(Request $request)
    {
        try {
            $id_karyawan = $request->nama;
            $date = now();

            $attendance = Attendance::where('id_karyawan', $id_karyawan)
                ->whereDate('created_at', $date)
                ->first();


            if (!$attendance) {
                $validator = Validator::make($request->all(), [
                    'checkin_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'checkin_location' => 'required',
                ]);

                if ($validator->fails()) {
                    return new ArrayResource(false, $validator->messages()->all(), null);
                }

                $images = $request->file('checkin_image');
                $images->storeAs('public/presensi', $images->hashName());
                $shift = Shifts::find($request->shift);
                if (!$shift) {
                    return new ArrayResource(false, 'Shift anda tidak ditemukan', null);
                }


                $jamShiftString = $shift->waktu_masuk;
                $jamShiftParts = explode(":", $jamShiftString);
                $shiftTime = now()->setHour((int)$jamShiftParts[0])->setMinute((int)$jamShiftParts[1])->setSecond(0);
                
                $allowedCheckInStartTime = $shiftTime->subHour();
                $maxCheckInTime = $shiftTime->addHours(3);
                $currentTime = now();
               // if ($currentTime->lessThan($allowedCheckInStartTime)) {
               //     return new ArrayResource(false, 'Presensi hanya dapat dilakukan mulai 1 jam sebelum waktu masuk shift.', null);
               // }
                if ($currentTime->greaterThan($maxCheckInTime)) {
                    return new ArrayResource(false, 'Presensi tidak dapat dilakukan lebih dari 3 jam setelah waktu masuk shift.', null);
                }

                $jamShiftDuration = (int)$jamShiftParts[0] * 60 + (int)$jamShiftParts[1];
                $jamMasukDuration = $currentTime->hour * 60 + $currentTime->minute;
                $selisih = $jamMasukDuration - $jamShiftDuration;

                $status = ($selisih > 15) ? 'terlambat' : 'tepat waktu';

                $attendance = Attendance::create([
                    'id_karyawan'  => $id_karyawan,
                    'checkin_location' => $request->checkin_location,
                    'checkin_image' =>'presensi/'. $images->hashName(),
                    'status'  => $status,
                    'id_device' => $request->id_device,
                    'checkout_location' => '',
                    'checkout_image' => '',
                    'durasi_kerja' => '',
                    'id_office' => $request->office,
                    'id_shift' => $request->shift
                ]);
                $attendance->makeHidden('id_office');
                $attendance->makeHidden('id_shift');
                $attendance->makeHidden('id_karyawan');
                $attendance->checkin_image = $attendance->checkin_image != null ? url('storage/' .  $attendance->checkin_image) : null;
                return new ArrayResource(true, 'Presensi masuk Berhasil', $attendance);
            }

            if ($attendance) {

                if ($attendance->checkout_location != '') {
                    return new ArrayResource(false, 'Anda sudah melakukan absensi hari ini', null);
                }

                $validator = Validator::make($request->all(), [
                    'checkout_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'checkout_location' => 'required',
                    'nama' => 'required',
                ]);

                if ($validator->fails()) {
                    return new ArrayResource(false, $validator->messages()->all(), null);
                }

                $images = $request->file('checkout_image');
                $images->storeAs('public/presensi', $images->hashName());

                $checkInTime = $attendance->created_at;
                $checkOutTime = now();
                $diff = $checkOutTime->diff($checkInTime);
                $durasi = sprintf('%d jam %d menit', $diff->h, $diff->i);

                $attendance->update([
                    'checkout_location' => $request->checkout_location,
                    'checkout_image' => 'presensi/'. $images->hashName(),
                    'id_device' => $request->id_device,
                    'durasi_kerja' => $durasi,
                ]);

                $attendance->makeHidden('id_office');
                $attendance->makeHidden('id_shift');
                $attendance->makeHidden('id_karyawan');
                $attendance->checkout_image = $attendance->checkout_image != null ? url('storage/' .  $attendance->checkout_image) : null;
                return new ArrayResource(true, 'Presensi keluar Berhasil', $attendance);
            }
        } catch (\Throwable $th) {
            return new ArrayResource(false, $th, null);
        }
    }

    public function get($id_karyawan)
    {
        try {
            $data = Attendance::where('id_karyawan', $id_karyawan)
                ->select('id', 'created_at', 'updated_at', 'durasi_kerja', 'status')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            $data->getCollection()->transform(function ($item) {
                return  $item->makeHidden('id_karyawan');
            });
            return new ListResource(true, 'histori absensi anda', $data);
        } catch (\Throwable $th) {
            return new ArrayResource(false, $th, null);
        }
    }

    public function detail($id)
    {
        try {
            $data = Attendance::with(
                [
                    'office' => function ($query) {
                        $query->select('id', 'nama');
                    },
                    'shift' => function ($query) {
                        $query->select('id', 'nama');
                    },
                    'karyawan' => function ($query) {
                        $query->select('id', 'nama');
                    },
                ]
            )->findOrFail($id);

            $data->makeHidden(['id_office', 'id_shift', 'id_karyawan']);

            $data->checkin_image = $data->checkin_image != null ? url('storage/' .  $data->checkin_image) : null;
            $data->checkout_image = $data->checkout_image != null ? url('storage/' .  $data->checkout_image) : null;
            $response = [
                'id' => $data->id,
                'checkin_location' => $data->checkin_location,
                'checkout_location' => $data->checkout_location,
                'checkin_image' => $data->checkin_image,
                'checkout_image' => $data->checkout_image,
                'durasi_kerja' => $data->durasi_kerja,
                'id_device' => $data->id_device,
                'status' => $data->status,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
                'office' => $data->office->nama,
                'shift' => $data->shift->nama,
                'nama' => $data->karyawan->nama,
            ];

            return new ArrayResource(true, 'histori absensi anda', $response);
        } catch (\Throwable $th) {
            dd($th);
            return new ArrayResource(false, $th, null);
        }
    }
}
