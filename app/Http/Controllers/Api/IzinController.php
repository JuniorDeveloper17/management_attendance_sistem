<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArrayResource;
use App\Http\Resources\ListResource;
use App\Models\izin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class IzinController extends Controller
{

    public function create(Request $request)
    {
        try {
            //==>validasi hari
            $date = now();
            $check = Izin::where('id_karyawan', $request->id_karyawan)
                ->whereDate('created_at', $date)->first();

            if ($check) {
                return new ArrayResource(false, 'anda sudah melakukan izin hari ini', null);
            }

            //==>validasi keterangan harus ada
            $validator = Validator::make($request->all(), [
                'keterangan' => 'required'
            ]);

            if ($validator->fails()) {
                return new ArrayResource(false, $validator->messages()->all(), null);
            }

            //==>validasi apakah ada document yang dilampirkan
            $newImage = null;

            if ($request->hasFile('document')) {
                $image = $request->file('document');
                $image->storeAs('public/izin', $image->hashName());
                $newImage ='izin/'. $image->hashName();
            }

            //==>simpan ke database 
            $data = Izin::create(
                [
                    'id_karyawan' => $request->id_karyawan,
                    'keterangan' => $request->keterangan,
                    'keterangan_lanjutan' => $request->keterangan_lanjutan,
                    'document' => $newImage ?? null,
                    'status' => false
                ]
            );
            //printf($data->document);
            //==>manipulasi hasil response
            $data->makeHidden('id_karyawan');
            $data->status = $data->status == 1 ? true : false;
            $data->document = $data->document != null ? url('storage/' .  $data->document) : null;

            return new ArrayResource(true, 'pengajuan izin berhasil di kirim', $data);
        } catch (\Throwable $th) {
            return new ArrayResource(false, $th, null);
        }
    }
    public function update(Request $request)
    {
        try {
            $id = $request->id;
            $data = Izin::findOrFail($id);

            //==>validasi apakah waktu nya sama 
            $createdAt = Carbon::parse($data->created_at);
            if ($createdAt->toDateString() != Carbon::now()->toDateString() || $data->status == 1) {

                return new ArrayResource(false, 'tidak dapat merubah informasi izin', null);
            }

            //==>validasi apakah ada document yang dilampirkan
            $newImage = null;

            if ($request->hasFile('document')) {
                $image = $request->file('document');
                $image->storeAs('public/izin', $image->hashName());
                //==>hapus document lama jika ada
                if ($data->document) {
                    Storage::delete('public/' . $data->document);
                }
                $newImage = 'izin/'.$image->hashName();
            }


            $data->update([
                'keterangan' => $request->keterangan ?? $data->keterangan,
                'keterangan_lanjutan' => $request->keterangan_lanjutan ?? $data->keterangan_lanjutan,
                'document' => $newImage ?? $data->document,
            ]);

            //==>manipulasi hasil response
            $data->makeHidden('id_karyawan');
            $data->status = $data->status == 1 ? true : false;
            $data->document = $data->document != null ? url('storage/' .  $data->document) : null;

            return new ArrayResource(true, 'pengajuan izin berhasil di perbarui', $data);
        } catch (\Throwable $th) {

            return new ArrayResource(false, $th, null);
        }
    }
    public function get($id_karyawan)
    {
        try {
            $data = Izin::where('id_karyawan', $id_karyawan)
                ->select('id', 'keterangan', 'status', 'created_at')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            //==>manipulasi hasil response
            $data->getCollection()->transform(function ($item) {
                $item->status = $item->status == 1 ? true : false;
                return $item->makeHidden('id_karyawan');
            });


            return new ListResource(true, 'list data izin', $data);
        } catch (\Throwable $th) {
            return new ArrayResource(false, $th, null);
        }
    }
    public function detail($id)
    {
        try {
            $data = Izin::findOrFail($id);

            //==>manipulasi hasil response
            $data->makeHidden('id_karyawan');
            $data->status = $data->status == 1 ? true : false;
            $data->document = $data->document != null ? url('storage/' .  $data->document) : null;

            return new ArrayResource(true, 'detail izin ' . $data->keterangan, $data);
        } catch (\Throwable $th) {
            return new ArrayResource(false, $th, null);
        }
    }
    public function delete($id)
    {
        try {
            $data = Izin::findOrFail($id);
           
            //==>validasi apakah waktu nya sama 
            $createdAt = Carbon::parse($data->created_at);
            if ($createdAt->toDateString() != Carbon::now()->toDateString() || $data->status == 1) {
                return new ArrayResource(false, 'tidak dapat membatalkan izin', null);
            }

            if (!empty($data->document)) {
                $filePath = $data->document; 
            
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
    
            }

            $data->delete();
            return new ArrayResource(true, 'izin berhasil di batalkan', null);
        } catch (\Throwable $th) {
            return new ArrayResource(false, $th, null);
        }
    }
}
