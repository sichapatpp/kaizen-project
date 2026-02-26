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
        transition: visibility 0s, opacity 0.5s linear;
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
  <div class="kaizen-inner" style="max-width: 1400px;">

    <div class="kaizen-header" style="margin-bottom: 24px;">
      <div>
        <div class="topbar-title">อนุมัติและรายงาน</div>
        <div class="topbar-sub">ตรวจสอบและอนุมัติกิจกรรมที่รอดำเนินการ</div>
      </div>
      <div>
        <a href="{{ route('activities.status') }}" class="btn-secondary" style="text-decoration: none;">
            📊 ติดตามสถานะ
        </a>
      </div>
    </div>

    <div class="tab-navigation" style="margin-bottom: 24px;">
      <button class="tab-btn active" onclick="switchTab('pending')">
        รออนุมัติ <span class="badge" id="pendingCount">0</span>
      </button>
      <button class="tab-btn" onclick="switchTab('history')">
        รายงาน
      </button>
    </div>

    {{-- TAB: รออนุมัติ --}}
    <div id="pendingTab" class="tab-content active">

      <div style="margin-bottom: 24px;">
        <div style="display: flex; flex-wrap: wrap; gap: 8px; align-items: center; margin-bottom: 16px;">
          <span style="font-size: 13px; color: #64748b; font-weight: 500;">กรองสถานะ:</span>
          <button class="status-filter-btn active" data-status="all" onclick="setStatusFilter('all', this)"
            style="padding: 6px 14px; border-radius: 20px; border: 1.5px solid #3b82f6; background: #3b82f6; color: white; font-size: 13px; cursor: pointer; font-weight: 500; transition: all 0.2s;">
            ทั้งหมด
          </button>
          <button class="status-filter-btn" data-status="waiting_for_chairman_approval" onclick="setStatusFilter('waiting_for_chairman_approval', this)"
            style="padding: 6px 14px; border-radius: 20px; border: 1.5px solid #e2e8f0; background: white; color: #475569; font-size: 13px; cursor: pointer; font-weight: 500; transition: all 0.2s;">
            👑 รอประธานอนุมัติ
          </button>
          <button class="status-filter-btn" data-status="waiting_for_manager_result_approval" onclick="setStatusFilter('waiting_for_manager_result_approval', this)"
            style="padding: 6px 14px; border-radius: 20px; border: 1.5px solid #e2e8f0; background: white; color: #475569; font-size: 13px; cursor: pointer; font-weight: 500; transition: all 0.2s;">
            📋 รอหัวหน้าอนุมัติผล
          </button>
          <button class="status-filter-btn" data-status="pending" onclick="setStatusFilter('pending', this)"
            style="padding: 6px 14px; border-radius: 20px; border: 1.5px solid #e2e8f0; background: white; color: #475569; font-size: 13px; cursor: pointer; font-weight: 500; transition: all 0.2s;">
            🕐 รอพิจารณา (รอบที่ 1)
          </button>
        </div>

        <div class="filter-grid">
          <div class="filter-item" style="grid-column: span 2;">
            <label>🔍 ค้นหา</label>
            <input type="text" id="pendingSearch" placeholder="ค้นหาชื่อกิจกรรม, ผู้ยื่น..." oninput="filterPending()" />
          </div>
          <div class="filter-item">
            <label>🏷️ ประเภท</label>
            <select id="pendingTypeFilter" onchange="filterPending()">
              <option value="">ทั้งหมด</option>
              <option value="increase_revenue">เพิ่มรายได้</option>
              <option value="reduce_expenses">ลดรายจ่าย</option>
              <option value="reduce_steps">ลดขั้นตอน</option>
              <option value="reduce_time">ลดเวลาการทำงาน</option>
              <option value="improve_quality">ปรับปรุงคุณภาพ</option>
              <option value="reduce_risk">ลดความเสี่ยง</option>
              <option value="maintain_image">รักษาภาพลักษณ์/ชื่อเสียงองค์กร</option>
              <option value="innovation">สิ่งประดิษฐ์/นวัตกรรม</option>
              <option value="new_service">เปิดบริการใหม่</option>
              <option value="others">อื่นๆ</option>
            </select>
          </div>
        </div>
      </div>

      <div id="pendingList"></div>

      <div id="emptyPending" class="empty-state" style="display: none;">
        <div class="empty-icon">✅</div>
        <div class="empty-title">ไม่มีรายการรออนุมัติ</div>
        <div class="empty-text">ไม่พบกิจกรรมที่รอการอนุมัติในขณะนี้</div>
      </div>
    </div>

    {{-- TAB: รายงาน --}}
    <div id="historyTab" class="tab-content" style="display: none;">
      <div class="filter-section" style="margin-bottom: 24px;">
        <div class="filter-grid">
          <div class="filter-item" style="grid-column: span 2;">
            <label>🔍 ค้นหา</label>
            <input type="text" id="historySearch" placeholder="ค้นหากิจกรรม, ผู้ยื่น..." oninput="filterHistory()" />
          </div>
          <div class="filter-item">
            <label>📊 สถานะ</label>
            <select id="historyStatusFilter" onchange="filterHistory()">
              <option value="">ทั้งหมด</option>
              <option value="completed">เสร็จสิ้น</option>
              <option value="rejected">ไม่อนุมัติ</option>
            </select>
          </div>
        </div>
      </div>

      <div class="kaizen-card" style="padding: 0; overflow: hidden;">
        <div class="table-header">
          <h3 style="margin: 0;">รายงานกิจกรรมทั้งหมด</h3>
          <div class="table-info"><span id="historyCount">0</span> รายการ</div>
        </div>
        <div class="table-container">
          <table class="kaizen-table" id="historyTable">
            <thead>
              <tr>
                <th style="min-width: 250px;">ชื่อกิจกรรม</th>
                <th style="width: 140px;">ผู้ยื่น</th>
                <th style="width: 180px;">สถานะ</th>
                <th style="width: 150px;">วันที่อัปเดต</th>
                <th style="width: 100px;">จัดการ</th>
              </tr>
            </thead>
            <tbody id="historyTableBody"></tbody>
          </table>
        </div>
        <div id="emptyHistory" class="empty-state" style="display: none;">
          <div class="empty-icon">📋</div>
          <div class="empty-title">ไม่พบข้อมูล</div>
          <div class="empty-text">ไม่มีกิจกรรมที่ตรงกับเงื่อนไขที่เลือก</div>
        </div>
      </div>
    </div>

  </div>
