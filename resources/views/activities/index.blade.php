@extends('layouts.base')

@section('content')


    <div class="kaizen-wrapper">
        <div class="kaizen-inner">

            <!-- Header -->
            <div class="kaizen-header">
                <div>
                    <div class="topbar-title">สร้างกิจกรรม Kaizen</div>
                    <div class="topbar-sub">กรอกข้อมูลกิจกรรมปรับปรุงของคุณ</div>
                </div>
                <div style="display: flex; gap: 12px; align-items: center;">
                    <button class="btn-outline" onclick="loadDraft()" id="loadDraftBtn" style="display: none;">
                        📂 โหลดฉบับร่าง
                    </button>
                    <button class="btn-outline" onclick="saveDraft()">
                        💾 บันทึกฉบับร่าง
                    </button>
                </div>
            </div>

            {{-- แสดง error รวม --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin:0; padding-left:18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('activities.store') }}" enctype="multipart/form-data">
                @csrf
                <!-- Draft Notification -->
                <div id="draftNotification" class="draft-notification" style="display: none;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 20px;">📝</span>
                        <div>
                            <div style="font-weight: 600; font-size: 14px;">พบฉบับร่างที่บันทึกไว้</div>
                            <div style="font-size: 12px; color: #6b7280; margin-top: 2px;">
                                บันทึกล่าสุด: <span id="draftLastSaved"></span>
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <button class="btn-load-draft" onclick="loadDraft()">โหลดฉบับร่าง</button>
                        <button class="btn-dismiss-draft" onclick="dismissDraft()">ยกเลิก</button>
                    </div>
                </div>

                <!-- Steps -->
                <div class="kaizen-steps">
                    <div class="step-connector" id="conn1"></div>
                    <div class="step-connector" id="conn2"></div>
                    <div class="step-connector" id="conn3"></div>

                    <div class="step active" id="step-tab-1" onclick="goStep(1)" style="cursor: pointer;">
                        <div class="step-icon">ℹ️</div>
                        <div class="step-label">ข้อมูลทั่วไป</div>
                    </div>
                    <div class="step" id="step-tab-2" onclick="goStep(2)" style="cursor: pointer;">
                        <div class="step-icon">📝</div>
                        <div class="step-label">รายละเอียด</div>
                    </div>
                    <div class="step" id="step-tab-3" onclick="goStep(3)" style="cursor: pointer;">
                        <div class="step-icon">👥</div>
                        <div class="step-label">ผู้ร่วมงาน</div>
                    </div>
                    <div class="step" id="step-tab-4" onclick="goStep(4)" style="cursor: pointer;">
                        <div class="step-icon">📤</div>
                        <div class="step-label">รับทราบ</div>
                    </div>
                </div>

                <!-- ═══ STEP 1 ═══ -->
                <div class="kaizen-card" id="page-1">
                    <div class="section-header">
                        <h3>ข้อมูลทั่วไป</h3>
                        <p>กรอกชื่อกิจกรรม และข้อมูลพื้นฐาน</p>
                    </div>

                    <div class="form-group" style="margin-bottom:18px;">
                        <label>ชื่อกิจกรรม <span class="req">*</span></label>
                        <input type="text" id="title" name="title"value="{{ old('title') }}" />
                        @error('title')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- <div class="form-group">
                        <label>ปีงบประมาณ (Fiscal Year) <span class="req">*</span></label>
                        <input type="number" name="fiscalyear" value="{{ date('Y') + 543 }}" required />
                    </div> --}}

                    <div class="form-group" style="margin-bottom:18px;">
                        <label>ประเภทการปรับปรุง <span class="req">*</span></label>
                        <div class="checkbox-grid">
                            @php
                                $types = [
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
                            @endphp
                            @foreach ($types as $value => $label)
                                <label class="checkbox-item">
                                    <input type="checkbox" name="improvement_types[]" value="{{ $value }}"
                                        id="type-{{ $value }}">
                                    <span class="checkmark"></span>
                                    <span class="checkbox-label">{{ $label }}</span>
                                </label>
                            @endforeach

                            <!-- ระบุรายละเอียด -->
                            <div id="others-detail" style="display: none; margin-left: 30px; margin-top: 10px;">
                                <input type="text" name="improvement" id="other_detail_input"
                                    value="{{ old('improvement') }}">
                            </div>
                        </div>
                        <div class="helper" style="margin-top:8px;">* สามารถเลือกได้มากกว่า 1 รายการ</div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>ผู้ยื่นกิจกรรม <span class="req">*</span></label>
                            <input type="text" id="submitter" name="submitter" value="สิชาพัทธ สุขวิชัย" readonly
                                style="background:#f9fafb;color:#6b7280;" />
                        </div>

                    </div>

                    <div class="form-footer">
                        <div><button type="button" class="btn-save-draft" onclick="saveDraft()">💾
                                บันทึกฉบับร่าง</button>
                        </div>
                        <div class="step-info">ขั้นตอน 1 จาก 4</div>
                        <div><button type="button" class="btn-next" onclick="goStep(2)">ถัดไป <span>›</span></button>
                        </div>
                    </div>
                </div>

                <!-- ═══ STEP 2 ═══ -->
                <div class="kaizen-card" id="page-2" style="display:none;">
                    <div class="section-header">
                        <h3>รายละเอียดกิจกรรม</h3>
                        <p>อธิบายปัญหา แนวทาง และผลที่คาดว่าจะได้รับ พร้อมแนบรูปภาพ</p>
                    </div>

                    <!-- ปัญหาที่พบ -->
                    <div class="form-group" style="margin-bottom:6px;">
                        <label>ปัญหาที่พบ <span class="req">*</span></label>
                        <textarea id="problem"name="problem">{{ old('problem') }}</textarea>
                    </div>
                    <div class="upload-zone" onclick="document.getElementById('file-problem').click()">
                        <input type="file" id="file-problem" name="problem_images[]" multiple accept="image/*"
                            onchange="previewFiles(this,'preview-problem')" style="display:none;" />
                        <div class="upload-icon">📷</div>
                        <div class="upload-text"><span>คลิกเพื่อแนบรูปภาพ</span> หรือลากมาวาง</div>
                        <div class="upload-sub">รับ JPG, PNG, WEBP (ไม่เกิน 5 MB / ต่อรูป)</div>
                    </div>
                    <div class="file-previews" id="preview-problem"></div>
                    <div class="sample-img-note">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" />
                            <circle cx="8.5" cy="8.5" r="1.5" />
                            <polyline points="21 15 16 10 5 21" />
                        </svg>
                        ตัวอย่าง: แนบรูปสภาพพื้นที่จริงที่มีปัญหา เช่น รูปที่เห็นความรก จุดอันตราย หรือกระบวนการที่ช้า
                    </div>

                    <hr class="divider" />

                    <!-- แนวทาง -->
                    <div class="form-group" style="margin-bottom:6px;">
                        <label>แนวทางการปรับปรุง <span class="req">*</span></label>
                        <textarea id="solutionDescription" name="improvement">{{ old('improvement') }}</textarea>
                    </div>
                    <div class="upload-zone" onclick="document.getElementById('file-solution').click()">
                        <input type="file" id="file-solution" name="solution_images[]" multiple accept="image/*"
                            onchange="previewFiles(this,'preview-solution')" style="display:none;" />
                        <div class="upload-icon">📷</div>
                        <div class="upload-text"><span>คลิกเพื่อแนบรูปภาพ</span> หรือลากมาวาง</div>
                        <div class="upload-sub">รับ JPG, PNG, WEBP (ไม่เกิน 5 MB / ต่อรูป)</div>
                    </div>
                    <div class="file-previews" id="preview-solution"></div>
                    <div class="sample-img-note">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" />
                            <circle cx="8.5" cy="8.5" r="1.5" />
                            <polyline points="21 15 16 10 5 21" />
                        </svg>
                        ตัวอย่าง: แนบแผนงาน, แผนผัง, หรือรูประหว่างดำเนินการปรับปรุง
                    </div>

                    <hr class="divider" />

                    <!-- ผลที่คาดว่าจะได้รับ -->
                    <div class="form-group" style="margin-bottom:6px;">
                        <label>ผลที่คาดว่าจะได้รับ <span class="req">*</span></label>
                        <textarea id="expectedResult" name="result">{{ old('result') }}</textarea>
                    </div>
                    <div class="upload-zone" onclick="document.getElementById('file-result').click()">
                        <input type="file" id="file-result" name="result_images[]" multiple accept="image/*"
                            onchange="previewFiles(this,'preview-result')" style="display:none;" name="result_images[]"
                            multiple />
                        <div class="upload-icon">📷</div>
                        <div class="upload-text"><span>คลิกเพื่อแนบรูปภาพ</span> หรือลากมาวาง</div>
                        <div class="upload-sub">รับ JPG, PNG, WEBP (ไม่เกิน 5 MB / ต่อรูป)</div>
                    </div>
                    <div class="file-previews" id="preview-result"></div>
                    <div class="sample-img-note">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" />
                            <circle cx="8.5" cy="8.5" r="1.5" />
                            <polyline points="21 15 16 10 5 21" />
                        </svg>
                        ตัวอย่าง: แนบรูปหลังปรับปรุง, กราฟเทียบผล Before/After, รายงานผลเงิน
                    </div>
<hr class="divider" />
<div class="section-header" style="margin-top: 30px; margin-bottom: 20px;">
    <h3>📊 ตัวชี้วัดผลสำเร็จ (Quantitative Results)</h3>
    <p>.......</p>
</div>


                    <div class="form-footer">
                        <div><button type="button" class="btn-back" onclick="goStep(1)">‹ ย้อนกลับ</button></div>
                        <div class="step-info">ขั้นตอน 2 จาก 4</div>
                        <div><button type="button" class="btn-next" onclick="goStep(3)">ถัดไป <span>›</span></button>
                        </div>
                    </div>
                </div>

                <!-- ═══ STEP 3 ═══ -->
                <div class="kaizen-card" id="page-3" style="display:none;">
                    <div class="section-header">
                        <h3>ผู้ร่วมงาน</h3>
                        <p>ระบุผู้ร่วมงานและสัดส่วนการมีส่วนร่วม</p>
                    </div>

                    <!-- แจ้งเตือนสัดส่วน -->
                    <div id="percentAlert" class="percent-alert" style="display:none;">
                        <span class="alert-icon">⚠️</span>
                        <span id="percentMessage">หมายเหตุ: สัดส่วนรวมของผู้ร่วมงานต้องเท่ากับ 100%</span>
                    </div>

                    <!-- รายการผู้ร่วมงาน -->
                    <div id="collaboratorsList"></div>

                    <!-- ปุ่มเพิ่มผู้ร่วมงาน -->
                    <div class="form-group" style="margin-top:16px;">
                        <button type="button" class="btn-add-collaborator" onclick="addCollaborator()">
                            <span style="font-size:18px;margin-right:6px;">+</span> เพิ่มผู้ร่วมงาน
                        </button>
                    </div>

                    <div class="form-footer">
                        <div><button type="button" class="btn-back" onclick="goStep(2)">‹ ย้อนกลับ</button></div>
                        <div class="step-info">ขั้นตอน 3 จาก 4</div>
                        <div><button type="button" class="btn-next" onclick="goStep(4)">ถัดไป <span>›</span></button>
                        </div>
                    </div>
                </div>

                <!-- ═══ STEP 4 ═══ -->
                <div class="kaizen-card" id="page-4" style="display:none;">
                    <div class="section-header">
                        <h3>ตรวจสอบและบันทึก</h3>
                        <p>ตรวจทานข้อมูลทั้งหมดก่อนบันทึกเข้าระบบ</p>
                    </div>

                    <div class="summary-box">
                        <div class="sb-title">📋 สรุปกิจกรรม</div>
                        <div class="sb-grid">
                            <div>
                                <div class="sb-label">ชื่อกิจกรรม</div>
                                <div class="sb-val" id="summary-activity-name">—</div>
                            </div>
                            <div>
                                <div class="sb-label">สถานะ</div>
                                <span class="status-badge status-draft" style="margin-top:2px;display:inline-flex;"><span
                                        class="dot"></span>ฉบับร่าง</span>
                            </div>
                            <div>
                                <div class="sb-label">ผู้ยื่น</div>
                                <div class="sb-val">สิชาพัทธ สุขวิชัย</div>
                            </div>
                        </div>
                        <hr class="sb-divider" />
                        <div class="sb-section">
                            <div class="sb-label">ประเภทการปรับปรุง</div>
                            <div class="sb-val" id="summary-improvement-types">—</div>
                        </div>
                        <div class="sb-section">
                            <div class="sb-label">ปัญหาที่พบ</div>
                            <div class="sb-val" id="summary-problem">—</div>
                        </div>
                        <div class="sb-section">
                            <div class="sb-label">แนวทาง</div>
                            <div class="sb-val" id="summary-solution">—</div>
                        </div>
                        <div class="sb-section">
                            <div class="sb-label">ผลที่คาดว่าจะได้รับ</div>
                            <div class="sb-val" id="summary-result">—</div>
                        </div>
                        <div class="sb-section">
                            <div class="sb-label">ผู้ร่วมงาน</div>
                            <div class="sb-val" id="summary-collaborators">—</div>
                        </div>
                    </div>

                    <div class="form-footer">
                        <div><button type="button" class="btn-back" onclick="goStep(3)">‹ ย้อนกลับ</button></div>
                        <div class="step-info">ขั้นตอน 4 จาก 4</div>
                        <div><button class="btn-next" style="background:#22c55e;box-shadow:0 2px 6px rgba(34,197,94,.35);"
                                type="submit">บันทึกกิจกรรม <span>›</span></button></div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast -->
    <div class="kaizen-toast" id="toast">
        <span class="toast-icon">✅</span>
        <span id="toast-msg">บันทึกสำเร็จ</span>
    </div>

    <script>
        const DRAFT_STORAGE_KEY = 'kaizen_draft';

        document.addEventListener('DOMContentLoaded', function() {
            checkForDraft();

            //  Checkbox 
            const type10 = document.getElementById('type-others');
            if (type10) {
                type10.addEventListener('change', function() {
                    document.getElementById('others-detail').style.display = this.checked ? 'block' :
                        'none';
                });
            }
        });

        // ─── การคำนวณและสรุป ****** ───
        function updateSummary() {
            // แก้ ID ให้ตรงกับ HTML 
            const titleVal = document.getElementById('title').value;
            document.getElementById('summary-activity-name').textContent = titleVal || '—';

            document.getElementById('summary-problem').textContent = document.getElementById('problem').value || '—';
            document.getElementById('summary-solution').textContent = document.getElementById('solutionDescription')
                .value || '—';
            document.getElementById('summary-result').textContent = document.getElementById('expectedResult').value || '—';

            // ประเภทการปรับปรุง
            const types = Array.from(document.querySelectorAll('input[name="improvement_types[]"]:checked'))
                .map(cb => {
                    return cb.parentNode.querySelector('.checkbox-label').textContent;
                });

            // แสดงผลในหน้าสรุป
            const summaryElem = document.getElementById('summary-improvement-types');
            if (summaryElem) {
                summaryElem.textContent = types.join(', ') || '—';
            }
            // ผู้ร่วมงาน
            const collaborators = getCollaboratorsData();
            document.getElementById('summary-collaborators').textContent = collaborators.map(c =>
                `${c.name} (${c.percent}%)`).join(', ') || '—';
        }

        // ─── ผู้ร่วมงานและสัดส่วน 100% ───
        let collaboratorCount = 0;

        function addCollaborator() {
            collaboratorCount++;
            const container = document.getElementById('collaboratorsList');
            const div = document.createElement('div');
            div.className = 'collaborator-item';
            div.id = `collab-row-${collaboratorCount}`;
            div.style = "border:1px solid #e5e7eb; padding:15px; border-radius:8px; margin-bottom:10px; background:#fff;";

            div.innerHTML = `
        <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
            <strong>ผู้ร่วมงาน #${collaboratorCount}</strong>
            <button type="button" onclick="document.getElementById('collab-row-${collaboratorCount}').remove(); calculateTotalPercent(); updateSummary();" style="color:red; cursor:pointer; background:none; border:none;">✕ ลบ</button>
        </div>
        <div style="display:grid; grid-template-columns: 2fr 1fr; gap:10px;">
            <input type="text" name="participants[${collaboratorCount}][name]" placeholder="ชื่อ-นามสกุล" class="c-name" oninput="updateSummary()">
            <div class="percent-input-wrapper">
                <input type="number" name="participants[${collaboratorCount}][percent]" placeholder="%" class="c-percent" oninput="calculateTotalPercent(); updateSummary()" style="width:100%">
            </div>
        </div>
    `;
            container.appendChild(div);
            calculateTotalPercent();
        }

        function calculateTotalPercent() {
            const inputs = document.querySelectorAll('.c-percent');
            let total = 0;
            inputs.forEach(i => total += parseFloat(i.value) || 0);

            const alert = document.getElementById('percentAlert');
            const message = document.getElementById('percentMessage');

            if (inputs.length === 0) {
                alert.style.display = 'none';
                return;
            }

            alert.style.display = 'flex';
            if (total === 100) {
                alert.style.background = "#f0fdf4";
                alert.style.color = "#166534";
                message.innerHTML = `<strong>✓ สัดส่วนครบถ้วน 100%</strong>`;
            } else {
                alert.style.background = "#fffbeb";
                alert.style.color = "#92400e";
                message.innerHTML = `<strong>⚠️ สัดส่วนรวมคือ ${total}%</strong> (ต้องรวมให้ได้ 100%)`;
            }
        }

        function getCollaboratorsData() {
            const list = [];
            document.querySelectorAll('.collaborator-item').forEach(item => {
                const name = item.querySelector('.c-name').value;
                const percent = item.querySelector('.c-percent').value;
                if (name) list.push({
                    name,
                    percent
                });
            });
            return list;
        }

        // ─── แสดงรูปภาพ (File Preview) ───
        function previewFiles(input, containerId) {
            const container = document.getElementById(containerId);
            container.innerHTML = ''; // ล้างรูปเก่า
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = e => {
                    const imgDiv = document.createElement('div');
                    imgDiv.style = "display:inline-block; position:relative; margin:5px;";
                    imgDiv.innerHTML =
                        `<img src="${e.target.result}" style="width:80px; height:80px; object-fit:cover; border-radius:4px; border:1px solid #ddd;">`;
                    container.appendChild(imgDiv);
                };
                reader.readAsDataURL(file);
            });
        }

        // ─── เปลี่ยนหน้า ───
        function goStep(n) {
            if (n === 4) updateSummary();
            for (let i = 1; i <= 4; i++) {
                const page = document.getElementById('page-' + i);
                if (page) page.style.display = (i === n) ? 'block' : 'none';
                const tab = document.getElementById('step-tab-' + i);
                if (tab) {
                    tab.classList.toggle('active', i === n);
                    tab.classList.toggle('done', i < n);
                }
            }
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // ─── บันทึกฉบับร่าง  ───
        function saveDraft() {
            const data = {
                timestamp: new Date().toISOString(),
                data: {
                    title: document.getElementById('title').value,
                    problem: document.getElementById('problem').value,
                    improvement: document.getElementById('solutionDescription').value,
                    result: document.getElementById('expectedResult').value,
                    collaborators: getCollaboratorsData()
                }
            };
            localStorage.setItem(DRAFT_STORAGE_KEY, JSON.stringify(data));
            showToast('บันทึกฉบับร่างแล้ว');
        }

        function loadDraft() {
            const draft = localStorage.getItem(DRAFT_STORAGE_KEY);
            if (!draft) return;
            const d = JSON.parse(draft).data;

            document.getElementById('title').value = d.title || '';
            document.getElementById('problem').value = d.problem || '';
            document.getElementById('solutionDescription').value = d.solution || '';
            document.getElementById('expectedResult').value = d.result || '';

            if (d.collaborators) {
                document.getElementById('collaboratorsList').innerHTML = '';
                d.collaborators.forEach(c => {
                    addCollaborator();
                    const last = document.querySelector('.collaborator-item:last-child');
                    last.querySelector('.c-name').value = c.name;
                    last.querySelector('.c-percent').value = c.percent;
                });
            }
            calculateTotalPercent();
            dismissDraft();
            showToast('โหลดข้อมูลร่างสำเร็จ');
        }

        function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toast-msg').textContent = msg;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }

        function dismissDraft() {
            document.getElementById('draftNotification').style.display = 'none';
        }

        function checkForDraft() {
            const draft = localStorage.getItem(DRAFT_STORAGE_KEY);
            if (draft) {
                document.getElementById('draftNotification').style.display = 'flex';
                document.getElementById('loadDraftBtn').style.display = 'block';
                const time = JSON.parse(draft).timestamp;
                document.getElementById('draftLastSaved').textContent = new Date(time).toLocaleString('th-TH');
            }
        }
    </script>
@endsection
