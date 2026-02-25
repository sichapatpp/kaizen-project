@extends('layouts.base')

@section('content')
<div class="kaizen-wrapper">
    <div class="kaizen-inner">

        <div class="kaizen-header">
            <div>
                <div class="topbar-title">ฉบับร่างของฉัน</div>
                <div class="topbar-sub">กิจกรรม Kaizen ที่ยังไม่ได้ส่ง</div>
            </div>
            <a href="{{ route('activities.create') }}" class="btn-next" style="text-decoration:none; padding:10px 20px;">
                + สร้างกิจกรรมใหม่
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="background:#f0fdf4; border:1px solid #bbf7d0;
                 color:#166534; border-radius:8px; padding:12px 16px; margin-bottom:16px;">
                {{ session('success') }}
            </div>
        @endif

        @if($drafts->isEmpty())
            <div style="text-align:center; padding:60px 20px; color:#9ca3af;">
                <div style="font-size:48px; margin-bottom:12px;">📝</div>
                <div style="font-size:18px; font-weight:600; margin-bottom:8px;">ยังไม่มีฉบับร่าง</div>
                <div style="font-size:14px;">กด "สร้างกิจกรรมใหม่" แล้วบันทึกเป็นร่างได้เลย</div>
            </div>
        @else
            <div style="display:grid; gap:12px;">
                @foreach($drafts as $draft)
                <div style="background:#fff; border:1px solid #e5e7eb; border-radius:10px;
                            padding:18px 20px; display:flex; justify-content:space-between;
                            align-items:center; gap:16px;">
                    <div style="flex:1; min-width:0;">
                        <div style="font-weight:600; font-size:15px; color:#111827; margin-bottom:4px;
                                    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $draft->title ?: '(ยังไม่มีชื่อกิจกรรม)' }}
                        </div>
                        <div style="font-size:13px; color:#6b7280;">
                            บันทึกล่าสุด: {{ $draft->updated_at->translatedFormat('j M Y H:i') }}
                        </div>
                        @if($draft->improvement_types)
                        <div style="margin-top:6px; display:flex; flex-wrap:wrap; gap:4px;">
                            @php
                                $typeLabels = [
                                    'increase_revenue' => 'เพิ่มรายได้',
                                    'reduce_expenses'  => 'ลดรายจ่าย',
                                    'reduce_steps'     => 'ลดขั้นตอน',
                                    'reduce_time'      => 'ลดเวลาการทำงาน',
                                    'improve_quality'  => 'ปรับปรุงคุณภาพ',
                                    'reduce_risk'      => 'ลดความเสี่ยง',
                                    'maintain_image'   => 'รักษาภาพลักษณ์',
                                    'innovation'       => 'นวัตกรรม',
                                    'new_service'      => 'เปิดบริการใหม่',
                                    'others'           => 'อื่นๆ',
                                ];
                            @endphp
                            @foreach((array)$draft->improvement_types as $type)
                            <span style="background:#eff6ff; color:#1d4ed8; font-size:11px;
                                         padding:2px 8px; border-radius:999px;">{{ $typeLabels[$type] ?? $type }}</span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    <div style="display:flex; gap:8px; flex-shrink:0;">
                        <a href="{{ route('activities.editDraft', $draft->id) }}"
                           style="background:#3b82f6; color:#fff; padding:8px 16px;
                                  border-radius:6px; text-decoration:none; font-size:13px; font-weight:500;">
                            ✏️ แก้ไข
                        </a>
                        <form method="POST" action="{{ route('activities.deleteDraft', $draft->id) }}"
                              onsubmit="return confirm('ลบฉบับร่างนี้?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                style="background:#fef2f2; color:#dc2626; padding:8px 16px;
                                       border:1px solid #fecaca; border-radius:6px;
                                       font-size:13px; font-weight:500; cursor:pointer;">
                                🗑 ลบ
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @endif

    </div>
</div>
@endsection