</div>

{{-- Modal: รายละเอียด --}}
<div class="modal-overlay" id="detailModal">
  <div class="modal-container">
    <div class="modal-header">
      <div>
        <h2 id="modalTitle" style="margin: 0; font-size: 20px; font-weight: 700; color: #1e293b;">รายละเอียดกิจกรรม</h2>
        <p id="modalSubtitle" style="margin: 4px 0 0 0; font-size: 14px; color: #64748b;"></p>
      </div>
      <button class="btn-close-modal" id="btnCloseDetailModal">✕</button>
    </div>
    <div class="modal-body">
      <div style="margin-bottom: 24px;">
        <span id="modalStatus" class="status-badge status-pending">
          <span class="dot"></span>รอพิจารณา
        </span>
      </div>
      <div class="detail-grid">
        <div class="detail-item">
          <div class="detail-label">📋 ชื่อกิจกรรม</div>
          <div class="detail-value" id="detailActivityName">—</div>
        </div>
        <div class="detail-item">
          <div class="detail-label">🏷️ ประเภทการปรับปรุง</div>
          <div class="detail-value" id="detailTypes">—</div>
        </div>
        <div class="detail-item">
          <div class="detail-label">👤 ผู้ยื่นกิจกรรม</div>
          <div class="detail-value" id="detailSubmitter">—</div>
        </div>
        <div class="detail-item">
          <div class="detail-value" id="detailSubmitDate">—</div>
        </div>
      </div>
      <hr class="modal-divider" />
      <!-- ส่วนที่ 1: ปัญหาที่พบ -->
      <div class="detail-section">
        <div class="section-title">❗ ปัญหาที่พบ</div>
        <div class="section-content" id="detailProblem">—</div>
        <div class="image-gallery" id="problemImages"></div>
      </div>
      <hr class="modal-divider" />
      <!-- ส่วนที่ 2: แนวทางการปรับปรุง -->
      <div class="detail-section">
        <div class="section-title">💡 แนวทางการปรับปรุง</div>
        <div class="section-content" id="detailSolution">—</div>
        <div class="image-gallery" id="solutionImages"></div>
      </div>
      <hr class="modal-divider" />
      <!-- ส่วนที่ 3: ผลที่คาดว่าจะได้รับ -->
      <div class="detail-section">
        <div class="section-title">🎯 ผลที่คาดว่าจะได้รับ</div>
        <div class="section-content" id="detailResult">—</div>
        <div class="image-gallery" id="resultImages"></div>
      </div>
      <hr class="modal-divider" />
      <!-- ส่วนที่ 4: รูปภาพประกอบผลงาน — แสดงเสมอ ไม่ขึ้นกับ reportSection -->
      <div class="detail-section">
        <div class="section-title">🖼️ รูปภาพประกอบผลงาน</div>
        <div class="image-gallery" id="actualImages">
          <span id="actualImagesEmpty" style="color:#94a3b8; font-size:13px;">ยังไม่มีรูปภาพประกอบผลงาน</span>
        </div>
      </div>

      <!-- ══ ส่วนผลการดำเนินงาน (รอบที่ 2) แสดงเฉพาะสถานะที่มีรายงาน ══ -->
      <div id="reportSection" style="display: none;">
        <hr class="modal-divider" />
        <div class="detail-section">
          <div class="section-title">📊 ผลการดำเนินงาน</div>

          <!-- ผลลัพธ์ที่เกิดขึ้นจริง -->
          <div id="actualResultBlock" style="display:none; margin-top:14px;">
            <div style="font-size:12px; font-weight:600; color:#64748b; margin-bottom:4px; text-transform:uppercase; letter-spacing:.5px;">ผลลัพธ์ที่เกิดขึ้นจริง</div>
            <div class="section-content" id="detailActualResult" style="background:#f0fdf4; border-left:3px solid #22c55e; padding:10px 14px; border-radius:0 6px 6px 0;">—</div>
          </div>

          <!-- รายละเอียดการดำเนินงานเพิ่มเติม -->
          <div id="performanceDetailBlock" style="display:none; margin-top:14px;">
            <div style="font-size:12px; font-weight:600; color:#64748b; margin-bottom:4px; text-transform:uppercase; letter-spacing:.5px;">รายละเอียดการดำเนินงานเพิ่มเติม</div>
            <div class="section-content" id="detailPerformanceDetail" style="background:#f8fafc; padding:10px 14px; border-radius:6px; border:1px solid #e2e8f0;">—</div>
          </div>

          <!-- ตัวชี้วัด -->
          <div id="indicatorsBlock" style="display:none; margin-top:14px;">
            <div style="font-size:12px; font-weight:600; color:#64748b; margin-bottom:8px; text-transform:uppercase; letter-spacing:.5px;">ตัวชี้วัด (Performance Indicators)</div>
            <div style="border:1px solid #e2e8f0; border-radius:10px; overflow:hidden;">
              <div style="display:grid; grid-template-columns:2.2fr 1fr 1fr 1.2fr 1fr; gap:8px; background:#f8fafc; padding:10px 14px; border-bottom:1px solid #e2e8f0; font-size:11px; font-weight:700; color:#475569; text-align:center;">
                <div style="text-align:left;">ชื่อตัวชี้วัด</div>
                <div>ก่อน</div>
                <div>หลัง</div>
                <div>ผลต่าง (%)</div>
                <div>หน่วย</div>
              </div>
              <div id="detailIndicatorRows"></div>
            </div>
          </div>

          <!-- งบประมาณ + เป้าหมาย -->
          <div id="budgetAchievedBlock" style="display:none; margin-top:14px; display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div style="background:#f8fafc; padding:12px 16px; border-radius:8px; border:1px solid #e2e8f0;">
              <div style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">งบประมาณที่ใช้จริง</div>
              <div id="detailBudgetUsed" style="font-size:16px; font-weight:700; color:#1e293b;">—</div>
            </div>
            <div id="achievedBox" style="padding:12px 16px; border-radius:8px; border:1px solid #e2e8f0;">
              <div style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">เป้าหมาย</div>
              <div id="detailAchieved" style="font-size:14px; font-weight:600;">—</div>
            </div>
          </div>

          <!-- เหตุผลที่ไม่บรรลุ -->
          <div id="notAchievedBlock" style="display:none; margin-top:12px;">
            <div style="font-size:12px; font-weight:600; color:#64748b; margin-bottom:4px; text-transform:uppercase; letter-spacing:.5px;">เหตุผลที่ไม่บรรลุเป้าหมาย</div>
            <div class="section-content" id="detailNotAchievedDetail" style="background:#fff7ed; border-left:3px solid #f97316; padding:10px 14px; border-radius:0 6px 6px 0;">—</div>
          </div>



        </div>
      </div>
      <!-- ══ จบส่วนผลการดำเนินงาน ══ -->

      <hr class="modal-divider" />
      <div class="detail-section">
        <div class="section-title">👥 ผู้ร่วมงาน</div>
        <div id="detailCollaborators" class="collaborators-list">—</div>
      </div>
    </div>
    <div class="modal-footer" id="modalFooter">
      <button class="btn-secondary" id="btnSecondaryCloseDetail">ปิด</button>
    </div>
  </div>
