<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArrayResource;
use App\Models\Karyawan;
use App\Models\KaryawanLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Str;

use function Illuminate\Log\log;

class KaryawanController extends Controller
{


    public function login(Request $request)
    {

        try {

            $validator = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email',
                    'password' => ['required',],
                ],
                [
                    'email.required' => 'Email wajib diisi.',
                    'email.email' => 'Format email tidak valid.',
                    'password.required' => 'Password wajib diisi.',
                ]
            );

            if ($validator->fails()) {
                return new ArrayResource(false, $validator->messages()->all(), null);
            }

            $email = $request->email;
            $password = $request->password;
            $karyawan = Karyawan::where('email', $email)->first();

            if ($karyawan == null) {
                return new ArrayResource(false, 'email anda tidak valid', null);
            }


            if (strlen($password) === 60 && (substr($password, 0, 4) === '$2y$' || substr($password, 0, 4) === '$2b$')) {
                if ($karyawan->password != $password) {
                    return new ArrayResource(false, 'password anda tidak valid', null);
                }
            } else {
                if (!Hash::check($password, $karyawan->password)) {
                    return new ArrayResource(false, 'password anda tidak valid', null);
                }
            }




            if ($karyawan->id_device == null) {
                $karyawan->update([
                    'id_device' => $request->id_device
                ]);
            }
            $karyawan->wfa = $karyawan->wfa == 1 ? true : false;
            $karyawan->foto = $karyawan->foto != null ? url('storage/' . $karyawan->foto) : null;

            return new ArrayResource(true, 'Login berhasil', $karyawan);
        } catch (\Throwable $th) {
            return new ArrayResource(false, $th, null);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'password' => 'required',
                    'new_password' => 'required'
                ]
            );

            if ($validator->fails()) {
                return new ArrayResource(false, $validator->messages()->all(), null);
            }

            $id = $request->id;
            $password = $request->password;
            $newPassword = $request->new_password;
            $data = Karyawan::findOrFail($id);

            if (!Hash::check($password, $data->password)) {
                return new ArrayResource(false, 'tidak dapat merubah password. password lama anda tidak valid', null);
            }
            $data->update([
                'password' => $newPassword
            ]);
            $data->save();
            $data->wfa = $data->wfa == 1 ? true : false;
            $data->foto = $data->foto != null ? url('storage/' . $data->foto) : null;
            return new ArrayResource(true, 'password anda berhasil di update', $data);
        } catch (\Throwable $th) {
            return new ArrayResource(false, $th, null);
        }
    }
    public function update(Request $request)
    {
        try {
            $id = $request->id;
            $validator = Validator::make(
                $request->all(),
                [
                    'email' => 'nullable|string|email|max:255|unique:karyawans',
                    'no_telp' => 'nullable|max:13|unique:karyawans',
                ],
            );

            if ($validator->fails()) {
                return new ArrayResource(false, $validator->messages()->all(), null);
            }

            $data = Karyawan::findOrFail($id);

            $newImage = null;

            if ($request->hasFile('foto')) {
                $image = $request->file('foto');
                $image->storeAs('public/karyawan', $image->hashName());

                // Hapus foto lama jika ada
                if ($data->foto) {
                    Storage::delete('public' . $data->foto);
                }
                $newImage = 'karyawan/'.$image->hashName();
            }

            $data->update([
                'nama' => $request->nama ?? $data->nama,
                'email' => $request->email ?? $data->email,
                'alamat' => $request->alamat ?? $data->alamat,
                'jenis_kelamin' => $request->jenis_kelamin ?? $data->jenis_kelamin,
                'tanggal_lahir'  => $request->tanggal_lahir ?? $data->tanggal_lahir,
                'foto' => $newImage != null ? $newImage : $data->foto,
                'no_telp' => $request->no_telp ?? $data->no_telp,
                'face_id' => $request->face_id ?? $data->face_id,

            ]);
            $data->wfa = $data->wfa == 1 ? true : false;
            $data->foto = $data->foto != null ? url('storage/' . $data->foto) : null;

            return new ArrayResource(true, 'data anda berhasil di update', $data);
        } catch (\Throwable $th) {
            return new ArrayResource(false, $th, null);
        }
    }

    public function sendLocation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_karyawan' => 'required',
                'latitude' => 'required',
                'longitude' => 'required'
            ]);

            if ($validator->fails()) {
                return new ArrayResource(false, $validator->messages()->all(), null);
            }

            $data = KaryawanLocation::where('id_karyawan', $request->id_karyawan)->first();

            if (!$data) {
                $data =  KaryawanLocation::create([
                    'id_karyawan' => $request->id_karyawan,
                    'id_device' => $request->id_device,
                    'location' => $request->location ?? '',
                    'distance' => $request->distance ?? '',
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,

                ]);
            }

            if ($data) {
                $data->update([
                    'id_device' => $request->id_device,
                    'distance' => $request->distance ?? '',
                    'location' => $request->location . '-' . Str::random(2) ?? 'tidak diketahui',
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ]);
            }
            $data->makeHidden('id_karyawan');
            return new ArrayResource(true, 'tracking lokasi', $data);
        } catch (\Throwable $th) {
            return new ArrayResource(false, $th, null);
        }
    }
}
