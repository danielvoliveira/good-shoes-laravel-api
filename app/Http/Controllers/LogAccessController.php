<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\LogAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogAccessController extends Controller
{
	use RefreshDatabase;

    public function index()
    {
        $logAccess = LogAccess::all();
 
        return response()->json([
            'success' => true,
            'data' => $logAccess
        ]);
    }
}