</div>

{{-- Modal: เห็นชอบ/รับทราบ/ไม่อนุมัติ --}}
<div class="modal-overlay" id="approvalModal">
  <div class="modal-container" style="max-width: 600px;">
    <div class="modal-header">
      <div>
        <h2 style="margin: 0; font-size: 20px; font-weight: 700; color: #1e293b;">
          <span id="approvalAction">เห็นชอบ</span>กิจกรรม
        </h2>
        <p id="approvalActivityName" style="margin: 4px 0 0 0; font-size: 14px; color: #64748b;"></p>
      </div>
      <button class="btn-close-modal" id="btnCloseApprovalModal">✕</button>
    </div>
    <div class="modal-body">
      <div style="margin-bottom: 20px;">
        <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #334155;">
          💬 หมายเหตุ <span style="color: #94a3b8; font-weight: 400;">(ไม่บังคับ)</span>
        </label>
        <textarea id="approvalNote" rows="4"
          style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; resize: vertical;"
          placeholder="ระบุหมายเหตุหรือข้อเสนอแนะ..."></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-secondary" id="btnSecondaryCloseApproval">ยกเลิก</button>
      <button id="confirmApprovalBtn" class="btn-primary">ยืนยัน</button>
    </div>
  </div>
</div>

<div class="kaizen-toast" id="toast">
  <span class="toast-icon"></span>
  <span id="toast-msg">ดำเนินการสำเร็จ</span>
