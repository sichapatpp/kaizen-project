<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApproveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function Approve()
    {
        return view('activities.approve');
    }
}
