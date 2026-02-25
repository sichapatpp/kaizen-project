@extends('layouts.base')

@section('content')

<div class="kaizen-wrapper">
    <div class="kaizen-inner">

@php
    $isOwner  = auth()->id() === $kaizen->user_id;
    $readOnly = $readOnly ?? !$isOwner;

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
        'others'           => 'อื่นๆ (ระบุเอง)',
    ];
    $types = (array) $kaizen->improvement_types;
@endphp

        <div class="kaizen-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <div>
                <div class="topbar-title" style="font-size: 20px; font-weight: 600; color: #1e293b;">รายงานผลการดำเนินงาน</div>
                <div class="topbar-sub" style="color: #64748b; font-size: 14px;">
                    บันทึกผลลัพธ์หลังการปรับปรุง (กิจกรรม #{{ $kaizen->id }}: {{ $kaizen->title }})
                </div>
            </div>
            <div>
                <a href="{{ url()->previous() ?: route('activities.status') }}" 
                   style="display: inline-flex; align-items: center; padding: 8px 16px; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; color: #475569; text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s;">
                    ‹ ย้อนกลับ
                </a>
            </div>
        </div>

        @if(session('warning'))
        <div style="margin-bottom:16px; padding:12px 16px; background:#fff7ed; color:#9a3412; border:1px solid #fed7aa; border-radius:8px; display:flex; align-items:center; gap:10px; font-size: 14px;">
            <span>⚠️</span> <span>{{ session('warning') }}</span>
        </div>
        @endif

        <form method="POST" action="{{ route('activities.saveReport', $kaizen->id) }}" enctype="multipart/form-data">
            @csrf

            <div class="kaizen-card" style="background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 24px;">
                <div class="section-header" style="margin-bottom: 20px;">
                    <h3 style="font-size: 16px; font-weight: 600; color: #1e293b; margin-bottom: 4px;">ผลการดำเนินงาน</h3>
                    <p style="font-size: 13px; color: #64748b;">เปรียบเทียบผลลัพธ์ที่เกิดขึ้นจริง และแนบรูปประกอบ</p>
                </div>

                <div style="background: #f8fafc; padding: 16px; border-radius: 8px; margin-bottom: 24px; border: 1px solid #e2e8f0;">
                    <div style="margin-bottom: 12px;">
                        <label style="font-weight:600; font-size:13px; color:#64748b; display: block; margin-bottom: 4px;">ปัญหาที่พบ:</label>
                        <div style="font-size:14px; color: #334155;">{{ $kaizen->problem }}</div>
                    </div>
                    <div style="margin-bottom: 12px;">
                        <label style="font-weight:600; font-size:13px; color:#64748b; display: block; margin-bottom: 4px;">แนวทางการปรับปรุง:</label>
                        <div style="font-size:14px; color: #334155;">{{ $kaizen->improvement }}</div>
                    </div>

                    @if(!empty($types))
                    <div>
                        <label style="font-weight:600; font-size:13px; color:#64748b; display: block; margin-bottom: 6px;">ประเภทการปรับปรุง:</label>
                        <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                            @foreach($types as $type)
                                <span style="display:inline-block; padding:4px 10px; background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; border-radius:20px; font-size:12px; font-weight:500;">
                                    {{ $typeLabels[$type] ?? $type }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 8px;">ผลลัพธ์ที่เกิดขึ้นจริง (Result) <span style="color: #ef4444;">*</span></label>
                    <textarea name="actual_result" rows="4" class="input-styled" required {{ $readOnly ? 'disabled' : '' }}>{{ old('actual_result', $kaizen->actual_result) }}</textarea>
                    <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">อธิบายผลลัพธ์ที่ได้หลังการปรับปรุง ว่าดีขึ้นอย่างไร</div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 8px;">รายละเอียดการดำเนินงานเพิ่มเติม</label>
                    <textarea name="performance_detail" rows="2" class="input-styled" {{ $readOnly ? 'disabled' : '' }}>{{ old('performance_detail', $kaizen->performance_detail) }}</textarea>
                </div>

                <div class="form-group" style="margin-top:24px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 8px;">ตัวชี้วัด (Performance Indicators)</label>
                    <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">
                        <div id="indicators-table-header" style="display:grid; grid-template-columns:2.2fr 1fr 1fr 1.2fr 1fr 40px; gap:8px; background:#f8fafc; padding:12px 14px; border-bottom:1px solid #e2e8f0; font-size:12px; font-weight:600; color:#475569; text-align:center;">
                            <div style="text-align:left;">ชื่อตัวชี้วัด</div>
                            <div>ก่อน</div>
                            <div>หลัง</div>
                            <div>ผลต่าง (%)</div>
                            <div>หน่วย</div>
                            <div></div>
                        </div>

                        <div id="indicators-list">
                            @php
                                $displayIndicators = $kaizen->indicators->count() > 0 
                                    ? $kaizen->indicators 
                                    : collect($kaizen->improvement_types)->map(fn($t) => (object)['indicator_name' => $typeLabels[$t] ?? $t, 'before_value' => '', 'after_value' => '', 'unit' => '']);
                            @endphp

                            @foreach($displayIndicators as $index => $ind)
                                <div class="indicator-row" style="display:grid; grid-template-columns:2.2fr 1fr 1fr 1.2fr 1fr 40px; gap:8px; align-items:center; padding:10px 14px; border-bottom:1px solid #f1f5f9; background:#fff;">
                                    <div class="name-container">
                                        @if($readOnly)
                                            <input type="text" value="{{ $ind->indicator_name }}" class="input-styled" disabled>
                                        @else
                                            @php $isOther = !in_array($ind->indicator_name, array_values($typeLabels)) && $ind->indicator_name != ''; @endphp
                                            @if($isOther)
                                                <input type="text" name="indicators[{{ $index }}][indicator_name]" value="{{ $ind->indicator_name }}" class="input-styled">
                                            @else
                                                <select name="indicators[{{ $index }}][indicator_name]" class="input-styled indicator-select" onchange="checkOther(this)">
                                                    <option value="">-- เลือก --</option>
                                                    @foreach($typeLabels as $label)
                                                        <option value="{{ $label }}" {{ $ind->indicator_name === $label ? 'selected' : '' }}>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        @endif
                                    </div>
                                    <input type="number" step="any" name="indicators[{{ $index }}][before_value]" value="{{ $ind->before_value }}" placeholder="0" class="input-styled val-before" oninput="calculateDiff(this)" {{ $readOnly ? 'disabled' : '' }}>
                                    <input type="number" step="any" name="indicators[{{ $index }}][after_value]" value="{{ $ind->after_value }}" placeholder="0" class="input-styled val-after" oninput="calculateDiff(this)" {{ $readOnly ? 'disabled' : '' }}>
                                    
                                    <div class="diff-display" style="text-align:center; font-weight:bold; font-size:13px; color:#64748b;">-</div>
                                    
                                    <input type="text" name="indicators[{{ $index }}][unit]" value="{{ $ind->unit }}" placeholder="หน่วย" class="input-styled" {{ $readOnly ? 'disabled' : '' }}>
                                    
                                    @if(!$readOnly)
                                        <button type="button" onclick="removeIndicator(this)" style="background:none; border:none; color:#94a3b8; cursor:pointer; font-size: 16px;">✕</button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @if(!$readOnly)
                        <button type="button" onclick="addIndicator()" class="btn-add-dashed">+ เพิ่มตัวชี้วัด</button>
                    @endif
                </div>

                <div class="form-row" style="display: flex; gap: 20px; margin-top: 24px;">
                    <div class="form-group" style="flex: 1;">
                        <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 8px;">งบประมาณที่ใช้จริง (บาท)</label>
                        <input type="number" name="budget_used" value="{{ old('budget_used', $kaizen->budget_used) }}" step="0.01" class="input-styled" {{ $readOnly ? 'disabled' : '' }} />
                    </div>
                    <div style="flex: 1;"></div>
                </div>

                <div class="form-group" style="margin-top: 24px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 10px;">เป้าหมายที่ตั้งไว้ <span style="color: #ef4444;">*</span></label>
                    <div style="display: flex; gap: 24px;">
                        <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 14px;">
                            <input type="radio" name="is_achieved" value="1" {{ old('is_achieved', $kaizen->is_achieved) == 1 ? 'checked' : '' }} onchange="toggleNotAchieved(false)" {{ $readOnly ? 'disabled' : '' }}> บรรลุเป้าหมาย
                        </label>
                        <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 14px;">
                            <input type="radio" name="is_achieved" value="0" {{ old('is_achieved', $kaizen->is_achieved) == 0 ? 'checked' : '' }} onchange="toggleNotAchieved(true)" {{ $readOnly ? 'disabled' : '' }}> ไม่บรรลุเป้าหมาย
                        </label>
                    </div>
                </div>

                <div id="not_achieved_wrapper" class="form-group" style="display: {{ old('is_achieved', $kaizen->is_achieved) == 0 ? 'block' : 'none' }}; margin-top: 16px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 8px;">เหตุผลที่ไม่บรรลุเป้าหมาย <span style="color: #ef4444;">*</span></label>
                    <textarea name="not_achieved_detail" rows="3" class="input-styled" {{ $readOnly ? 'disabled' : '' }}>{{ old('not_achieved_detail', $kaizen->not_achieved_detail) }}</textarea>
                </div>

                <div class="form-group" style="margin-top: 24px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 12px;">รูปภาพประกอบผลงาน (Result Images)</label>
                    @if(!$readOnly)
                    <div class="upload-zone" onclick="document.getElementById('file-result').click()" style="border: 2px dashed #e2e8f0; border-radius: 12px; padding: 30px; text-align: center; cursor: pointer; transition: all 0.2s;">
                        <input type="file" id="file-result" name="result_images[]" multiple accept="image/*" onchange="previewFiles(this,'preview-result')" style="display:none;" />
                        <div style="color: #64748b; font-size: 14px;">📷 คลิกเพื่อแนบรูปภาพผลลัพธ์</div>
                    </div>
                    @endif
                    <div class="file-previews" id="preview-result" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 12px;">
                        @foreach($kaizen->files->where('file_type', 'actual') as $file)
                            <img src="{{ asset('storage/' . $file->file_path) }}" style="width:100px; height:100px; object-fit:cover; border-radius:8px; border:1px solid #e2e8f0;">
                        @endforeach
                    </div>
                </div>

                @if(!$readOnly)
                <div style="margin-top: 16px; display: flex; justify-content: center; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                    <button type="submit" style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 40px; background: #22c55e; color: #fff; border: none; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 10px rgba(34, 197, 94, 0.25);">
                        บันทึกและส่งรายงานผล <span style="font-size: 18px;">›</span>
                    </button>
                </div>
                @endif
            </div>
        </form>
    </div>
</div>

<style>
    .input-styled { width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; transition: all 0.2s; background: #fff; color: #334155; }
    .input-styled:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); outline: none; }
    .input-styled:disabled { background: #f8fafc; color: #94a3b8; }
    
    .btn-add-dashed { margin-top: 12px; width: 100%; padding: 10px; background: #fff; border: 1px dashed #cbd5e1; border-radius: 8px; color: #64748b; cursor: pointer; font-size: 13px; font-weight: 500; transition: all 0.2s; }
    .btn-add-dashed:hover { background: #f8fafc; border-color: #94a3b8; color: #334155; }

    .diff-positive { color: #10b981 !important; } 
    .diff-negative { color: #ef4444 !important; } 
    
    .upload-zone:hover { background: #f8fafc; border-color: #cbd5e1; }
</style>

<script>
const indicatorOptions = [
    'เพิ่มรายได้', 'ลดรายจ่าย', 'ลดขั้นตอน', 'ลดเวลาการทำงาน', 
    'ปรับปรุงคุณภาพ', 'ลดความเสี่ยง', 'รักษาภาพลักษณ์/ชื่อเสียงองค์กร', 
    'สิ่งประดิษฐ์/นวัตกรรม', 'เปิดบริการใหม่', 'อื่นๆ (ระบุเอง)'
];

let rowIdx = {{ count($displayIndicators) }};

const textIndicatorTypes = ['ปรับปรุงคุณภาพ', 'รักษาภาพลักษณ์/ชื่อเสียงองค์กร', 'สิ่งประดิษฐ์/นวัตกรรม', 'เปิดบริการใหม่'];

function calculateDiff(input) {
    const row = input.closest('.indicator-row');
    const beforeInput = row.querySelector('.val-before');
    const afterInput = row.querySelector('.val-after');
    const display = row.querySelector('.diff-display');

    if (beforeInput.type === 'text') {
        display.innerText = '-';
        display.classList.remove('diff-positive', 'diff-negative');
        return;
    }

    const before = parseFloat(beforeInput.value);
    const after = parseFloat(afterInput.value);

    if (!isNaN(before) && !isNaN(after) && before !== 0) {
        const diff = ((after - before) / Math.abs(before)) * 100;
        display.innerText = (diff > 0 ? '+' : '') + diff.toFixed(1) + '%';
        display.classList.remove('diff-positive', 'diff-negative');
        if (diff > 0) display.classList.add('diff-positive');
        else if (diff < 0) display.classList.add('diff-negative');
    } else {
        display.innerText = '-';
        display.classList.remove('diff-positive', 'diff-negative');
    }
}

function updateIndicatorInputTypes(row) {
    const select = row.querySelector('.indicator-select');
    let indicatorName = select ? select.value : row.querySelector('input[name*="[indicator_name]"]')?.value;
    
    if (!indicatorName) return;

    const beforeInput = row.querySelector('.val-before');
    const afterInput = row.querySelector('.val-after');
    const display = row.querySelector('.diff-display');
    
    if (textIndicatorTypes.includes(indicatorName)) {
        beforeInput.type = 'text';
        afterInput.type = 'text';
        display.innerText = '-';
        display.classList.remove('diff-positive', 'diff-negative');
    } else {
        beforeInput.type = 'number';
        afterInput.type = 'number';
        calculateDiff(beforeInput);
    }
}

function checkOther(select) {
    if (select.value === 'อื่นๆ (ระบุเอง)') {
        const name = select.name;
        const parent = select.parentElement;
        parent.innerHTML = `<input type="text" name="${name}" class="input-styled" placeholder="ชื่อตัวชี้วัด..." autofocus oninput="updateOptions(); updateIndicatorInputTypes(this.closest('.indicator-row'));">`;
    }
    updateOptions();
    if(select.closest('.indicator-row')) updateIndicatorInputTypes(select.closest('.indicator-row'));
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.indicator-row').forEach(row => updateIndicatorInputTypes(row));
});

function addIndicator() {
    rowIdx++;
    const list = document.getElementById('indicators-list');
    const div = document.createElement('div');
    div.className = 'indicator-row';
    div.style.cssText = 'display:grid; grid-template-columns:2.2fr 1fr 1fr 1.2fr 1fr 40px; gap:8px; align-items:center; padding:10px 14px; border-bottom:1px solid #f1f5f9; background:#fff;';
    div.innerHTML = `
        <div class="name-container">
            <select name="indicators[${rowIdx}][indicator_name]" class="input-styled indicator-select" onchange="checkOther(this)">
                <option value="">-- เลือก --</option>
                ${indicatorOptions.map(opt => `<option value="${opt}">${opt}</option>`).join('')}
            </select>
        </div>
        <input type="number" step="any" name="indicators[${rowIdx}][before_value]" placeholder="0" class="input-styled val-before" oninput="calculateDiff(this)">
        <input type="number" step="any" name="indicators[${rowIdx}][after_value]" placeholder="0" class="input-styled val-after" oninput="calculateDiff(this)">
        <div class="diff-display" style="text-align:center; font-weight:bold; font-size:13px; color:#64748b;">-</div>
        <input type="text" name="indicators[${rowIdx}][unit]" placeholder="หน่วย" class="input-styled">
        <button type="button" onclick="removeIndicator(this)" style="background:none; border:none; color:#94a3b8; cursor:pointer; font-size:16px;">✕</button>
    `;
    list.appendChild(div);
    updateOptions();
    updateIndicatorInputTypes(div);
}

function removeIndicator(btn) {
    btn.closest('.indicator-row').remove();
    updateOptions();
}

function updateOptions() {
    const selects = document.querySelectorAll('.indicator-select');
    const selectedValues = Array.from(selects).map(s => s.value).filter(v => v && v !== 'อื่นๆ (ระบุเอง)');

    selects.forEach(s => {
        Array.from(s.options).forEach(opt => {
            if (!opt.value || opt.value === 'อื่นๆ (ระบุเอง)') return;
            opt.disabled = selectedValues.includes(opt.value) && opt.value !== s.value;
            opt.style.display = (selectedValues.includes(opt.value) && opt.value !== s.value) ? 'none' : 'block';
        });
    });
}

function toggleNotAchieved(show) { 
    document.getElementById('not_achieved_wrapper').style.display = show ? 'block' : 'none'; 
}

function previewFiles(input, containerId) {
    const container = document.getElementById(containerId);
    Array.from(input.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style = "width:100px; height:100px; object-fit:cover; border-radius:8px; border:1px solid #e2e8f0;";
            container.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    updateOptions();
    document.querySelectorAll('.val-before').forEach(el => calculateDiff(el));
});
</script>

@endsection