</div>

<script>
const activitiesData = @json($activitiesData);

const statusLabels = {
  draft: 'ฉบับร่าง',
  pending: 'รอพิจารณา (รอบที่ 1)',
  in_progress: 'รอดำเนินการ',
  waiting_for_manager_result_approval: 'รอหัวหน้าอนุมัติผล (รอบที่ 2)',
  waiting_for_chairman_approval: 'รอประธานอนุมัติ',
  completed: 'เสร็จสิ้น',
  rejected: 'ไม่อนุมัติ'
};

const pendingStatusOrder = [
  { status: 'waiting_for_chairman_approval', icon: '👑' },
  { status: 'waiting_for_manager_result_approval', icon: '📋' },
  { status: 'pending', icon: '🕐' },
];

const typeLabels = {
  increase_revenue: 'เพิ่มรายได้',
  reduce_expenses: 'ลดรายจ่าย',
  reduce_steps: 'ลดขั้นตอน',
  reduce_time: 'ลดเวลาการทำงาน',
  improve_quality: 'ปรับปรุงคุณภาพ',
  reduce_risk: 'ลดความเสี่ยง',
  maintain_image: 'รักษาภาพลักษณ์/ชื่อเสียงองค์กร',
  innovation: 'สิ่งประดิษฐ์/นวัตกรรม',
  new_service: 'เปิดบริการใหม่',
  others: 'อื่นๆ'
};

// สถานะที่มีรายงานผลแนบ
const REPORT_STATUSES = [
  'in_progress',
  'waiting_for_manager_result_approval',
  'waiting_for_chairman_approval',
  'completed'
];

// ── กำหนดคำบนปุ่มตามสถานะของกิจกรรม ──
// pending          → รอบที่ 1  → "เห็นชอบ"
// waiting_for_manager_result_approval → รอบที่ 2 → "รับทราบ"
// waiting_for_chairman_approval       → ประธาน  → "รับทราบ"
function getApproveLabel(status) {
  if (status === 'pending') return 'เห็นชอบ';
  if (status === 'waiting_for_manager_result_approval') return 'รับทราบ';
  if (status === 'waiting_for_chairman_approval') return 'รับทราบ';
  return 'อนุมัติ';
}

let currentApprovalActivity = null;
let currentApprovalAction = null;
let filteredHistory = [];
let allPendingActivities = [];
let activeStatusFilter = 'all';

document.addEventListener('DOMContentLoaded', function() {
  renderPendingActivities();
  renderHistory();

  // Delegation on Pending List
  const pendingList = document.getElementById('pendingList');
  if (pendingList) {
    pendingList.addEventListener('click', function(e) {
      const btnViewDetail = e.target.closest('[data-action="view-detail"]');
      if (btnViewDetail) showDetail(parseInt(btnViewDetail.getAttribute('data-id'), 10));

      const btnApprove = e.target.closest('[data-action="approve"]');
      if (btnApprove) openApprovalModal(parseInt(btnApprove.getAttribute('data-id'), 10), 'approve');

      const btnReject = e.target.closest('[data-action="reject"]');
      if (btnReject) openApprovalModal(parseInt(btnReject.getAttribute('data-id'), 10), 'reject');
    });
  }

  // Delegation on History Table
  const historyTableBody = document.getElementById('historyTableBody');
  if (historyTableBody) {
    historyTableBody.addEventListener('click', function(e) {
      const btnViewDetail = e.target.closest('[data-action="view-detail"]');
      if (btnViewDetail) showDetail(parseInt(btnViewDetail.getAttribute('data-id'), 10));
    });
  }

  // Bind Confirm Action
  const btnConfirmApproval = document.getElementById('confirmApprovalBtn');
  if (btnConfirmApproval) {
    btnConfirmApproval.addEventListener('click', confirmApproval);
  }

  // Bind Modal Close Actions
  const detailModal = document.getElementById('detailModal');
  if (detailModal) {
    detailModal.addEventListener('click', function(e) {
      if (e.target === this) closeDetailModal();
    });
  }
  const btnCloseDetailModal = document.getElementById('btnCloseDetailModal');
  if (btnCloseDetailModal) btnCloseDetailModal.addEventListener('click', closeDetailModal);
  const btnSecondaryCloseDetail = document.getElementById('btnSecondaryCloseDetail');
  if (btnSecondaryCloseDetail) btnSecondaryCloseDetail.addEventListener('click', closeDetailModal);

  const approvalModal = document.getElementById('approvalModal');
  if (approvalModal) {
    approvalModal.addEventListener('click', function(e) {
      if (e.target === this) closeApprovalModal();
    });
  }
  const btnCloseApprovalModal = document.getElementById('btnCloseApprovalModal');
  if (btnCloseApprovalModal) btnCloseApprovalModal.addEventListener('click', closeApprovalModal);
  const btnSecondaryCloseApproval = document.getElementById('btnSecondaryCloseApproval');
  if (btnSecondaryCloseApproval) btnSecondaryCloseApproval.addEventListener('click', closeApprovalModal);
});

