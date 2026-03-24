<?php

namespace App\Http\Controllers;

use App\Models\KaizenProject;
use App\Models\KaizenFile;
use App\Models\KaizenParticipant;
use App\Models\KaizenIndicator;
use App\Models\KaizenHistory;
use App\Models\KaizenReview;
use App\Models\User;
use App\Models\Notification;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\KaizenStatusMail;

class KaizenController extends Controller
{
    /** หน้า form สร้างกิจกรรมใหม่ */
    public function create()
    {
        $draftId = null;
        $draftTypes = [];
        $draft = null;
        $users = User::where('id', '!=', Auth::id())->get();
        return view('activities.index', compact('draftId', 'draftTypes', 'draft', 'users'));
    }

    /** หน้ารายการ (index เดิม) */
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        return view('activities.index', compact('users'));
    }

    /** รายการ draft ของ user */
    public function drafts()
    {
        $drafts = KaizenProject::where('user_id', auth()->id())
            ->where('status', 'draft')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('activities.draft', compact('drafts'));
    }

    /** นับ draft (AJAX) */
    public function draftCount()
    {
        $count = KaizenProject::where('user_id', auth()->id())
            ->where('status', 'draft')
            ->count();

        return response()->json(['count' => $count]);
    }

    /** โหลด draft เข้า form */
    public function editDraft($id)
    {
        $draft = KaizenProject::where('id', '=', $id)
            ->where('user_id', '=', auth()->id())
            ->where('status', '=', 'draft')
            ->with(['participants', 'indicators'])
            ->firstOrFail();

        $draftId = $draft->id;
        $draftTypes = (array)$draft->improvement_types;

        session()->flashInput([
            'title' => $draft->title,
            'problem' => $draft->problem,
            'improvement' => $draft->improvement,
            'result' => $draft->result,
            'actual_result' => $draft->actual_result ?? '',
            'performance_detail' => $draft->performance_detail ?? '',
            'budget_used' => $draft->budget_used ?? '',
            'improvement_types' => $draftTypes,
            'is_achieved' => $draft->is_achieved,
            'not_achieved_detail' => $draft->not_achieved_detail ?? '',
        ]);

        $users = User::where('id', '!=', Auth::id())->get();
        return view('activities.index', compact('draftId', 'draftTypes', 'draft', 'users'));
    }

    public function saveDraft(Request $request)
    {
        try {
            $draftId = $request->input('draft_id');

            $data = [
                'fiscalyear' => date('Y') + 543,
                'title' => $request->input('title') ?: null,
                'problem' => $request->input('problem') ?: '-',
                'improvement' => $request->input('improvement') ?: '-',
                'result' => $request->input('result') ?: '-',
                'actual_result' => $request->input('actual_result') ?: null,
                'improvement_types' => $request->input('improvement_types') ?: [],
                'other_improvement_detail' => $request->input('other_improvement_detail') ?: null,
                'performance_detail' => $request->input('performance_detail') ?: null,
                'budget_used' => $request->input('budget_used') ?: null,
                'is_achieved' => $request->input('is_achieved') ?? 1,
                'not_achieved_detail' => $request->input('not_achieved_detail') ?: null,
                'user_id' => auth()->id(),
                'status' => 'draft',
            ];

            if ($draftId) {
                $kaizen = KaizenProject::where('id', $draftId)
                    ->where('user_id', auth()->id())
                    ->where('status', 'draft')
                    ->first();

                if ($kaizen) {
                    $kaizen->update($data);
                }
                else {
                    $kaizen = KaizenProject::create($data);
                }
            }
            else {
                $kaizen = KaizenProject::create($data);
            }

            KaizenParticipant::where('kaizen_project_id', '=', $kaizen->id)->delete();
            foreach ((array)$request->input('participants', []) as $p) {
                if (!empty($p['name'])) {
                    KaizenParticipant::create([
                        'kaizen_project_id' => $kaizen->id,
                        'participant_name' => $p['name'],
                        'participation_percent' => $p['percent'] ?? 0,
                    ]);
                }
            }

            KaizenIndicator::where('kaizen_project_id', '=', $kaizen->id)->delete();
            foreach ((array)$request->input('indicators', []) as $ind) {
                if (!empty($ind['indicator_name'])) {
                    KaizenIndicator::create([
                        'kaizen_project_id' => $kaizen->id,
                        'indicator_name' => $ind['indicator_name'],
                        'before_value' => $ind['before_value'] ?? '',
                        'after_value' => $ind['after_value'] ?? '',
                        'unit' => $ind['unit'] ?? '',
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'draft_id' => $kaizen->id,
                'message' => 'บันทึกฉบับร่างแล้ว โดยมีข้อมูลบางส่วนที่อาจยังไม่ครบถ้วน',
            ]);

        }
        catch (\Exception $e) {
            Log::error('Draft Save Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการบันทึกฉบับร่าง',
            ], 500);
        }
    }

    /**
     * อัปโหลดรูปสำหรับ Draft (แยก endpoint เพราะ saveDraft ใช้ JSON ส่งไม่ได้)
     * POST /activities/draft/{id}/upload-files
     */
    public function uploadDraftFiles(Request $request, $id)
    {
        $kaizen = KaizenProject::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $request->validate([
            'file_type' => 'required|in:problem,solution,result',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $fileType = $request->input('file_type');

        $uploaded = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $this->saveFileToDB($file, $kaizen->id, $fileType);
                $uploaded[] = $file->getClientOriginalName();
            }
        }

        return response()->json([
            'success' => true,
            'uploaded' => count($uploaded),
        ]);
    }

    /** ลบ draft */
    public function deleteDraft($id)
    {
        KaizenProject::where('id', '=', $id)
            ->where('user_id', '=', auth()->id())
            ->where('status', '=', 'draft')
            ->delete();

        return redirect()->route('activities.draft')
            ->with('success', 'ลบฉบับร่างเรียบร้อยแล้ว');
    }

    public function status()
    {
        $projects = KaizenProject::with(['user', 'participants', 'files', 'reviews.user', 'indicators'])
            ->orderBy('created_at', 'desc')
            ->get();

        $activitiesData = $projects->map(function ($item) {

            $getImages = function ($type) use ($item) {
                    return $item->files->where('file_type', $type)->map(function ($f) {
                            return [
                            'url' => asset('storage/' . $f->file_path),
                            'name' => $f->file_name,
                            ];
                        }
                        )->values();
                    }
                        ;

                    $reviews = $item->reviews
                        ->sortByDesc('created_at')
                        ->map(function ($r) {
                try {
                    $dt = $r->created_at
                        ?\Carbon\Carbon::parse($r->created_at)
                        : null;
                    $dateStr = $dt
                        ? $dt->translatedFormat('j/n/') . ($dt->year + 543) . ' ' . $dt->format('H:i')
                        : '-';
                }
                catch (\Exception $e) {
                    $dateStr = $r->created_at ?? '-';
                }
                return [
                'action' => $r->action,
                'comment' => $r->comment,
                'reviewer' => $r->user->name ?? '-',
                'created_at' => $dateStr,
                'review_round' => $r->review_round ?? null,
                ];
            }
            )->values()->toArray();

            // ตัวชี้วัด — ส่งทุก field ที่หน้า report ใช้
            $indicators = $item->indicators->map(function ($ind) {
                    return [
                    'indicator_name' => $ind->indicator_name,
                    'before_value' => $ind->before_value,
                    'after_value' => $ind->after_value,
                    'unit' => $ind->unit,
                    ];
                }
                )->values()->toArray();

                return [
                'id' => $item->id,
                'code' => 'KZ-' . $item->fiscalyear . '-' . str_pad($item->id, 3, '0', STR_PAD_LEFT),
                'name' => $item->title,
                'status' => $item->status,
                'user_id' => $item->user_id,
                'types' => $item->improvement_types ?? [],
                'submitter' => $item->submitter ?? $item->user->name ?? '-',
                'problem' => $item->problem,
                'solution' => $item->improvement,
                'result' => $item->result,

                // ── ข้อมูลผลการดำเนินงาน (จาก saveReport) ──
                'actual_result' => $item->actual_result,
                'performance_detail' => $item->performance_detail,
                'budget_used' => $item->budget_used,
                'is_achieved' => $item->is_achieved,
                'not_achieved_detail' => $item->not_achieved_detail,
                'award_type' => $item->award_type,
                'indicators' => $indicators,

                'collaborators' => $item->participants->map(function ($p) {
                    return [
                    'name' => $p->participant_name,
                    'percent' => $p->participation_percent,
                    ];
                }
                )->toArray(),

                // รูปภาพแยกตาม file_type อย่างถูกต้อง
                'problem_images' => $getImages('problem'), // file_type = 'problem'
                'solution_images' => $getImages('solution'), // file_type = 'solution'
                'result_images' => $getImages('result'), // file_type = 'result'  (ผลที่คาดว่าจะได้รับ)
                'actual_images' => $getImages('actual'), // file_type = 'actual'  (รูปภาพประกอบผลงาน)
    
                'reviews' => $reviews,
                ];
            });

        return view('activities.status', compact('activitiesData'));
    }

    public function edit($id)
    {
        $kaizen = KaizenProject::with(['participants', 'indicators', 'files'])->findOrFail($id);

        if ($kaizen->user_id !== auth()->id()) {
            abort(403);
        }

        if (!in_array($kaizen->status, ['draft', 'rejected'])) {
            return redirect()->route('activities.status');
        }

        $draftId = $kaizen->id;
        $draftTypes = (array)$kaizen->improvement_types;

        session()->flashInput([
            'title' => $kaizen->title,
            'problem' => $kaizen->problem,
            'improvement' => $kaizen->improvement,
            'result' => $kaizen->result,
            'actual_result' => $kaizen->actual_result ?? '',
            'performance_detail' => $kaizen->performance_detail ?? '',
            'budget_used' => $kaizen->budget_used ?? '',
            'improvement_types' => $draftTypes,
            'other_improvement_detail' => $kaizen->other_improvement_detail ?? '',
            'is_achieved' => $kaizen->is_achieved,
            'not_achieved_detail' => $kaizen->not_achieved_detail ?? '',
        ]);

        $users = User::where('id', '!=', Auth::id())->get();
        return view('activities.index', compact('kaizen', 'draftId', 'draftTypes', 'users'));
    }

    public function update(Request $request, $id)
    {
        return $this->store($request->merge(['draft_id' => $id]));
    }

    public function report($id)
    {
        $kaizen = KaizenProject::with('files', 'indicators')->findOrFail($id);

        $isOwner = auth()->id() === $kaizen->user_id;
        $readOnly = request()->boolean('view')
            || !$isOwner
            || $kaizen->status !== 'in_progress';

        $lockedStatuses = ['waiting_for_manager_result_approval', 'waiting_for_chairman_approval', 'completed'];
        if ($isOwner && in_array($kaizen->status, $lockedStatuses) && !request()->boolean('view')) {
            return redirect()->route('activities.report', ['id' => $id, 'view' => 1])
                ->with('warning', 'ไม่สามารถแก้ไขข้อมูลในสถานะนี้ได้');
        }

        return view('activities.report', compact('kaizen', 'readOnly'));
    }

    public function saveReport(Request $request, $id)
    {
        $kaizen = KaizenProject::findOrFail($id);

        if ($kaizen->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'คุณไม่ใช่เจ้าของกิจกรรมนี้ ไม่สามารถบันทึกข้อมูลได้');
        }

        if ($kaizen->status !== 'in_progress') {
            return redirect()->route('activities.status')->with('error', 'ไม่สามารถแก้ไขข้อมูลในระหว่างรออนุมัติได้');
        }

        $request->validate([
            'actual_result' => 'required|string',
            'other_improvement_detail' => 'nullable|string',
            'performance_detail' => 'nullable|string',
            'budget_used' => 'nullable|numeric',
            'is_achieved' => 'nullable|boolean',
            'not_achieved_detail' => 'nullable|string',
            'actual_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'indicators' => 'nullable|array',
        ]);
        // dd($request->all());
        // อัปรูปใหม่เพิ่มเติม
        $this->uploadFiles($request, 'actual_images', $kaizen->id, 'actual');

        $oldStatus = $kaizen->status;

        $kaizen->actual_result = $request->actual_result;
        $kaizen->other_improvement_detail = $request->other_improvement_detail;
        $kaizen->performance_detail = $request->performance_detail;
        $kaizen->budget_used = $request->budget_used;
        $kaizen->is_achieved = $request->is_achieved ?? 1;
        $kaizen->not_achieved_detail = $request->not_achieved_detail;
        $kaizen->status = 'waiting_for_manager_result_approval';
        $kaizen->save();

        $this->logStatusChange($kaizen->id, $oldStatus, $kaizen->status);

        $kaizen->indicators()->delete();
        if ($request->has('indicators') && is_array($request->indicators)) {
            foreach ($request->indicators as $ind) {
                if (!empty($ind['indicator_name'])) {
                    KaizenIndicator::create([
                        'kaizen_project_id' => $kaizen->id,
                        'indicator_name' => $ind['indicator_name'],
                        'before_value' => $ind['before_value'] ?? '',
                        'after_value' => $ind['after_value'] ?? '',
                        'unit' => $ind['unit'] ?? '',
                    ]);
                }
            }
        }



        return redirect()->route('activities.status')
            ->with('success', 'บันทึกผลการดำเนินงานและส่งอนุมัติเรียบร้อยแล้ว');
    }

    public function store(Request $request)
    {
        if (!$request->has('is_draft')) {
            $request->validate([
                'title' => 'required|string',
                'problem' => 'required|string',
                'improvement' => 'required|string',
                'result' => 'required|string',
                'indicators' => 'nullable|array',
                'indicators.*.indicator_name' => 'required_with:indicators|string|max:255',
                'participants' => 'nullable|array',
                'participants.*.name' => 'required_with:participants|string|max:255',
            ]);
        }

        $draftId = $request->input('draft_id');
        $payload = [
            'fiscalyear' => date('Y') + 543,
            'title' => $request->title ?? 'ไม่มีชื่อหัวข้อ (ฉบับร่าง)',
            'problem' => $request->problem,
            'improvement' => $request->improvement,
            'result' => $request->result,
            'user_id' => auth()->id(),
            'status' => $request->has('is_draft') ? 'draft' : 'pending',
            'improvement_types' => $request->improvement_types,
            'submitter' => $request->submitter,
            'other_improvement_detail' => $request->other_improvement_detail,
        ];

        $oldStatus = null;
        if ($draftId) {
            $kaizen = KaizenProject::where('id', '=', $draftId)
                ->where('user_id', '=', auth()->id())
                ->whereIn('status', ['draft', 'rejected'])
                ->first();

            if ($kaizen) {
                $oldStatus = $kaizen->status;
                $kaizen->update($payload);
            }
            else {
                $kaizen = KaizenProject::create($payload);
            }
        }
        else {
            $kaizen = KaizenProject::create($payload);
        }

        $this->logStatusChange($kaizen->id, $oldStatus, $kaizen->status);

        $kaizen->participants()->delete();
        if ($request->has('participants') && is_array($request->participants)) {
            foreach ($request->participants as $participant) {
                if (!empty($participant['name'])) {
                    KaizenParticipant::create([
                        'kaizen_project_id' => $kaizen->id,
                        'participant_name' => $participant['name'],
                        'participation_percent' => $participant['percent'] ?? 0,
                    ]);
                }
            }
        }

        $kaizen->indicators()->delete();
        if ($request->has('indicators') && is_array($request->indicators)) {
            foreach ($request->indicators as $indicator) {
                if (empty($indicator['indicator_name']))
                    continue;
                KaizenIndicator::create([
                    'kaizen_project_id' => $kaizen->id,
                    'indicator_name' => $indicator['indicator_name'],
                    'before_value' => $indicator['before_value'] ?? '',
                    'after_value' => $indicator['after_value'] ?? '',
                    'unit' => $indicator['unit'] ?? '',
                ]);
            }
        }

        // อัปรูปใหม่เพิ่มเติม (Append)
        $this->uploadFiles($request, 'problem_images', $kaizen->id, 'problem');
        $this->uploadFiles($request, 'solution_images', $kaizen->id, 'solution');
        $this->uploadFiles($request, 'result_images', $kaizen->id, 'result');

        return redirect()
            ->route('activities.status')
            ->with('success', $request->has('is_draft') ? 'บันทึกฉบับร่างเรียบร้อยแล้ว' : 'บันทึกกิจกรรม Kaizen เรียบร้อยแล้ว');
    }

    public function show($id)
    {
        $kaizen = KaizenProject::with('files')->findOrFail($id);
        return view('activities.show', compact('kaizen'));
    }

    public function approve()
    {
        $projects = KaizenProject::with(['user', 'participants', 'files', 'indicators'])
            ->orderBy('created_at', 'desc')
            ->get();

        $activitiesData = $projects->map(function ($item) {

            $getImages = function ($type) use ($item) {
                    return $item->files->where('file_type', $type)->map(function ($f) {
                            return [
                            'url' => asset('storage/' . $f->file_path),
                            'name' => $f->file_name,
                            ];
                        }
                        )->values();
                    }
                        ;

                    $submitDate = $item->created_at
                        ? $item->created_at->translatedFormat('j/n/') . ($item->created_at->year + 543)
                        : '-';
                    $approvalDate = $item->updated_at && $item->status != 'draft' && $item->status != 'pending'
                        ? $item->updated_at->translatedFormat('j/n/') . ($item->updated_at->year + 543)
                        : null;

                    // ตัวชี้วัด — ส่งทุก field ที่หน้า report ใช้
                    $indicators = $item->indicators->map(function ($ind) {
                    return [
                    'indicator_name' => $ind->indicator_name,
                    'before_value' => $ind->before_value,
                    'after_value' => $ind->after_value,
                    'unit' => $ind->unit,
                    ];
                }
                )->values()->toArray();

                return [
                'id' => $item->id,
                'code' => 'KZ-' . $item->fiscalyear . '-' . str_pad($item->id, 3, '0', STR_PAD_LEFT),
                'name' => $item->title,
                'status' => $item->status,
                'types' => $item->improvement_types ?? [],
                'submitter' => $item->submitter ?? $item->user->name ?? '-',
                'submitDate' => $submitDate,
                'approvalDate' => $approvalDate,
                'problem' => $item->problem,
                'solution' => $item->improvement,
                'result' => $item->result,
                'actual_result' => $item->actual_result,
                'performance_detail' => $item->performance_detail,
                'budget_used' => $item->budget_used,
                'is_achieved' => $item->is_achieved,
                'not_achieved_detail' => $item->not_achieved_detail,
                'award_type' => $item->award_type,
                'indicators' => $indicators,
                'collaborators' => $item->participants->map(function ($p) {
                    return [
                    'name' => $p->participant_name,
                    'percent' => $p->participation_percent,
                    ];
                }
                )->toArray(),

                // รูปภาพแยกตาม file_type อย่างถูกต้อง
                'problem_images' => $getImages('problem'), // file_type = 'problem'
                'solution_images' => $getImages('solution'), // file_type = 'solution'
                'result_images' => $getImages('result'), // file_type = 'result'  (ผลที่คาดว่าจะได้รับ)
                'actual_images' => $getImages('actual'), // file_type = 'actual'  (รูปภาพประกอบผลงาน)
                ];
            });

        return view('activities.approve', compact('activitiesData'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'note' => 'nullable|string',
        ]);

        $project = KaizenProject::findOrFail($id);
        $oldStatus = $project->status;
        $action = $request->status;

        if ($action === 'approved') {
            if ($oldStatus === 'pending') {
                $project->status = 'in_progress';
            }
            elseif ($oldStatus === 'waiting_for_manager_result_approval') {
                $project->status = 'waiting_for_chairman_approval';
            }
            elseif ($oldStatus === 'waiting_for_chairman_approval') {
                $project->status = 'completed';
            }
        }
        else {
            if ($oldStatus === 'pending') {
                $project->status = 'rejected';
            }
            elseif (in_array($oldStatus, ['waiting_for_manager_result_approval', 'waiting_for_chairman_approval'])) {
                $project->status = 'in_progress';
            }
        }

        $project->save();
        $this->logStatusChange($project->id, $oldStatus, $project->status);

        // คำนวณ review_round จาก oldStatus เพื่อเก็บลง DB
        $reviewRound = 1;
        if ($oldStatus === 'waiting_for_manager_result_approval') {
            $reviewRound = 2;
        }
        elseif ($oldStatus === 'waiting_for_chairman_approval') {
            $reviewRound = 3;
        }

        KaizenReview::create([
            'kaizen_project_id' => $project->id,
            'user_id' => Auth::id(),
            'comment' => $request->note ?: null,
            'action' => ($action === 'approved') ? 'approve' : 'reject',
            'review_round' => $reviewRound,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'อัปเดตสถานะเรียบร้อยแล้ว',
        ]);
    }

    public function giveAward(Request $request, $id)
    {
        $request->validate([
            'award_type' => 'required|in:Platinum,Gold,Silver,Bronze',
        ]);

        $project = KaizenProject::findOrFail($id);

        if ($project->status !== 'completed') {
            return response()->json(['success' => false, 'message' => 'ให้รางวัลได้เฉพาะกิจกรรมที่เสร็จสิ้นแล้วเท่านั้น'], 422);
        }

        $project->award_type = $request->award_type;
        $project->save();

        return response()->json(['success' => true, 'message' => 'บันทึกรางวัลเรียบร้อยแล้ว']);
    }

    private function logStatusChange($projectId, $oldStatus, $newStatus)
    {
        if ($oldStatus === $newStatus)
            return;

        $userId = Auth::id();

        KaizenHistory::create([
            'kaizen_project_id' => $projectId,
            'old_status' => $oldStatus ?: 'none',
            'new_status' => $newStatus,
            'user_id' => $userId,
        ]);

        $project = KaizenProject::find($projectId, ['*']);

        // Log the activity
        ActivityLog::create([
            'kaizen_project_id' => $projectId,
            'user_id' => $userId,
            'action' => 'changed_status',
            'status' => $newStatus,
            'comment' => "Status changed from {$oldStatus} to {$newStatus}",
        ]);

        // Send Notification to project owner if status was approved or rejected
        if ($project && $project->user_id !== $userId) {
            $title = '';
            $message = '';
            $statusTh = '';

            switch ($newStatus) {
                case 'in_progress':
                    if ($oldStatus === 'rejected' || $oldStatus === 'waiting_for_manager_result_approval' || $oldStatus === 'waiting_for_chairman_approval') {
                        $statusTh = 'ส่งกลับแก้ไข';
                    }
                    else if ($oldStatus === 'pending') {
                        $statusTh = 'อนุมัติ (เริ่มดำเนินการ)';
                    }
                    break;
                case 'waiting_for_chairman_approval':
                    $statusTh = 'ผ่านการอนุมัติจากหัวหน้าแล้ว (รอประธานพิจารณา)';
                    break;
                case 'completed':
                    $statusTh = 'อนุมัติโดยประธานเรียบร้อยแล้ว (เสร็จสิ้น)';
                    break;
                case 'rejected':
                    $statusTh = 'ถูกปฏิเสธ';
                    break;
            }

            if ($statusTh !== '') {
                Notification::create([
                    'user_id' => $project->user_id,
                    'kaizen_project_id' => $projectId,
                    'title' => "อัปเดตสถานะกิจกรรม: {$project->title}",
                    'message' => "กิจกรรม '{$project->title}' ของคุณได้รับการ {$statusTh} แล้วโดย " . Auth::user()->name,
                ]);

                // ส่งอีเมลแจ้งเตือนเจ้าของกิจกรรม
                if ($project->user && $project->user->email) {
                    Mail::to($project->user->email)->send(new KaizenStatusMail($project, $statusTh));
                }
            }
        }
    }

    /**
     * อัปโหลดไฟล์ใหม่ **เพิ่มเติม** โดยไม่ลบเก่า
     * ใช้กรณีที่ต้องการ append เช่น ครั้งแรกที่สร้าง (ยังไม่มีรูปเก่า)
     */
    private function uploadFiles(Request $request, $inputName, $kaizenId, $fileType)
    {
        if ($request->hasFile($inputName)) {
            $files = $request->file($inputName);
            if (is_array($files)) {
                foreach ($files as $file) {
                    $this->saveFileToDB($file, $kaizenId, $fileType);
                }
            }
            else {
                $this->saveFileToDB($files, $kaizenId, $fileType);
            }
        }
    }

    private function saveFileToDB($file, $kaizenId, $fileType)
    {
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $folderPath = "kaizen_files/{$kaizenId}/{$fileType}";
        $filePath = $file->storeAs($folderPath, $fileName, 'public');

        KaizenFile::create([
            'kaizen_project_id' => $kaizenId,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_type' => $fileType,
            'user_id' => auth()->id(),
        ]);
    }
    public function deleteFile($id)
    {
        try {
            $file = KaizenFile::findOrFail($id);

            // ตรวจสอบสิทธิ์ (เจ้าของกิจกรรม หรือ Admin/Manager?)
            $kaizen = $file->kaizenProject;
            if ($kaizen->user_id !== auth()->id() && !auth()->user()->role->role_name === 'admin') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            Storage::disk('public')->delete($file->file_path);
            $file->delete();

            return response()->json(['success' => true]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}