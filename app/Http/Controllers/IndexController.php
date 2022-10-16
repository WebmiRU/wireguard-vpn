<?php

namespace App\Http\Controllers;

class IndexController extends Controller
{
    public function index() {
        return response()->json([
            'api_version' => 0.1,
        ]);
    }
}
