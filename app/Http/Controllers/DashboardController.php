<?php

namespace App\Http\Controllers;

use App\Models\KaizenProject;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentYear  = (int) date('Y') + 543;
        $selectedYear = (int) $request->input('year', $currentYear);
        $years        = collect(range($currentYear, $currentYear - 5));

        $query = KaizenProject::where('fiscalyear', $selectedYear);

        $counts = [
            'total'    => (clone $query)->count(),
            'pending'  => (clone $query)->whereIn('status', [
                              'pending',
                              'waiting_for_manager_result_approval',
                              'waiting_for_chairman_approval',
                          ])->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
            'approved' => (clone $query)->where('status', 'completed')->count(),
            'draft'    => (clone $query)->where('status', 'draft')->count(),
        ];

        // ผลงานเด่น = เสร็จสิ้น + บรรลุเป้าหมาย + ใช้งบน้อยสุด (ประหยัดสุด)
        $featuredProjects = (clone $query)
            ->where('status', 'completed')
            ->where('is_achieved', true)
            ->whereNotNull('budget_used')
            ->where('budget_used', '>', 0)
            ->orderBy('budget_used', 'asc')
            ->limit(6)
            ->with(['participants', 'user'])
            ->get();

        $allActivities = (clone $query)
            ->where('status', '!=', 'draft')
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $typeLabels = [
            'increase_revenue' => 'เพิ่มรายได้',
            'reduce_expenses'  => 'ลดรายจ่าย',
            'reduce_steps'     => 'ลดขั้นตอน',
            'reduce_time'      => 'ลดเวลาการทำงาน',
            'improve_quality'  => 'ปรับปรุงคุณภาพ',
            'reduce_risk'      => 'ลดความเสี่ยง',
            'maintain_image'   => 'รักษาภาพลักษณ์/ชื่อเสียงองค์กร',
            'innovation'       => 'สิ่งประดิษฐ์/นวัตกรรม',
            'new_service'      => 'เปิดบริการใหม่',
            'others'           => 'อื่นๆ',
        ];

        $typeCounts   = [];
        $typeProjects = [];

        foreach ($allActivities as $project) {
            $types = $project->improvement_types ?? [];
            if (is_string($types)) {
                $types = json_decode($types, true) ?? [];
            }
            foreach ((array) $types as $type) {
                $label = $typeLabels[$type] ?? $type;
                $typeCounts[$label] = ($typeCounts[$label] ?? 0) + 1;
                $typeProjects[$label][] = [
                    'id'    => $project->id,
                    'title' => $project->title,
                    'user'  => $project->user->name ?? 'ไม่ระบุ',
                ];
            }
        }

        arsort($typeCounts);

        return view('dashboard.index', compact(
            'counts',
            'featuredProjects',
            'allActivities',
            'typeCounts',
            'typeProjects',
            'years',
            'selectedYear'
        ));
    }
}