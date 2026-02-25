<?php

namespace App\Http\Controllers;

use App\Models\KaizenProject;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $counts = [
            'total'    => KaizenProject::count(),
            'pending'  => KaizenProject::whereIn('status', ['pending', 'waiting_for_manager_result_approval', 'waiting_for_chairman_approval'])->count(),
            'rejected' => KaizenProject::where('status', 'rejected')->count(),
            'approved' => KaizenProject::where('status', 'completed')->count(),
            'draft'    => KaizenProject::where('status', 'draft')->count(),
        ];

        $featuredProjects = KaizenProject::where('status', 'completed')
            ->where('is_achieved', true)
            ->orderBy('budget_used', 'desc')
            ->limit(6)
            ->with('participants')
            ->get();

        return view('dashboard.index', compact('counts', 'featuredProjects'));
    }
}