function switchTab(tab) {
  document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
  event.target.classList.add('active');
  document.querySelectorAll('.tab-content').forEach(content => content.style.display = 'none');
  document.getElementById(tab === 'pending' ? 'pendingTab' : 'historyTab').style.display = 'block';
}

// ---- STATUS FILTER PILLS ----

function setStatusFilter(status, btn) {
  activeStatusFilter = status;
  document.querySelectorAll('.status-filter-btn').forEach(b => {
    b.style.background = 'white';
    b.style.color = '#475569';
    b.style.borderColor = '#e2e8f0';
  });
  btn.style.background = '#3b82f6';
  btn.style.color = 'white';
  btn.style.borderColor = '#3b82f6';
  filterPending();
}

// ---- PENDING TAB ----

function renderPendingActivities() {
  const userRole = "{{ auth()->user()->role->role_name }}";

  allPendingActivities = activitiesData.filter(a => {
    if (userRole === 'manager') return a.status === 'pending' || a.status === 'waiting_for_manager_result_approval';
    if (userRole === 'chairman') return a.status === 'waiting_for_chairman_approval';
    if (userRole === 'admin') return ['pending', 'waiting_for_manager_result_approval', 'waiting_for_chairman_approval'].includes(a.status);
    return false;
  });

  document.getElementById('pendingCount').textContent = allPendingActivities.length;
  renderPendingList(allPendingActivities);
}

function filterPending() {
  const searchTerm = document.getElementById('pendingSearch').value.toLowerCase().trim();
  const typeFilter = document.getElementById('pendingTypeFilter').value;

  const filtered = allPendingActivities.filter(activity => {
    const matchSearch = !searchTerm ||
      activity.name.toLowerCase().includes(searchTerm) ||
      activity.submitter.toLowerCase().includes(searchTerm);
    const matchType = !typeFilter || (activity.types && activity.types.includes(typeFilter));
    const matchStatus = activeStatusFilter === 'all' || activity.status === activeStatusFilter;
    return matchSearch && matchType && matchStatus;
  });

  renderPendingList(filtered);
}

function renderPendingList(pendingActivities) {
  const container = document.getElementById('pendingList');
  const emptyState = document.getElementById('emptyPending');

  if (pendingActivities.length === 0) {
    container.innerHTML = '';
    emptyState.style.display = 'block';
    return;
  }

  emptyState.style.display = 'none';

  const statusesToShow = activeStatusFilter === 'all'
    ? pendingStatusOrder
    : pendingStatusOrder.filter(s => s.status === activeStatusFilter);

  let html = '';

  statusesToShow.forEach(({ status, icon }) => {
    const group = pendingActivities.filter(a => a.status === status);
    if (group.length === 0) return;

    html += `
      <div style="margin-bottom: 36px;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid #e2e8f0;">
          <span style="font-size: 20px;">${icon}</span>
          <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #1e293b;">${statusLabels[status]}</h3>
          <span style="background: #f1f5f9; color: #64748b; padding: 2px 10px; border-radius: 20px; font-size: 13px; font-weight: 600;">${group.length} รายการ</span>
        </div>
        ${group.map(activity => {
          const approveLabel = getApproveLabel(activity.status);
          return `
          <div class="approval-card">
            <div class="approval-header">
              <div class="approval-info">
                <h3>${activity.name}</h3>
              </div>
            </div>
            <div class="approval-meta">
              <div class="meta-item">
                <div class="meta-label">ผู้ยื่น</div>
                <div class="meta-value">${activity.submitter}</div>
              </div>
              <div class="meta-item">
                <div class="meta-label">ประเภท</div>
                <div class="meta-value">
                  ${activity.types.map(type => `<span class="type-tag">${typeLabels[type] || type}</span>`).join(' ')}
                </div>
              </div>
            </div>
            <div class="approval-sections">
              <div class="section-box problem">
                <div class="section-box-title">❗ปัญหา</div>
                <div class="section-box-content">${activity.problem}</div>
              </div>
              <div class="section-box solution">
                <div class="section-box-title">💡วิธีการ</div>
                <div class="section-box-content">${activity.solution}</div>
              </div>
              <div class="section-box result">
                <div class="section-box-title">🎯ผลที่คาดว่าจะได้รับ</div>
                <div class="section-box-content">${activity.result}</div>
              </div>
            </div>
            <div class="approval-actions">
              <button class="btn-view-detail" data-action="view-detail" data-id="${activity.id}">👁️ ดูรายละเอียด</button>
              @if(auth()->user()->role->role_name !== 'admin')
              <button class="btn-approve" data-action="approve" data-id="${activity.id}">✅ ${approveLabel}</button>
              <button class="btn-reject" data-action="reject" data-id="${activity.id}">❌ ไม่อนุมัติ</button>
              @endif
            </div>
          </div>
        `}).join('')}
      </div>
    `;
  });

  container.innerHTML = html;
}

