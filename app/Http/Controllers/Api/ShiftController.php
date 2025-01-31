<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArrayResource;
use App\Models\Shifts;

class ShiftController extends Controller
{
    public function get($id)
    {
        try {
            $data = Shifts::findOrFail($id);
            $data->makeHidden('created_at');
            $data->makeHidden('updated_at');
            return new ArrayResource(true, 'data shift', $data);
        } catch (\Throwable $th) {
            return new ArrayResource(false, $th, null);
        }
    }
}
