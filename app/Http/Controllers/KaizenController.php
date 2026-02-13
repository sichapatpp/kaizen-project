<?php

namespace App\Http\Controllers;

use App\Models\KaizenProject;
use App\Models\KaizenFile;
use App\Models\KaizenParticipant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KaizenController extends Controller
{
    /**
     * แสดงหน้าแบบฟอร์ม
     */
    public function index()
    {
        return view('activities.index');
    }

    public function status()
    {
        // ดึงข้อมูลพร้อมความสัมพันธ์ 
        $projects = KaizenProject::with(['user', 'participants', 'files'])
            ->orderBy('created_at', 'desc')
            ->get();

        //  จัดรูปแบบข้อมูล (Map) ให้ตรงกับที่ JavaScript ในหน้า View ต้องการ
        $activitiesData = $projects->map(function ($item) {

            // กรองไฟล์ตามประเภท
            $getImages = function ($type) use ($item) {
                return $item->files->where('file_type', $type)->map(function ($f) {
                    return [
                        'url' => asset('storage/' . $f->file_path),
                        'name' => $f->file_name
                    ];
                })->values();
            };

            return [
                'id' => $item->id,
                'code' => 'KZ-' . $item->fiscalyear . '-' . str_pad($item->id, 3, '0', STR_PAD_LEFT),
                'name' => $item->title,
                'status' => $item->status,
                'types' => $item->improvement_types ?? [],
                'submitter' => $item->submitter ?? $item->user->name ?? '-', '-',
                'problem' => $item->problem,
                'solution' => $item->improvement,
                'result' => $item->result,
                'collaborators' => $item->participants->map(function ($p) {
                    return [
                        'name' => $p->participant_name,
                        'percent' => $p->participation_percent
                    ];
                })->toArray(),
                'problem_images' => $getImages('problem'),
                'solution_images' => $getImages('solution'),
                'result_images' => $getImages('result'),
            ];
        });

        return view('activities.status', compact('activitiesData'));
    }

    /**
     * บันทึกข้อมูล Kaizen ลงฐานข้อมูล
     */
    public function store(Request $request)
    {
        //  Validation
        $request->validate([
            'title' => 'required|string',
            'problem' => 'required|string',
            'improvement' => 'required|string',
            'result' => 'required|string',
            'problem_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'solution_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'result_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'participants' => 'nullable|array',
            'participants.*.name' => 'required_with:participants|string|max:255',
            'participants.*.percent' => 'required_with:participants|numeric|min:0|max:100',
        ]);

        // บันทึกข้อมูลโปรเจคหลัก
        $kaizen = KaizenProject::create([
            'fiscalyear' => date('Y') + 543,
            'title' => $request->title,
            'problem' => $request->problem,
            'improvement' => $request->improvement,
            'result' => $request->result,
            'user_id' => auth()->id(),
            'status' => 'pending',
            'improvement_types' => $request->improvement_types,
        ]);

        //  บันทึกข้อมูลผู้ร่วมงาน
        if ($request->has('participants') && is_array($request->participants)) {
            foreach ($request->participants as $key => $participant) {
                if (!empty($participant['name'])) {
                    KaizenParticipant::create([
                        'kaizen_project_id' => $kaizen->id,
                        'participant_name'  => $participant['name'],
                        'participation_percent' => $participant['percent'] ?? 0,
                    ]);
                }
            }
        }

        // อัปโหลดไฟล์
        $this->uploadFiles($request, 'problem_images', $kaizen->id, 'problem');
        $this->uploadFiles($request, 'solution_images', $kaizen->id, 'solution');
        $this->uploadFiles($request, 'result_images', $kaizen->id, 'result');

        //  Redirect
        return redirect()
            ->route('activities.index')
            ->with('success', 'บันทึกกิจกรรม Kaizen และผู้ร่วมงานเรียบร้อยแล้ว');
    }

    /**
     * แสดงรายละเอียด (หน้าแยก)
     */
    public function show($id)
    {
        $kaizen = KaizenProject::with('files')->findOrFail($id);
        return view('activities.show', compact('kaizen'));
    }

    /**
     * หน้าอนุมัติและรายงาน
     */
    public function approve()
    {
        $projects = KaizenProject::with(['user', 'participants', 'files'])
                    ->orderBy('created_at', 'desc')
                    ->get();

        $activitiesData = $projects->map(function ($item) {
            
            $getImages = function ($type) use ($item) {
                return $item->files->where('file_type', $type)->map(function ($f) {
                    return [
                        'url' => asset('storage/' . $f->file_path),
                        'name' => $f->file_name
                    ];
                })->values();
            };

            $submitDate = $item->created_at ? $item->created_at->translatedFormat('j/n/') . ($item->created_at->year + 543) : '-';
            $approvalDate = $item->updated_at && $item->status != 'draft' && $item->status != 'pending' 
                            ? $item->updated_at->translatedFormat('j/n/') . ($item->updated_at->year + 543) 
                            : null;

            return [
                'id' => $item->id,
                'name' => $item->title,
                'status' => $item->status,
                'types' => $item->improvement_types ?? [],
                'submitter' => $item->submitter ?? $item->user->name ?? '-',
                'submitDate' => $submitDate,
                'approvalDate' => $approvalDate,
                'problem' => $item->problem,
                'solution' => $item->improvement,
                'result' => $item->result,
                'collaborators' => $item->participants->map(function ($p) {
                    return [
                        'name' => $p->participant_name,
                        'percent' => $p->participation_percent
                    ];
                })->toArray(),
                'problem_images' => $getImages('problem'),
                'solution_images' => $getImages('solution'),
                'result_images' => $getImages('result'),
            ];
        });

        return view('activities.approve', compact('activitiesData'));
    }

    /**
     * ✅ เพิ่มใหม่: อัปเดตสถานะกิจกรรม (Approve/Reject)
     */
    /**
     * แสดงหน้าบันทึกผลการดำเนินงาน (Report)
     */
    public function report($id)
    {
        $kaizen = KaizenProject::with('files')->findOrFail($id);

        // Check if user is owner and status is in_progress or draft (if returned)
        if ($kaizen->user_id !== auth()->id()) {
            abort(403);
        }
        
        // Allow reporting if in_progress or rejected (assuming rejected means "fix and resubmit")
        if (!in_array($kaizen->status, ['in_progress', 'draft'])) {
            return redirect()->route('activities.status')->with('error', 'ไม่สามารถรายงานผลได้ในสถานะนี้');
        }

        return view('activities.report', compact('kaizen'));
    }

    /**
     * บันทึกผลการดำเนินงาน
     */
    public function saveReport(Request $request, $id)
    {
        $request->validate([
            'result' => 'required|string',
            'result_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $kaizen = KaizenProject::findOrFail($id);
        
        if ($kaizen->user_id !== auth()->id()) {
            abort(403);
        }

        $kaizen->result = $request->result;
        $kaizen->status = 'waiting_for_result_approval'; // รอหัวหน้าอนุมัติผล
        $kaizen->save();

        $this->uploadFiles($request, 'result_images', $kaizen->id, 'result');

        return redirect()->route('activities.status')->with('success', 'บันทึกรายผลการดำเนินงานเรียบร้อยแล้ว');
    }

    /**
     * อัปเดตสถานะกิจกรรม (Approve/Reject)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected', // Action from UI
            'note' => 'nullable|string'
        ]);

        $project = KaizenProject::findOrFail($id);
        $user = auth()->user();
        $role = $user->role->role_name ?? 'user';

        $currentStatus = $project->status;
        $action = $request->status; // approved / rejected
        $nextStatus = $currentStatus;

        // Workflow Logic
        if ($role === 'manager') {
            if ($currentStatus === 'pending') {
                // Round 1: Manager Approve Proposal -> In Progress
                // Round 1: Manager Reject Proposal -> Draft (Return to User)
                $nextStatus = ($action === 'approved') ? 'in_progress' : 'draft';
            } elseif ($currentStatus === 'waiting_for_result_approval') {
                // Round 2: Manager Approve Result -> Wait for Chairman
                // Round 2: Manager Reject Result -> In Progress (Return to User to fix result)
                $nextStatus = ($action === 'approved') ? 'waiting_for_chairman_approval' : 'in_progress';
            }
        } elseif ($role === 'chairman') {
            if ($currentStatus === 'waiting_for_chairman_approval') {
                // Final: Chairman Approve -> Completed
                // Final: Chairman Reject -> In Progress (Return to User to fix result)
                $nextStatus = ($action === 'approved') ? 'completed' : 'in_progress';
            }
        } elseif ($role === 'admin') {
             // Admin override (Force)
             $nextStatus = ($action === 'approved') ? 'completed' : 'draft';
        }

        // Update DB
        $project->status = $nextStatus;
        $project->save();

        return response()->json([
            'success' => true,
            'message' => 'อัปเดตสถานะเรียบร้อยแล้ว',
            'new_status' => $nextStatus
        ]);
    }

    /**
     * Function สำหรับอัปโหลดและบันทึกไฟล์
     */
    private function uploadFiles(Request $request, $inputName, $kaizenId, $fileType)
    {
        if ($request->hasFile($inputName)) {
            $files = $request->file($inputName);
            if (is_array($files)) {
                foreach ($files as $file) {
                    $this->saveFileToDB($file, $kaizenId, $fileType);
                }
            } else {
                $this->saveFileToDB($files, $kaizenId, $fileType);
            }
        }
    }

    /**
     * Function ย่อย: จัดการไฟล์แต่ละไฟล์
     */
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
}