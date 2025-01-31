<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArrayResource;
use App\Models\Offices;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function get($id)
    {
        try {
            $data = Offices::findOrFail($id);
            $data->makeHidden('created_at');
            $data->makeHidden('updated_at');
            return new ArrayResource(true, 'data office', $data);
        } catch (\Throwable $th) {
            return new ArrayResource(false, $th, null);
        }
    }
}
