<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class APIController extends Controller
{
    public function status()
    {
        return response()->json(
            [
                'status' => 'Ok',
                'message' => 'API is running'
            ],
            200
        );
    }

}