// ---- HISTORY TAB ----

function renderHistory() {
  filteredHistory = activitiesData.filter(a => ['completed', 'rejected'].includes(a.status));
  updateHistoryTable();
}

function updateHistoryTable() {
  const tbody = document.getElementById('historyTableBody');
  const emptyState = document.getElementById('emptyHistory');
  const countElement = document.getElementById('historyCount');

  countElement.textContent = filteredHistory.length;

  if (filteredHistory.length === 0) {
    tbody.innerHTML = '';
    emptyState.style.display = 'block';
    return;
  }

  emptyState.style.display = 'none';

  tbody.innerHTML = filteredHistory.map(activity => `
    <tr>
      <td style="font-weight: 600; color: #3b82f6;">${activity.name}</td>
      <td>${activity.submitter}</td>
      <td>
        <span class="status-badge status-${activity.status}">
          <span class="dot"></span>${statusLabels[activity.status]}
        </span>
      </td>
      <td>${activity.approvalDate || '—'}</td>
      <td>
        <div style="display: flex; gap: 4px;">
          <button class="btn-action primary" data-action="view-detail" data-id="${activity.id}" title="ดูรายละเอียด">👁️</button>
          ${['completed', 'rejected', 'in_progress', 'waiting_for_manager_result_approval', 'waiting_for_chairman_approval'].includes(activity.status) ?
            `<a href="/activities/${activity.id}/report" class="btn-action warning" title="ดูรายงานผล" style="background:#f59e0b; color:white; text-decoration:none; padding:4px 8px; border-radius:6px; font-size:12px; display:inline-flex; align-items:center;">📝 </a>`
            : ''}
        </div>
      </td>
    </tr>
  `).join('');
}

function filterHistory() {
  const searchTerm = document.getElementById('historySearch').value.toLowerCase().trim();
  const statusFilter = document.getElementById('historyStatusFilter').value;

  filteredHistory = activitiesData
    .filter(a => ['completed', 'rejected'].includes(a.status))
    .filter(activity => {
      const matchSearch = !searchTerm ||
        activity.name.toLowerCase().includes(searchTerm) ||
        activity.submitter.toLowerCase().includes(searchTerm);
      const matchStatus = !statusFilter || activity.status === statusFilter;
      return matchSearch && matchStatus;
    });

  updateHistoryTable();
}

// ---- MODALS ----

