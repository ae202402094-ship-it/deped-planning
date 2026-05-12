<?php

namespace App\Http\Controllers;

use App\Models\School;

class MapController extends Controller
{
    public function showPublicMap()
    {
        $schools = School::all(); 
        return view('public_map', compact('schools'));
    }

    public function showMap() 
    {
        $schools = School::all(); 
        return view('admin.map', compact('schools'));
    }
}