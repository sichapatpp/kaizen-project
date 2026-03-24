<?php

namespace App\Http\Controllers;

use App\Models\KaizenProject;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = (int)date('Y') + 543;
        $selectedYear = (int)$request->input('year', $currentYear);
        $years = collect(range($currentYear, $currentYear - 5));

        $query = KaizenProject::where('fiscalyear', $selectedYear);

        $counts = [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->whereIn('status', [
                'pending',
                'waiting_for_manager_result_approval',
                'waiting_for_chairman_approval',
            ])->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
            'approved' => (clone $query)->where('status', 'completed')->count(),
            'draft' => (clone $query)->where('status', 'draft')->count(),
        ];

        // ผลงานเด่น = เสร็จสิ้น + ได้รับรางวัล
        $featuredProjects = (clone $query)
            ->where('status', 'completed')
            ->whereNotNull('award_type')
            ->orderByRaw("FIELD(award_type, 'Platinum', 'Gold', 'Silver', 'Bronze') ASC")
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->with(['participants', 'user'])
            ->get();

        $allActivities = (clone $query)
            ->where('status', '!=', 'draft')
            ->with(['user', 'indicators'])
            ->orderBy('created_at', 'desc')
            ->get();

        $typeLabels = [
            'increase_revenue' => 'เพิ่มรายได้',
            'reduce_expenses' => 'ลดรายจ่าย',
            'reduce_steps' => 'ลดขั้นตอน',
            'reduce_time' => 'ลดเวลาการทำงาน',
            'improve_quality' => 'ปรับปรุงคุณภาพ',
            'reduce_risk' => 'ลดความเสี่ยง',
            'maintain_image' => 'รักษาภาพลักษณ์/ชื่อเสียงองค์กร',
            'innovation' => 'สิ่งประดิษฐ์/นวัตกรรม',
            'new_service' => 'เปิดบริการใหม่',
            'others' => 'อื่นๆ',
        ];

        $typeCounts = [];
        $typeProjects = [];
        $typeSummaries = [];

        foreach ($allActivities as $project) {
            $types = $project->improvement_types ?? [];
            if (is_string($types)) {
                $types = json_decode($types, true) ?? [];
            }
            foreach ((array)$types as $type) {
                $label = $typeLabels[$type] ?? $type;
                $typeCounts[$label] = ($typeCounts[$label] ?? 0) + 1;
                $typeProjects[$label][] = [
                    'id' => $project->id,
                    'title' => $project->title,
                    'user' => $project->user->name ?? 'ไม่ระบุ',
                ];

                // สะสมค่า before/after จากทุกตัวชี้วัดของโครงการนั้น
                $sumBefore = $project->indicators->sum(fn($ind) => is_numeric($ind->before_value) ? (float)$ind->before_value : 0);
                $sumAfter = $project->indicators->sum(fn($ind) => is_numeric($ind->after_value) ? (float)$ind->after_value : 0);
                $firstUnit = $project->indicators->first()->unit ?? '';

                $typeSummaries[$label]['before'] = ($typeSummaries[$label]['before'] ?? 0) + $sumBefore;
                $typeSummaries[$label]['after'] = ($typeSummaries[$label]['after'] ?? 0) + $sumAfter;
                $typeSummaries[$label]['unit'] = $firstUnit;
            }
        }

        // คำนวณ net หลังจากรวมทุก project แล้ว
        foreach ($typeSummaries as $label => &$summary) {
            $summary['net'] = $summary['before'] - $summary['after'];
        }
        unset($summary);

        arsort($typeCounts);

        return view('dashboard.index', compact(
            'counts',
            'featuredProjects',
            'allActivities',
            'typeCounts',
            'typeProjects',
            'typeSummaries',
            'years',
            'selectedYear'
        ));
    }

    private function getTypeData($selectedYear, $typeName)
    {
        $allActivities = KaizenProject::where('fiscalyear', $selectedYear)
            ->where('status', '!=', 'draft')
            ->with(['user', 'indicators'])
            ->orderBy('created_at', 'desc')
            ->get();

        $typeLabels = [
            'increase_revenue' => 'เพิ่มรายได้',
            'reduce_expenses' => 'ลดรายจ่าย',
            'reduce_steps' => 'ลดขั้นตอน',
            'reduce_time' => 'ลดเวลาการทำงาน',
            'improve_quality' => 'ปรับปรุงคุณภาพ',
            'reduce_risk' => 'ลดความเสี่ยง',
            'maintain_image' => 'รักษาภาพลักษณ์/ชื่อเสียงองค์กร',
            'innovation' => 'สิ่งประดิษฐ์/นวัตกรรม',
            'new_service' => 'เปิดบริการใหม่',
            'others' => 'อื่นๆ',
        ];

        $projects = [];
        $summary = ['before' => 0, 'after' => 0, 'net' => 0];

        foreach ($allActivities as $project) {
            $types = $project->improvement_types ?? [];
            if (is_string($types)) {
                $types = json_decode($types, true) ?? [];
            }
            $labels = array_map(function($t) use ($typeLabels) {
                return $typeLabels[$t] ?? $t;
            }, (array)$types);

            if (in_array($typeName, $labels)) {
                $sumBefore = $project->indicators->sum(fn($ind) => is_numeric($ind->before_value) ? (float)$ind->before_value : 0);
                $sumAfter = $project->indicators->sum(fn($ind) => is_numeric($ind->after_value) ? (float)$ind->after_value : 0);
                
                $projects[] = [
                    'id' => $project->id,
                    'title' => $project->title,
                    'user' => $project->user->name ?? 'ไม่ระบุ',
                    'before' => $sumBefore,
                    'after' => $sumAfter,
                    'net' => $sumBefore - $sumAfter,
                ];

                $summary['before'] += $sumBefore;
                $summary['after'] += $sumAfter;
            }
        }
        $summary['net'] = $summary['before'] - $summary['after'];

        return compact('projects', 'summary', 'typeName', 'selectedYear');
    }

    public function exportExcel(Request $request)
    {
        $year = $request->input('year', date('Y') + 543);
        $type = $request->input('type');

        if (!$type) return back()->with('error', 'กรุณาระบุประเภท');

        $data = $this->getTypeData($year, $type);
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\KaizenTypeExport($data), "Kaizen_{$type}_{$year}.xlsx");
    }

    public function exportPdf(Request $request)
    {
        $year = $request->input('year', date('Y') + 543);
        $type = $request->input('type');

        if (!$type) return back()->with('error', 'กรุณาระบุประเภท');

        $data = $this->getTypeData($year, $type);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('dashboard.export-pdf', $data);
        return $pdf->download("Kaizen_{$type}_{$year}.pdf");
    }
}