@extends('layouts.base')

@section('content')

<div class="kaizen-wrapper">
    <div class="kaizen-inner">
        
        <div class="kaizen-header">
            <div>
                <div class="topbar-title">รายงานผลการดำเนินงาน</div>
                <div class="topbar-sub">บันทึกผลลัพธ์หลังการปรับปรุง (กิจกรรม #{{ $kaizen->id }}: {{ $kaizen->title }})</div>
            </div>
            <div>
                <a href="{{ route('activities.status') }}" class="btn-secondary">‹ ย้อนกลับ</a>
            </div>
        </div>

        <form method="POST" action="{{ route('activities.saveReport', $kaizen->id) }}" enctype="multipart/form-data">
            @csrf
            
            <div class="kaizen-card">
                <div class="section-header">
                    <h3>ผลการดำเนินงาน</h3>
                    <p>เปรียบเทียบผลลัพธ์ที่เกิดขึ้นจริง และแนบรูปประกอบ</p>
                </div>

                <!-- Display Previous Info (Readonly) -->
                <div style="background: #f8fafc; padding: 16px; border-radius: 8px; margin-bottom: 24px; border: 1px solid #e2e8f0;">
                    <div style="margin-bottom: 12px;">
                        <label style="font-weight:600; font-size:14px; color:#475569;">ปัญหาที่พบ:</label>
                        <div style="font-size:14px;">{{ $kaizen->problem }}</div>
                    </div>
                    <div>
                        <label style="font-weight:600; font-size:14px; color:#475569;">แนวทางการปรับปรุง:</label>
                        <div style="font-size:14px;">{{ $kaizen->improvement }}</div>
                    </div>
                </div>

                <div class="form-group">
                    <label>ผลลัพธ์ที่เกิดขึ้นจริง (Result) <span class="req">*</span></label>
                    <textarea name="result" rows="5" required>{{ old('result', $kaizen->result) }}</textarea>
                    <div class="helper">อธิบายผลลัพธ์ที่ได้หลังการปรับปรุง ว่าดีขึ้นอย่างไร</div>
                </div>

                <div class="form-group">
                    <label>รูปภาพประกอบผลงาน (Result Images)</label>
                    <div class="upload-zone" onclick="document.getElementById('file-result').click()">
                        <input type="file" id="file-result" name="result_images[]" multiple accept="image/*"
                            onchange="previewFiles(this,'preview-result')" style="display:none;" />
                        <div class="upload-icon">📷</div>
                        <div class="upload-text"><span>คลิกเพื่อแนบรูปภาพ</span> หรือลากมาวาง</div>
                        <div class="upload-sub">รับ JPG, PNG, WEBP (ไม่เกิน 5 MB / ต่อรูป)</div>
                    </div>
                    <div class="file-previews" id="preview-result">
                        <!-- Show existing result images if any -->
                        @foreach($kaizen->files->where('file_type', 'result') as $file)
                            <div style="display:inline-block; position:relative; margin:5px;">
                                <img src="{{ asset('storage/' . $file->file_path) }}" style="width:80px; height:80px; object-fit:cover; border-radius:4px; border:1px solid #ddd;">
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn-primary" style="width: 100%; justify-content: center;">
                        💾 บันทึกและส่งรายงานผล
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
function previewFiles(input, containerId) {
    const container = document.getElementById(containerId);
    // Don't clear existing images immediately if you want to keep them visually, 
    // but usually new upload replaces or appends. 
    // For simplicity, we append new previews.
    
    Array.from(input.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const imgDiv = document.createElement('div');
            imgDiv.style = "display:inline-block; position:relative; margin:5px;";
            imgDiv.innerHTML = `<img src="${e.target.result}" style="width:80px; height:80px; object-fit:cover; border-radius:4px; border:1px solid #ddd;">`;
            container.appendChild(imgDiv);
        };
        reader.readAsDataURL(file);
    });
}
</script>
@endsection
