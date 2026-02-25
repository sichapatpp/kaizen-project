@extends('layouts.base')

@section('content')
<style>
@verbatim
    .kaizen-toast {
        visibility: hidden;
        min-width: 250px;
        background-color: #333;
        color: #fff;
        text-align: center;
        border-radius: 8px;
        padding: 16px;
        position: fixed;
        z-index: 9999;
        left: 50%;
        bottom: 30px;
        transform: translateX(-50%);
        font-size: 14px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .kaizen-toast.show {
        visibility: visible;
        -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
        animation: fadein 0.5s, fadeout 0.5s 2.5s;
    }
    @-webkit-keyframes fadein  { from { bottom: 0;    opacity: 0; } to { bottom: 30px; opacity: 1; } }
    @-webkit-keyframes fadeout { from { bottom: 30px; opacity: 1; } to { bottom: 0;    opacity: 0; } }
    @keyframes fadein  { from { bottom: 0;    opacity: 0; } to { bottom: 30px; opacity: 1; } }
    @keyframes fadeout { from { bottom: 30px; opacity: 1; } to { bottom: 0;    opacity: 0; } }
@endverbatim
</style>

    <div class="kaizen-wrapper">
        <div class="kaizen-inner">

            <!-- Header -->
            <div class="kaizen-header">
                <div>
                    <div class="topbar-title">สร้างกิจกรรม Kaizen</div>
                    <div class="topbar-sub">กรอกข้อมูลกิจกรรมปรับปรุงของคุณ</div>
                </div>
                <div style="display: flex; gap: 12px; align-items: center;">
                    <a href="{{ route('activities.draft') }}" class="btn-outline" id="loadDraftBtn">
                        📂 ฉบับร่างของฉัน
                        <span id="draftCountBadge"
                              style="display:none; background:#ef4444; color:#fff;
                                     border-radius:999px; padding:1px 7px;
                                     font-size:12px; margin-left:4px;"></span>
                    </a>
                    <button type="button" class="btn-outline" onclick="saveDraft(event)">
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

                <!-- Draft loaded notification -->
               

                <!-- hidden field เก็บ draft_id -->
                <input type="hidden" name="draft_id" id="draft_id" value="{{ $draftId ?? '' }}">

                <!-- Steps -->
                <div class="kaizen-steps">
                    <div class="step-connector" id="conn1"></div>
                    <div class="step-connector" id="conn2"></div>
                    <div class="step-connector" id="conn3"></div>

                    <div class="step active" id="step-tab-1" onclick="goStep(1)" style="cursor:pointer;">
                        <div class="step-icon">ℹ️</div>
                        <div class="step-label">ข้อมูลทั่วไป</div>
                    </div>
                    <div class="step" id="step-tab-2" onclick="goStep(2)" style="cursor:pointer;">
                        <div class="step-icon">📝</div>
                        <div class="step-label">รายละเอียด</div>
                    </div>
                    <div class="step" id="step-tab-3" onclick="goStep(3)" style="cursor:pointer;">
                        <div class="step-icon">👥</div>
                        <div class="step-label">ผู้ร่วมงาน</div>
                    </div>
                    <div class="step" id="step-tab-4" onclick="goStep(4)" style="cursor:pointer;">
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
                        <input type="text" id="title" name="title" value="{{ old('title') }}" />
                        @error('title')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom:18px;">
                        <label>ประเภทการปรับปรุง <span class="req">*</span></label>
                        <div class="checkbox-grid">
                            @php
                                $types = [
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
                            @endphp
                            @foreach ($types as $value => $label)
                                <label class="checkbox-item">
                                    <input type="checkbox" name="improvement_types[]"
                                           value="{{ $value }}" id="type-{{ $value }}">
                                    <span class="checkmark"></span>
                                    <span class="checkbox-label">{{ $label }}</span>
                                </label>
                            @endforeach

                            <div id="others-detail" style="display:none; margin-left:30px; margin-top:10px;">
                                <input type="text" name="other_improvement_detail" id="other_detail_input"
                                       value="{{ old('other_improvement_detail') }}"
                                       placeholder="ระบุรายละเอียด...">
                            </div>
                        </div>
                        <div class="helper" style="margin-top:8px;">* สามารถเลือกได้มากกว่า 1 รายการ</div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>ผู้ยื่นกิจกรรม <span class="req">*</span></label>
                            <input type="text" id="submitter" name="submitter"
                                   value="{{ Auth::user()->name }}" readonly
                                   style="background:#f9fafb; color:#6b7280;" />
                        </div>
                    </div>

                    <div class="form-footer">
                        <div>
                            <button type="button" class="btn-save-draft" onclick="saveDraft(event)">💾 บันทึกฉบับร่าง</button>
                        </div>
                        <div class="step-info">ขั้นตอน 1 จาก 4</div>
                        <div>
                            <button type="button" class="btn-next" onclick="goStep(2)">ถัดไป <span>›</span></button>
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
                        <textarea id="problem" name="problem">{{ old('problem') }}</textarea>
                        @error('problem')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
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
                        @error('improvement')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
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
                        @error('result')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="upload-zone" onclick="document.getElementById('file-result').click()">
                        <input type="file" id="file-result" name="result_images[]" multiple accept="image/*"
                               onchange="previewFiles(this,'preview-result')" style="display:none;" />
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
 
                    <div class="form-footer">
                        <div><button type="button" class="btn-back" onclick="goStep(1)">‹ ย้อนกลับ</button></div>
                        <div class="step-info">ขั้นตอน 2 จาก 4</div>
                        <div><button type="button" class="btn-next" onclick="goStep(3)">ถัดไป <span>›</span></button></div>
                    </div>
                </div>

                <!-- ═══ STEP 3 ═══ -->
                <div class="kaizen-card" id="page-3" style="display:none;">
                    <div class="section-header">
                        <h3>ผู้ร่วมงาน</h3>
                        <p>ระบุผู้ร่วมงานและสัดส่วนการมีส่วนร่วม</p>
                    </div>

                    <div id="percentAlert" class="percent-alert" style="display:none;">
                        <span class="alert-icon">⚠️</span>
                        <span id="percentMessage">หมายเหตุ: สัดส่วนรวมของผู้ร่วมงานต้องเท่ากับ 100%</span>
                    </div>

                    <div id="collaboratorsList"></div>

                    <div class="form-group" style="margin-top:16px;">
                        <button type="button" class="btn-add-collaborator" onclick="addCollaborator()">
                            <span style="font-size:18px; margin-right:6px;">+</span> เพิ่มผู้ร่วมงาน
                        </button>
                    </div>

                    <div class="form-footer">
                        <div><button type="button" class="btn-back" onclick="goStep(2)">‹ ย้อนกลับ</button></div>
                        <div class="step-info">ขั้นตอน 3 จาก 4</div>
                        <div><button type="button" class="btn-next" onclick="goStep(4)">ถัดไป <span>›</span></button></div>
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
                                <span class="status-badge status-draft" style="margin-top:2px; display:inline-flex;">
                                    <span class="dot"></span>ฉบับร่าง
                                </span>
                            </div>
                            <div>
                                <div class="sb-label">ผู้ยื่น</div>
                                <div class="sb-val">{{ Auth::user()->name }}</div>
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
                        <div>
                            <button type="submit" class="btn-next"
                                    style="background:#22c55e; box-shadow:0 2px 6px rgba(34,197,94,.35);">
                                บันทึกกิจกรรม <span>›</span>
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <!-- Toast — มีเพียงตัวเดียว -->
    <div class="kaizen-toast" id="kaizen-toast">
        <span class="toast-icon">✅</span>
        <span id="toast-msg">บันทึกสำเร็จ</span>
    </div>

    <script>
        var TOTAL_STEPS = 4;
        var availableUsers = @json($users ?? []);

        document.addEventListener('DOMContentLoaded', function () {

            // โหลด improvement_types จาก server (กรณี edit draft)
            var preChecked = {!! json_encode(old('improvement_types', $draftTypes ?? [])) !!};
            if (Array.isArray(preChecked)) {
                preChecked.forEach(function (val) {
                    var cb = document.getElementById('type-' + val);
                    if (cb) cb.checked = true;
                });
                if (preChecked.indexOf('others') !== -1) {
                    document.getElementById('others-detail').style.display = 'block';
                }
            }

            // โหลดผู้ร่วมงาน (กรณี edit draft/rejected)
            @php
                $existingParticipants = old('participants');
                if (!$existingParticipants && (isset($kaizen) || isset($draft))) {
                    $proj = $kaizen ?? $draft;
                    $existingParticipants = $proj->participants->map(function($p) {
                        return ['name' => $p->participant_name, 'percent' => $p->participation_percent];
                    })->toArray();
                }
            @endphp
            @if (!empty($existingParticipants))
                @foreach ($existingParticipants as $index => $p)
                    addCollaborator("{{ addslashes($p['name']) }}", "{{ $p['percent'] }}", {{ $index === 0 ? 'true' : 'false' }});
                @endforeach
            @else
                {{-- กรณีสร้างใหม่ ให้เพิ่มคนยื่นเป็นคนแรก --}}
                addCollaborator("{{ addslashes(Auth::user()->name) }}", "", true);
            @endif

            // แสดง notice ถ้ากำลัง edit draft อยู่
            var draftIdEl = document.getElementById('draft_id');
            if (draftIdEl && draftIdEl.value) {
                var notice = document.getElementById('draftLoadedNotice');
                if (notice) notice.style.display = 'flex';
            }

            // is_achieved
            if ("{{ old('is_achieved') }}" === "0") {
                toggleNotAchieved(true);
            }

            // badge จำนวน draft
            fetchDraftCount();

            // toggle อื่นๆ
            var typeOthers = document.getElementById('type-others');
            if (typeOthers) {
                typeOthers.addEventListener('change', function () {
                    document.getElementById('others-detail').style.display = this.checked ? 'block' : 'none';
                });
            }
        });

        function fetchDraftCount() {
            fetch('{{ route("activities.draftCount") }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var badge = document.getElementById('draftCountBadge');
                if (badge && data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'inline';
                }
            })
            .catch(function () {});
        }

        // ─── เปลี่ยน Step ───
        function goStep(n) {
            if (n === 4) updateSummary();

            for (var i = 1; i <= TOTAL_STEPS; i++) {
                var page = document.getElementById('page-' + i);
                if (page) page.style.display = (i === n) ? 'block' : 'none';

                var tab = document.getElementById('step-tab-' + i);
                if (tab) {
                    tab.classList.toggle('active', i === n);
                    tab.classList.toggle('done', i < n);
                }

                var conn = document.getElementById('conn' + i);
                if (conn) conn.classList.toggle('done', i < n);
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function toggleNotAchieved(show) {
            var wrapper = document.getElementById('not_achieved_wrapper');
            if (wrapper) wrapper.style.display = show ? 'block' : 'none';
        }

        // ─── ผู้ร่วมงาน ───
        var collaboratorCount = 0;

        function addCollaborator(initName, initPercent, isSubmitter) {
            initName    = initName    || '';
            initPercent = initPercent || '';
            isSubmitter = isSubmitter || false;
            collaboratorCount++;

            var container = document.getElementById('collaboratorsList');
            var div = document.createElement('div');
            div.className = 'collaborator-item';
            div.id = 'collab-row-' + collaboratorCount;
            div.style.cssText = 'border:1px solid #e5e7eb; padding:15px; border-radius:8px; margin-bottom:10px; background:#fff;';

            var deleteBtn = isSubmitter ? '' :
                '<button type="button" onclick="removeCollaborator(\'collab-row-' + collaboratorCount + '\')"' +
                ' style="color:red; cursor:pointer; background:none; border:none;">✕ ลบ</button>';

            var userOptions = '<option value="">-- เลือกผู้ร่วมงาน --</option>';
            if (typeof availableUsers !== 'undefined') {
                availableUsers.forEach(function(u) {
                    var selected = (u.name === initName) ? 'selected' : '';
                    userOptions += '<option value="' + u.name + '" ' + selected + '>' + u.name + '</option>';
                });
            }

            var nameInput = isSubmitter ?
                '<input type="text" name="participants[' + collaboratorCount + '][name]"' +
                    ' value="' + initName + '" readonly' +
                    ' style="background:#f9fafb; color:#6b7280;" class="c-name">' :
                '<select name="participants[' + collaboratorCount + '][name]" class="c-name" onchange="updateSummary()" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px; font-size:14px; background:#fff;">' +
                    userOptions +
                '</select>';

            div.innerHTML =
                '<div style="display:flex; justify-content:space-between; margin-bottom:10px;">' +
                    '<strong class="collab-label">ผู้ร่วมงาน #' + collaboratorCount + '</strong>' +
                    deleteBtn +
                '</div>' +
                '<div style="display:grid; grid-template-columns:2fr 1fr; gap:10px;">' +
                    nameInput +
                    '<div class="percent-input-wrapper">' +
                        '<input type="number" name="participants[' + collaboratorCount + '][percent]"' +
                            ' value="' + initPercent + '" placeholder="%"' +
                            ' class="c-percent" min="0" max="100"' +
                            ' oninput="calculateTotalPercent(); updateSummary()" style="width:100%">' +
                    '</div>' +
                '</div>';

            container.appendChild(div);
            reNumberCollaborators();
            calculateTotalPercent();
        }

        function removeCollaborator(rowId) {
            var row = document.getElementById(rowId);
            if (row) row.remove();
            reNumberCollaborators();
            calculateTotalPercent();
            updateSummary();
        }

        function reNumberCollaborators() {
            var items = document.querySelectorAll('.collaborator-item');
            items.forEach(function(item, index) {
                var num = index + 1;
                // Update Label
                var label = item.querySelector('.collab-label');
                if (label) label.textContent = 'ผู้ร่วมงาน #' + num;

                // Update Input Names
                var nameInp = item.querySelector('.c-name');
                if (nameInp) nameInp.name = 'participants[' + num + '][name]';

                var percInp = item.querySelector('.c-percent');
                if (percInp) percInp.name = 'participants[' + num + '][percent]';
            });
        }

        function calculateTotalPercent() {
            var inputs = document.querySelectorAll('.c-percent');
            var total  = 0;
            inputs.forEach(function (i) { total += parseFloat(i.value) || 0; });

            var alertEl = document.getElementById('percentAlert');
            var message = document.getElementById('percentMessage');

            if (inputs.length === 0) { alertEl.style.display = 'none'; return; }

            alertEl.style.display = 'flex';
            if (total === 100) {
                alertEl.style.background = '#f0fdf4';
                alertEl.style.color      = '#166534';
                message.innerHTML = '<strong>✓ สัดส่วนครบถ้วน 100%</strong>';
            } else {
                alertEl.style.background = '#fffbeb';
                alertEl.style.color      = '#92400e';
                message.innerHTML = '<strong>⚠️ สัดส่วนรวมคือ ' + total + '%</strong> (ต้องรวมให้ได้ 100%)';
            }
        }

        function getCollaboratorsData() {
            var list = [];
            document.querySelectorAll('.collaborator-item').forEach(function (item) {
                var name    = item.querySelector('.c-name')    ? item.querySelector('.c-name').value    : '';
                var percent = item.querySelector('.c-percent') ? item.querySelector('.c-percent').value : '';
                if (name) list.push({ name: name, percent: percent });
            });
            return list;
        }

        // ─── Preview รูปภาพ ───
        function previewFiles(input, containerId) {
            var container = document.getElementById(containerId);
            container.innerHTML = '';
            Array.from(input.files).forEach(function (file) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    var imgDiv = document.createElement('div');
                    imgDiv.style.cssText = 'display:inline-block; position:relative; margin:5px;';
                    imgDiv.innerHTML = '<img src="' + e.target.result + '"' +
                        ' style="width:80px; height:80px; object-fit:cover; border-radius:4px; border:1px solid #ddd;">';
                    container.appendChild(imgDiv);
                };
                reader.readAsDataURL(file);
            });
        }

        // ─── บันทึกฉบับร่าง ───
        function saveDraft(e) {
            var btn          = e ? (e.currentTarget || e.target) : null;
            var originalText = btn ? btn.textContent : '';

            if (btn) { btn.disabled = true; btn.textContent = 'กำลังบันทึก...'; }

            var types = Array.from(document.querySelectorAll('input[name="improvement_types[]"]:checked'))
                .map(function (cb) { return cb.value; });

            var payload = {
                draft_id:                 document.getElementById('draft_id').value,
                title:                    document.getElementById('title').value,
                improvement_types:        types,
                other_improvement_detail: document.getElementById('other_detail_input').value,
                problem:                  document.getElementById('problem').value,
                improvement:              document.getElementById('solutionDescription').value,
                result:                   document.getElementById('expectedResult').value,
                participants:             getCollaboratorsData(),
                indicators:               [],
            };

            fetch('{{ route("activities.saveDraft") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    if (data.draft_id) document.getElementById('draft_id').value = data.draft_id;
                    fetchDraftCount();
                    showToast('บันทึกฉบับร่างแล้ว');
                } else {
                    showToast('เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถบันทึกได้'));
                }
            })
            .catch(function () { showToast('เกิดข้อผิดพลาด โปรดลองใหม่'); })
            .finally(function () {
                if (btn) { btn.disabled = false; btn.textContent = originalText; }
            });
        }

        // ─── อัปเดตสรุป Step 4 ───
        function updateSummary() {
            document.getElementById('summary-activity-name').textContent =
                document.getElementById('title').value || '—';

            var types = Array.from(document.querySelectorAll('input[name="improvement_types[]"]:checked'))
                .map(function (cb) {
                    var item = cb.closest('.checkbox-item');
                    return item ? item.querySelector('.checkbox-label').textContent : cb.value;
                });
            document.getElementById('summary-improvement-types').textContent =
                types.length ? types.join(', ') : '—';

            document.getElementById('summary-problem').textContent =
                document.getElementById('problem').value || '—';
            document.getElementById('summary-solution').textContent =
                document.getElementById('solutionDescription').value || '—';
            document.getElementById('summary-result').textContent =
                document.getElementById('expectedResult').value || '—';

            var collabNames = getCollaboratorsData().map(function (c) {
                return c.name + ' (' + c.percent + '%)';
            });
            document.getElementById('summary-collaborators').textContent =
                collabNames.length ? collabNames.join(', ') : '—';
        }

        // ─── Toast ───
        function showToast(msg) {
            var toast = document.getElementById('kaizen-toast');
            var msgEl = document.getElementById('toast-msg');
            if (!toast || !msgEl) return;
            msgEl.textContent = msg;
            toast.classList.add('show');
            setTimeout(function () { toast.classList.remove('show'); }, 3000);
        }
    </script>

@endsection