function showDetail(id) {
  const activity = activitiesData.find(a => a.id === id);
  if (!activity) return;

  document.getElementById('modalTitle').textContent = activity.name;
  document.getElementById('modalStatus').className = `status-badge status-${activity.status}`;
  document.getElementById('modalStatus').innerHTML = `<span class="dot"></span>${statusLabels[activity.status]}`;
  document.getElementById('detailActivityName').textContent = activity.name;
  document.getElementById('detailTypes').innerHTML = activity.types.map(type =>
    `<span class="type-tag">${typeLabels[type] || type}</span>`
  ).join('');
  document.getElementById('detailSubmitter').textContent = activity.submitter;
  document.getElementById('detailSubmitDate').textContent = activity.submitDate;
  /* ── ข้อความ 3 ส่วน ── */
  document.getElementById('detailProblem').textContent  = activity.problem  || '—';
  document.getElementById('detailSolution').textContent = activity.solution || '—';
  document.getElementById('detailResult').textContent   = activity.result   || '—';

  /* ── ส่วนที่ 1-3: รูปภาพ ── */
  renderImages('problemImages',  activity.problem_images);
  renderImages('solutionImages', activity.solution_images);
  renderImages('resultImages',   activity.result_images);

  /* ── ส่วนที่ 4: รูปภาพประกอบผลงาน (actual) — แสดงเสมอ ── */
  const actualGallery = document.getElementById('actualImages');
  const actualEmpty   = document.getElementById('actualImagesEmpty');
  const actualImgs    = activity.actual_images || [];
  if (actualImgs.length > 0) {
    actualEmpty.style.display = 'none';
    actualGallery.innerHTML = actualImgs.map(img =>
      `<a href="${img.url}" target="_blank">
         <img src="${img.url}" title="${img.name}"
              style="width:80px; height:80px; object-fit:cover; border-radius:6px; margin-right:5px; cursor:pointer;">
       </a>`
    ).join('');
  } else {
    actualEmpty.style.display = 'inline';
    // ลบรูปเก่าออก เหลือแค่ข้อความ empty
    const imgs = actualGallery.querySelectorAll('a');
    imgs.forEach(el => el.remove());
  }

  /* ══ ส่วนผลการดำเนินงาน ══ */
  const reportSection  = document.getElementById('reportSection');
  const hasReport      = REPORT_STATUSES.includes(activity.status);

  if (hasReport) {
    reportSection.style.display = 'block';

    // ผลลัพธ์ที่เกิดขึ้นจริง
    const actualResultBlock = document.getElementById('actualResultBlock');
    if (activity.actual_result) {
      actualResultBlock.style.display = 'block';
      document.getElementById('detailActualResult').textContent = activity.actual_result;
    } else {
      actualResultBlock.style.display = 'none';
    }

    // รายละเอียดการดำเนินงานเพิ่มเติม
    const perfBlock = document.getElementById('performanceDetailBlock');
    if (activity.performance_detail) {
      perfBlock.style.display = 'block';
      document.getElementById('detailPerformanceDetail').textContent = activity.performance_detail;
    } else {
      perfBlock.style.display = 'none';
    }

    // ตัวชี้วัด
    const indicators = activity.indicators || [];
    const indBlock   = document.getElementById('indicatorsBlock');
    if (indicators.length > 0) {
      indBlock.style.display = 'block';
      document.getElementById('detailIndicatorRows').innerHTML = indicators.map(ind => {
        const before = parseFloat(ind.before_value);
        const after  = parseFloat(ind.after_value);
        let diffHtml = '<span style="color:#94a3b8;">—</span>';
        if (!isNaN(before) && !isNaN(after) && before !== 0) {
          const diff   = ((after - before) / Math.abs(before)) * 100;
          const color  = diff > 0 ? '#10b981' : diff < 0 ? '#ef4444' : '#64748b';
          const prefix = diff > 0 ? '+' : '';
          diffHtml = `<span style="color:${color}; font-weight:700;">${prefix}${diff.toFixed(1)}%</span>`;
        }
        return `
          <div style="display:grid; grid-template-columns:2.2fr 1fr 1fr 1.2fr 1fr; gap:8px; align-items:center;
                      padding:10px 14px; border-bottom:1px solid #f1f5f9; font-size:13px;">
            <div style="font-weight:500; color:#334155;">${ind.indicator_name || '—'}</div>
            <div style="text-align:center; color:#475569;">${ind.before_value ?? '—'}</div>
            <div style="text-align:center; color:#475569;">${ind.after_value ?? '—'}</div>
            <div style="text-align:center;">${diffHtml}</div>
            <div style="text-align:center; color:#64748b; font-size:12px;">${ind.unit || '—'}</div>
          </div>`;
      }).join('');
    } else {
      indBlock.style.display = 'none';
    }

    // งบประมาณ + เป้าหมาย
    const budgetBlock     = document.getElementById('budgetAchievedBlock');
    const hasAchievedData = activity.is_achieved !== null && activity.is_achieved !== undefined;
    if (activity.status === 'completed' && (activity.budget_used || hasAchievedData)) {
      budgetBlock.style.display = 'grid';
      document.getElementById('detailBudgetUsed').textContent =
        activity.budget_used ? Number(activity.budget_used).toLocaleString('th-TH') + ' บาท' : '—';

      const achievedBox    = document.getElementById('achievedBox');
      const detailAchieved = document.getElementById('detailAchieved');
      if (activity.is_achieved == 1) {
        achievedBox.style.background  = '#f0fdf4';
        achievedBox.style.borderColor = '#86efac';
        detailAchieved.innerHTML = '<span style="color:#16a34a;">✅ บรรลุเป้าหมาย</span>';
      } else if (activity.is_achieved == 0) {
        achievedBox.style.background  = '#fff7ed';
        achievedBox.style.borderColor = '#fdba74';
        detailAchieved.innerHTML = '<span style="color:#ea580c;">⚠️ ไม่บรรลุเป้าหมาย</span>';
      } else {
        achievedBox.style.background  = '#f8fafc';
        achievedBox.style.borderColor = '#e2e8f0';
        detailAchieved.textContent = '—';
      }
    } else {
      budgetBlock.style.display = 'none';
    }

    // เหตุผลที่ไม่บรรลุ
    const notAchievedBlock = document.getElementById('notAchievedBlock');
    if (activity.status === 'completed' && activity.is_achieved == 0 && activity.not_achieved_detail) {
      notAchievedBlock.style.display = 'block';
      document.getElementById('detailNotAchievedDetail').textContent = activity.not_achieved_detail;
    } else {
      notAchievedBlock.style.display = 'none';
    }



  } else {
    reportSection.style.display = 'none';
  }

  document.getElementById('detailCollaborators').innerHTML = activity.collaborators.map(collab => `
    <div class="collaborator-card">
      <div class="collaborator-info">
        <div class="collaborator-avatar">${collab.name.charAt(0)}</div>
        <div class="collaborator-name">${collab.name}</div>
      </div>
      <div class="collaborator-percent">${collab.percent}%</div>
    </div>
  `).join('');

  document.getElementById('detailModal').classList.add('show');
  document.body.style.overflow = 'hidden';
}

