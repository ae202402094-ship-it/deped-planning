<?php

namespace App\Http\Controllers;

use App\Models\School;

class PublicSchoolController extends Controller
{
    public function listSchools()
    {
        $schools = School::all(); 
        return view('schools_list', compact('schools'));
    }

    public function showPublicDetail($id) 
    {
        $school = School::findOrFail($id); 
        return view('user_view', compact('school'));
    }
}