/* helper: render รูปภาพใส่ container */
function renderImages(containerId, images) {
  const container = document.getElementById(containerId);
  if (images && images.length > 0) {
    container.innerHTML = images.map(img =>
      `<a href="${img.url}" target="_blank">
         <img src="${img.url}" title="${img.name}"
              style="width:80px; height:80px; object-fit:cover; border-radius:6px; margin-right:5px; cursor:pointer;">
       </a>`
    ).join('');
  } else {
    container.innerHTML = '';
  }
}

function closeDetailModal() {
  const modal = document.getElementById('detailModal');
  if (modal) {
    modal.classList.remove('show');
    // Reset inputs / state
    document.getElementById('modalTitle').textContent = '';
    document.getElementById('detailProblem').textContent = '';
    document.getElementById('detailSolution').textContent = '';
    document.getElementById('detailResult').textContent = '';
    document.getElementById('problemImages').innerHTML = '';
    document.getElementById('solutionImages').innerHTML = '';
    document.getElementById('resultImages').innerHTML = '';
    const actualEmpty = document.getElementById('actualImagesEmpty');
    if(actualEmpty) actualEmpty.style.display = 'inline';
    const actualImgs = document.getElementById('actualImages');
    if (actualImgs) {
        const imgs = actualImgs.querySelectorAll('a');
        imgs.forEach(el => el.remove());
    }
    document.getElementById('detailCollaborators').innerHTML = '';
  }
  document.body.style.overflow = '';
}

function openApprovalModal(activityId, action) {
  const activity = activitiesData.find(a => a.id === activityId);
  if (!activity) return;

  currentApprovalActivity = activity;
  currentApprovalAction = action;

  // กำหนดข้อความบน modal ตามรอบ / role
  let actionText;
  if (action === 'approve') {
    actionText = getApproveLabel(activity.status);
  } else {
    actionText = 'ไม่อนุมัติ';
  }

  document.getElementById('approvalAction').textContent = actionText;
  document.getElementById('approvalActivityName').textContent = activity.name;
  document.getElementById('approvalNote').value = '';

  const confirmBtn = document.getElementById('confirmApprovalBtn');
  confirmBtn.textContent = `ยืนยัน${actionText}`;
  confirmBtn.className = action === 'approve' ? 'btn-approve' : 'btn-reject';

  document.getElementById('approvalModal').classList.add('show');
  document.body.style.overflow = 'hidden';
}

function closeApprovalModal() {
  const modal = document.getElementById('approvalModal');
  if (modal) {
    modal.classList.remove('show');
    document.getElementById('approvalNote').value = '';
    document.getElementById('approvalAction').textContent = '';
    document.getElementById('approvalActivityName').textContent = '';
  }
  document.body.style.overflow = '';
  currentApprovalActivity = null;
  currentApprovalAction = null;
}

function confirmApproval() {
  if (!currentApprovalActivity || !currentApprovalAction) return;

  const note = document.getElementById('approvalNote').value.trim();
  const newStatus = currentApprovalAction === 'approve' ? 'approved' : 'rejected';

  // เก็บ action และ status ไว้ก่อน เพราะ closeApprovalModal() จะ reset เป็น null
  const action = currentApprovalAction;
  const activityStatus = currentApprovalActivity.status;

  fetch(`/activities/${currentApprovalActivity.id}/update-status`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
    },
    body: JSON.stringify({ status: newStatus, note: note })
  })
  .then(async response => {
    const isJson = response.headers.get('content-type')?.includes('application/json');
    const data = isJson ? await response.json() : null;
    if (!response.ok) throw new Error((data && data.message) || response.statusText);
    return data;
  })
  .then(data => {
    if (data && data.success) {
      const activity = activitiesData.find(a => a.id === currentApprovalActivity.id);
      if (activity) {
        activity.status = newStatus;
        activity.approvalDate = new Date().toLocaleDateString('th-TH');
        activity.approvalNote = note;
      }

      closeApprovalModal();
      renderPendingActivities();
      renderHistory();

      if (action === 'approve') {
        const label = getApproveLabel(activityStatus);
        showToast(`✅ ${label}กิจกรรมเรียบร้อยแล้ว`, 'success');
      } else {
        showToast('↩️ ส่งกลับเรียบร้อยแล้ว', 'error');
      }
    } else {
      showToast('❌ เกิดข้อผิดพลาด: ' + ((data && data.message) || 'ได้รับข้อมูลที่ไม่ถูกต้องจาก Server'), 'error');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showToast('❌ เกิดข้อผิดพลาดในการเชื่อมต่อ กรุณา Refresh หน้าเว็บ', 'error');
  });
}

// ---- TOAST ----
function showToast(msg, type = 'success') {
  const toast = document.getElementById('toast');
  const msgEl = document.getElementById('toast-msg');
  if (!toast || !msgEl) return;

  msgEl.textContent = msg;
  toast.style.backgroundColor = type === 'success' ? '#10b981' : '#ef4444';

  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 3000);
}
</script>

@endsection